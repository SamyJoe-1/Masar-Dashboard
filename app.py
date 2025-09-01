import streamlit as st
import pandas as pd
import io
import concurrent.futures
import re
from processor import ResumeProcessor
from matcher import JobMatcher
from streamlit_extras.add_vertical_space import add_vertical_space

# 📌 إعداد الصفحة
st.set_page_config(page_title="Resume ATS UPDATE", layout="wide")

# 🗂️ حالة الجلسة
if "stage" not in st.session_state:
    st.session_state.stage = "upload"
if "files" not in st.session_state:
    st.session_state.files = []
if "job_text" not in st.session_state:
    st.session_state.job_text = ""
if "top_n" not in st.session_state:
    st.session_state.top_n = 5
if "matcher" not in st.session_state:
    st.session_state.matcher = None
if "processor" not in st.session_state:
    st.session_state.processor = ResumeProcessor()  # تحميل مرة واحدة

# 🎯 دالة استخراج المسمى الوظيفي من الوصف
def extract_job_title(description: str) -> str:
    text = description.strip()

    # تنظيف النص من مقدمات أو جمل غير مهمة
    remove_phrases = [
        r"(?i)about the job",
        r"(?i)we are looking for",
        r"(?i)is looking for",
        r"(?i)we are hiring",
        r"(?i)company is seeking",
        r"(?i)join our team as",
        r"(?i)position summary"
    ]
    for phrase in remove_phrases:
        text = re.sub(phrase, "", text)

    text = text.strip()

    # البحث عن المسمى الوظيفي بصيغ شائعة
    patterns = [
        r"(?i)job title\s*:\s*([A-Z][A-Za-z\s/&-]{2,50})",
        r"(?i)position\s*:\s*([A-Z][A-Za-z\s/&-]{2,50})",
        r"(?i)as an?\s+([A-Z][A-Za-z\s/&-]{2,50})",
        r"(?i)is hiring for an?\s+([A-Z][A-Za-z\s/&-]{2,50})",
        r"(?i)is seeking an?\s+([A-Z][A-Za-z\s/&-]{2,50})",
        r"(?i)to join our team as an?\s+([A-Z][A-Za-z\s/&-]{2,50})",
        r"\b(Junior|Senior|Lead|Head)?\s*([A-Z][A-Za-z\s]+(?:Engineer|Manager|Analyst|Developer|Specialist|Designer|Consultant|Scientist|Officer))"
    ]

    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            title = " ".join([g for g in match.groups() if g])
            title = title.strip()
            title = re.split(r"[.\n]", title)[0]
            return title

    # fallback → أول جملة قصيرة
    first_line = text.split("\n")[0]
    if len(first_line) > 60:
        first_line = first_line[:60]
    return first_line.strip()

# ⚡ دالة معالجة ملف واحد
def process_single_file(uploaded_file, matcher, processor):
    file_bytes = uploaded_file.read()
    try:
        result = processor.process_pdf_bytes(file_bytes, file_name=uploaded_file.name)
        if result:
            score, matched_skills, suggestions, extra_info = matcher.match_resume(result)
            yoe = extra_info.get("yoe") or result.yoe or 0
            category = "Junior" if float(yoe) < 2 else "Mid" if float(yoe) < 6 else "Senior"

            return {
                "Name": result.name or "-",
                "File": uploaded_file.name,
                "Score": score,
                "YOE": yoe,
                "Matched Skills": matched_skills,
                "Extra Skills": extra_info.get("extra_skills", []),
                "Certificates": extra_info.get("certificates", []),
                "Projects": extra_info.get("projects", []),
                "Suggested Roles": suggestions,  # الوظائف المقترحة
                "Final Decision": "✅ مقبول" if score >= 50 else "❌ مرفوض",
                "Explanation": extra_info.get("verdict", ""),
                "Category": category,
                "Email": result.email or "-",
                "Phone": result.contact_number or result.phone or "-",
                "Job Title": result.job_title or "-",
                "Location": result.location or "-",
                "Experience Summary": result.summary or "-",
                "Graduation": f"{extra_info.get('graduation_status', '-')} - {extra_info.get('degree', '-')} ({extra_info.get('graduation_year', '-')})",
                "Last Job": extra_info.get("last_job", "-"),
            }
        else:
            return ("reject", uploaded_file.name, "⚠️ السيرة الذاتية فارغة أو غير قابلة للقراءة.")
    except Exception as e:
        return ("reject", uploaded_file.name, str(e))

# 🏠 واجهة الرفع
st.title("📄 نظام تقييم السير الذاتية - وزارة العمل العمانية")
st.caption("تم التطوير بواسطة شركة تقنيات النجاح")

