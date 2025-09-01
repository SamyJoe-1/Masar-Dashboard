import io
import re
import time
import traceback
import requests
from typing import List, Optional, Union, Dict, Any
from concurrent.futures import ThreadPoolExecutor, as_completed
from fastapi import FastAPI, UploadFile, File, Form, Body
from fastapi.responses import JSONResponse, StreamingResponse
from pydantic import BaseModel, Field
import os
import sys

from processor import ResumeProcessor
from matcher import JobMatcher

app = FastAPI(title="ATS Resume Matcher + Job Title Extractor", version="1.0.0")

processor = ResumeProcessor()
HTTP_TIMEOUT = 25
MAX_WORKERS = 8
UA = {"User-Agent": "ResumeATS/1.0 (+FastAPI)"}

class URLsPayload(BaseModel):
    job_description: str
    urls: Union[List[str], str]
    output_format: Optional[str] = "json"
    debug: Optional[bool] = False

# ------------------------------
# موديل للـ ai-job-title
# ------------------------------
class JDRequest(BaseModel):
    job_description: str
    debug: Optional[bool] = False

# ------------------------------
# تحميل ملف PDF من URL
# ------------------------------
def fetch_url(url: str) -> bytes:
    r = requests.get(url, headers=UA, timeout=HTTP_TIMEOUT, stream=True)
    r.raise_for_status()
    buf = io.BytesIO()
    for chunk in r.iter_content(65536):
        if chunk:
            buf.write(chunk)
            if buf.tell() > 50 * 1024 * 1024:
                break
    return buf.getvalue()

# ------------------------------
# تصنيف الخبرة
# ------------------------------
def categorize_by_yoe(yoe: Any, cv_job_title: Optional[str]) -> str:
    try:
        y = float(yoe or 0)
    except:
        y = 0.0
    if y < 2:
        return "Junior"
    if y < 6:
        return "Mid"
    if cv_job_title and any(k in cv_job_title.lower() for k in ["manager", "consultant"]):
        return "Manager/Consultant"
    return "Senior"

# ------------------------------
# معالجة ملف PDF واحد
# ------------------------------
def process_pdf_bytes(file_bytes: bytes, file_name: str, matcher: JobMatcher) -> Dict[str, Any]:
    t0 = time.time()
    try:
        resume = processor.process_pdf_bytes(file_bytes, file_name=file_name)
        if not resume:
            raise ValueError("Resume processing failed.")

        score, matched_skills, suggestions, extra_info = matcher.match_resume(resume)
        yoe = extra_info.get("yoe") or getattr(resume, "yoe", 0)
        category = categorize_by_yoe(yoe, getattr(resume, "job_title", None))

        result = {
            "Name": getattr(resume, "name", None) or "-",
            "File": file_name,
            "Score": score,
            "YOE": yoe,
            "Category": category,
            "Matched Skills": matched_skills or [],
            "Extra Skills": extra_info.get("extra_skills", []),
            "Certificates": extra_info.get("certificates", []),
            "Projects": extra_info.get("projects", []),
            "Suggested Roles": suggestions or [],
            "Final Decision": "✅ مقبول" if (score or 0) >= 50 else "❌ مرفوض",
            "Email": getattr(resume, "email", None) or "-",
            "Phone": getattr(resume, "contact_number", None) or getattr(resume, "phone", None) or "-",
            "Job Title": getattr(resume, "job_title", None) or "-",
            "Location": getattr(resume, "location", None) or "-",
            "Experience Summary": getattr(resume, "summary", None) or "-",
            "Graduation": f"{extra_info.get('graduation_status', '-')} - {extra_info.get('degree', '-')} ({extra_info.get('graduation_year', '-')})",
            "Last Job": extra_info.get("last_job", "-"),
        }
        return {"result": result}
    except Exception as e:
        return {"error": f"{file_name}: {e}", "reason": "processing_failed"}
    finally:
        elapsed = int((time.time() - t0) * 1000)

# ------------------------------
# (1) match-cvs
# ------------------------------
@app.post("/match-cvs")
async def match_cvs(
    job_description: str = Form(...),
    files: List[UploadFile] = File(...),
    output_format: str = Form("json")
):
    matcher = JobMatcher(job_description)
    results, errors = [], []

    with ThreadPoolExecutor(max_workers=MAX_WORKERS) as ex:
        futs = {ex.submit(process_pdf_bytes, f.file.read(), f.filename, matcher): f.filename for f in files}
        for fut in as_completed(futs):
            out = fut.result()
            if "result" in out:
                results.append(out["result"])
            else:
                errors.append(out)

    results = sorted(results, key=lambda x: x.get("Score", 0), reverse=True)

    if output_format.lower() == "excel":
        import pandas as pd
        buf = io.BytesIO()
        pd.DataFrame(results).to_excel(buf, index=False, engine="openpyxl")
        buf.seek(0)
        return StreamingResponse(buf, media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                 headers={"Content-Disposition": "attachment; filename=results.xlsx"})

    return JSONResponse({"results": results, "errors": errors})