if st.session_state.stage == "upload":
    uploaded_files = st.file_uploader(
        "⬆️ قم بتحميل السير الذاتية (PDF فقط)",
        type=["pdf"],
        accept_multiple_files=True
    )
    st.markdown("---")
    job_text = st.text_area("📝 أدخل وصف الوظيفة هنا", height=250)
    top_n = st.number_input("🔢 عدد المرشحين الأعلى لإظهارهم:", min_value=1, max_value=100, value=5)

    if uploaded_files and job_text:
        st.session_state.files = uploaded_files
        st.session_state.job_text = job_text
        st.session_state.top_n = top_n
        st.session_state.matcher = JobMatcher(job_text)
        st.session_state.stage = "results"
        st.rerun()

# 📊 عرض النتائج
elif st.session_state.stage == "results":
    st.subheader("📊 نتائج التقييم")

    matcher = st.session_state.matcher
    processor = st.session_state.processor

    # عرض المسمى الوظيفي من الوصف
    job_title_from_desc = extract_job_title(st.session_state.job_text)
    st.markdown(f"### 🏷️ المسمى الوظيفي: **{job_title_from_desc}**")

    results = []
    rejected = []

    # المعالجة المتوازية لسرعة عالية
    with concurrent.futures.ThreadPoolExecutor(max_workers=8) as executor:
        futures = {executor.submit(process_single_file, f, matcher, processor): f for f in st.session_state.files}
        for future in concurrent.futures.as_completed(futures):
            result = future.result()
            if isinstance(result, tuple) and result[0] == "reject":
                rejected.append((result[1], result[2]))
            else:
                results.append(result)

    # ترتيب النتائج
    df_all = pd.DataFrame(results)
    if "Score" in df_all.columns:
        df_all = df_all.sort_values(by="Score", ascending=False)

    # عرض تفاصيل المرشحين
    if not df_all.empty:
        st.markdown("## 👥 تفاصيل المرشحين")
        for candidate in results:
            with st.expander(f"📌 {candidate['Name']} - {candidate['Category']}"):
                col1, col2 = st.columns([1, 1])
                with col1:
                    st.markdown(f"**🗂️ اسم الملف:** `{candidate['File']}`")
                    st.markdown(f"**🏷️ الوظيفة المقترحة:** {', '.join(candidate['Suggested Roles']) if candidate['Suggested Roles'] else '-'}")
                    st.markdown(f"**📍 الموقع:** {candidate['Location']}")
                    st.markdown(f"**📧 البريد الإلكتروني:** {candidate['Email']}")
                    st.markdown(f"**📱 الهاتف:** {candidate['Phone']}")
                    st.markdown(f"**🎓 الخبرة (سنوات):** {candidate['YOE']}")
                    st.markdown(f"**🎓 التعليم:** {candidate['Graduation']}")
                    st.markdown(f"**💼 آخر وظيفة:** {candidate['Last Job']}")
                    st.markdown(f"**🔢 الدرجة:** `{candidate['Score']}`")
                    st.markdown(f"**🏁 القرار النهائي:** {candidate['Final Decision']}")
                with col2:
                    st.markdown("✅ **المهارات المطابقة:**")
                    st.success(", ".join(candidate["Matched Skills"]) or "-")
                    st.markdown("🛠️ **المهارات الإضافية:**")
                    st.info(", ".join(candidate["Extra Skills"]) or "-")
                    st.markdown("📂 **الشهادات:**")
                    st.info(", ".join(candidate["Certificates"]) or "-")
                    st.markdown("💡 **المشاريع:**")
                    st.code("\n".join(candidate["Projects"]) or "-", language="markdown")

                st.markdown("🧠 **الشرح والتحليل:**")
                st.info(candidate["Explanation"] or "-")
                st.markdown("🧾 **ملخص الخبرات:**")
                st.write(candidate["Experience Summary"])

        # تحميل النتائج Excel
        output = io.BytesIO()
        pd.DataFrame(results).to_excel(output, index=False, engine="openpyxl")
        output.seek(0)
        st.download_button("⬇️ تحميل النتائج بصيغة Excel", output, "results.xlsx")

    # عرض الملفات المرفوضة
    if rejected:
        st.markdown("## ⚠️ ملفات لم تتم معالجتها:")
        for filename, reason in rejected:
            st.warning(f"{filename}: {reason}")

    # إعادة التشغيل
    add_vertical_space(1)
    if st.button("🔁 بدء من جديد"):
        st.session_state.stage = "upload"
        st.session_state.files = []
        st.session_state.matcher = None
        st.rerun()