# ------------------------------
# (2) match-cvs-from-urls
# ------------------------------
@app.post("/match-cvs-from-urls")
async def match_cvs_from_urls(payload: URLsPayload = Body(...)):
    matcher = JobMatcher(payload.job_description)
    urls = [payload.urls] if isinstance(payload.urls, str) else payload.urls
    urls = list(dict.fromkeys([u.strip() for u in urls if u.strip()]))

    results, errors = [], []

    def fetch_and_process(url: str):
        try:
            data = fetch_url(url)
            return process_pdf_bytes(data, url.split("/")[-1] or "remote.pdf", matcher)
        except requests.exceptions.RequestException as e:
            return {"error": f"{url}: download failed ({e})", "reason": "download_failed"}

    with ThreadPoolExecutor(max_workers=MAX_WORKERS) as ex:
        futs = {ex.submit(fetch_and_process, u): u for u in urls}
        for fut in as_completed(futs):
            out = fut.result()
            if "result" in out:
                results.append(out["result"])
            else:
                errors.append(out)

    results = sorted(results, key=lambda x: x.get("Score", 0), reverse=True)

    if payload.output_format.lower() == "excel":
        import pandas as pd
        buf = io.BytesIO()
        pd.DataFrame(results).to_excel(buf, index=False, engine="openpyxl")
        buf.seek(0)
        return StreamingResponse(buf, media_type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                 headers={"Content-Disposition": "attachment; filename=results.xlsx"})

    return JSONResponse({"results": results, "errors": errors})

# ------------------------------
# (3) ai-job-title
# ------------------------------
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY", "")
OPENAI_MODEL = os.getenv("OPENAI_MODEL", "gpt-4o-mini")

try:
    from openai import OpenAI
    _client = OpenAI(api_key=OPENAI_API_KEY) if OPENAI_API_KEY else None
except:
    _client = None

SENIORITY_WORDS = {"senior","jr","sr","junior","middle","mid-level","principal","lead","head","intern","graduate","trainee","entry-level","كبير","سينيور","جونيور","مبتدئ","متدرب","رئيس","قائد"}
ROLE_NORMALIZE_RE = re.compile(r"\s{2,}")

def strip_seniority_company(title: str) -> str:
    t = title.strip()
    t = re.sub(r"(?i)\b(" + "|".join(map(re.escape, SENIORITY_WORDS)) + r")\b", " ", t)
    t = re.split(r"(?i)\b(at|with|في|لدى)\b", t)[0]
    t = t.replace("•", " ").strip(" .,:;\"'“”‘’-/\\|()[]")
    t = ROLE_NORMALIZE_RE.sub(" ", t)
    return t[:80].strip()

def heuristic_fallback(jd: str) -> str:
    first = jd.splitlines()[0] if "\n" in jd else jd
    first = re.split(r"[:\.\-–،؛]", first)[0]
    return strip_seniority_company(first) or "-"

SYS_PROMPT = "You are a precise extractor. Given a job description (English or Arabic), return ONLY the clean job title without seniority or company names."

def llm_infer_title(jd: str) -> str:
    resp = _client.chat.completions.create(
        model=OPENAI_MODEL,
        messages=[{"role": "system", "content": SYS_PROMPT}, {"role": "user", "content": jd}],
        temperature=0,
        max_tokens=24,
    )
    return strip_seniority_company((resp.choices[0].message.content or "").strip())

@app.post("/ai-job-title")
async def ai_job_title(req: JDRequest):
    t0 = time.time()
    try:
        if _client:
            title = llm_infer_title(req.job_description)
            return {"job_title": title, "source": "llm", "elapsed_ms": int((time.time() - t0) * 1000)}
        else:
            raise RuntimeError("OpenAI not configured")
    except:
        fb = heuristic_fallback(req.job_description)
        return {"job_title": fb, "source": "fallback", "elapsed_ms": int((time.time() - t0) * 1000)}
        
@app.get("/")
async def root():
    return {"message": "ATS Resume Matcher API is running!"}

# -------------------------
# Run with uvicorn for FastAPI
# -------------------------
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=5002)