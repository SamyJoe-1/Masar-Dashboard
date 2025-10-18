# backend/main.py
import os
from enum import Enum
from typing import Any, List, Optional, Union, Dict, Tuple

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field

# (اختياري) .env
try:
    from dotenv import load_dotenv
    load_dotenv()
except Exception:
    pass

# -------- OpenAI client (v1) --------
try:
    from openai import OpenAI
except Exception:
    OpenAI = None  # مكتبة غير متاحة

OPENAI_API_KEY = os.getenv("OPENAI_API_KEY", "").strip()
DEFAULT_MODEL = os.getenv("VAI_MODEL", "gpt-4o-mini").strip() or "gpt-4o-mini"


# ================== Schemas ==================
class Section(str, Enum):
    summary = "summary"
    education = "education"
    experience = "experience"
    skills = "skills"
    jd_highlights = "jd_highlights"   # موجود سابقاً
    projects = "projects"             # NEW
    certifications = "certifications" # NEW
    languages = "languages"           # NEW
    achievements = "achievements"     # NEW
    contact = "contact"               # NEW (header normalization)


class CVContext(BaseModel):
    name: Optional[str] = None
    title: Optional[str] = None
    location: Optional[str] = None
    email: Optional[str] = None
    phone: Optional[str] = None
    links: Optional[str] = None
    skills: Optional[List[str]] = None


class ImproveRequest(BaseModel):
    section: Section = Field(..., description="Which section to improve")
    content: Any = Field(..., description="Text or list depending on section")
    job_description: Optional[Union[str, List[str]]] = Field(
        None, description="Job description text, or list of lines (optional)"
    )
    target_role: Optional[str] = Field(None, description="e.g., 'Data Analyst'")
    language: str = Field("en", description="Output language code")
    style: str = Field(
        "ats",
        description="Style: 'ats' (concise/metrics), 'narrative', or 'neutral'",
    )
    cv: Optional[CVContext] = None
    max_items: int = Field(8, ge=1, le=50, description="Max bullets/items returned")
    sort_bullets_by_impact: bool = Field(True, description="Sort bullets by impact (metrics first)")
    return_mode: str = Field("raw", pattern="^(raw|json)$", description="Return 'raw' (default) or 'json'")
    group_skills: bool = Field(False, description="Group skills into technical/tools/soft where possible")
    force_llm: bool = Field(False, description="If true, 503 if LLM disabled instead of fallback")


class ImproveResponse(BaseModel):
    section: Section
    improved: Any
    notes: Optional[str] = None
    matched_keywords: Optional[List[str]] = None
    missing_keywords: Optional[List[str]] = None
    model: str


# طلب وإخراج الإندبوينت المجمّع
class FullCVIn(BaseModel):
    contact: Optional[Dict[str, Any]] = None  # name/title/location/email/phone/links
    summary: Optional[str] = None
    experience: Optional[List[str]] = None
    education: Optional[List[str]] = None
    skills: Optional[List[str]] = None
    projects: Optional[List[str]] = None
    certifications: Optional[List[str]] = None
    languages: Optional[List[str]] = None
    achievements: Optional[List[str]] = None


class ImproveAllRequest(BaseModel):
    cv: FullCVIn
    job_description: Optional[Union[str, List[str]]] = None
    target_role: Optional[str] = None
    language: str = "en"
    style: str = "ats"
    max_items: int = 8
    return_mode: str = Field("json", pattern="^(raw|json)$")
    group_skills: bool = False
    sort_bullets_by_impact: bool = True
    force_llm: bool = False


class ImproveAllResponse(BaseModel):
    model: str
    llm_enabled: bool
    result: Dict[str, Any]
    matched_keywords: Optional[List[str]] = None
    missing_keywords: Optional[List[str]] = None
    notes: Optional[str] = None


# ================== LLM Gateway ==================
class LLMGateway:
    def __init__(self, api_key: Optional[str], model: str = DEFAULT_MODEL):
        self.model = model
        self.enabled = bool(api_key and OpenAI is not None)
        self.client = OpenAI(api_key=api_key) if self.enabled else None

    # --- utils ---
    @staticmethod
    def _jd_text(jd: Optional[Union[str, List[str]]]) -> str:
        if jd is None:
            return ""
        if isinstance(jd, list):
            return "\n".join([str(x) for x in jd])
        return str(jd)

    @staticmethod
    def _sanitize_list(text: str, cap: int) -> List[str]:
        items = [x.strip("•- \t") for x in text.split("\n") if x.strip()]
        return items[:cap]

    @staticmethod
    def _dedup_list(items: List[str], cap: int) -> List[str]:
        out, seen = [], set()
        for it in items:
            key = (it or "").strip().lower()
            if not key:
                continue
            if key not in seen:
                out.append(it.strip())
                seen.add(key)
            if len(out) >= cap:
                break
        return out

    @staticmethod
    def _impact_score(line: str) -> int:
        if not line:
            return 0
        s = 0
        L = line.lower()
        for token in ["%", "$", "increase", "decrease", "reduced", "improved", "saved",
                      "faster", "lower", "higher", "growth", "efficiency", "accuracy"]:
            if token in L:
                s += 2
        for ch in line:
            if ch.isdigit():
                s += 1
                break
        for token in ["days", "weeks", "months", "quarter", "year"]:
            if token in L:
                s += 1
        for v in ["built", "designed", "led", "launched", "automated", "optimized", "developed", "deployed"]:
            if v in L:
                s += 1
        return s

    def _sort_by_impact(self, items: List[str]) -> List[str]:
        return sorted(items, key=self._impact_score, reverse=True)

    # --- keyword helpers (ATS) ---
    def _extract_keywords(self, text: str, cap: int = 40) -> List[str]:
        tokens = {w.strip(".,:;|()").lower() for w in text.split() if len(w) > 2}
        return sorted(list(tokens))[:cap]

    def _extract_kws_from_result(self, improved: Any) -> str:
        if isinstance(improved, list):
            return " ".join(improved)
        if isinstance(improved, dict):
            return " ".join([str(v) if not isinstance(v, list) else " ".join(v) for v in improved.values()])
        return str(improved)

    def _matched_missing(self, jd: str, improved: Any) -> Tuple[Optional[List[str]], Optional[List[str]]]:
        if not jd:
            return None, None
        jd_tokens = set(self._extract_keywords(jd))
        used_tokens = set(self._extract_keywords(self._extract_kws_from_result(improved)))
        stop = {"and","with","for","the","you","our","are","will","this","that","your","job","role","team","need","needs","must","preferred"}
        matched = sorted(list((jd_tokens & used_tokens) - stop))
        missing = sorted(list((jd_tokens - used_tokens) - stop))
        return matched[:40], missing[:40]

    # --- prompts ---
    def _base_directives(self, req_lang: str, style: str, target_role: Optional[str]) -> str:
        tone = "concise, metric-driven, ATS-friendly" if style == "ats" else (
            "professional narrative" if style == "narrative" else "professional"
        )
        return (
            f"You are an expert resume editor. Output in {req_lang}.\n"
            f"Tone: {tone}.\n"
            "Use action verbs, quantify impact (%, time, $), avoid fluff, no personal pronouns.\n"
            "If lists are required, return plain bullet items (one per line), no numbering.\n"
            f"Target role: {target_role or '(unspecified)'}.\n"
            "Tailor to the job description if provided; otherwise, improve clarity, impact, and ATS matching for a generic Data/AI role.\n"
        )

    def _cv_context_block(self, cv: Optional[CVContext]) -> str:
        if not cv:
            return ""
        return (
            "CV context:\n"
            f"- Name: {cv.name or '-'}\n"
            f"- Title: {cv.title or '-'}\n"
            f"- Location: {cv.location or '-'}\n"
            f"- Skills: {', '.join(cv.skills or [])}\n"
        )

    def _prompt(self, section: Section, content: Any, jd_text: str, lang: str, style: str,
                target_role: Optional[str], cap: int, cv: Optional[CVContext]) -> str:
        base = self._base_directives(lang, style, target_role)
        jd_block = f"\nJob Description:\n{jd_text}\n" if jd_text else "\n(No JD provided — optimize generically)\n"
        cv_block = self._cv_context_block(cv)

        if section == Section.summary:
            return (
                base + jd_block + cv_block +
                "Task: Rewrite/produce a professional resume summary (3–4 lines max).\n"
                "If no JD, emphasize role-relevant tech (SQL/Python/BI/EDA) and measurable impact.\n"
                f"Content to improve:\n{content}\n"
                "Return: a single paragraph."
            )

        if section == Section.education:
            return (
                base + jd_block +
                "Task: Normalize Education entries (degree, major, university, year). "
                "Keep lines compact; optionally append key coursework/awards if present.\n"
                f"Content to improve:\n{content}\n"
                "Return: bullet list; one item per line, e.g., 'B.Sc. — University (2024) | coursework/award'."
            )

        if section == Section.experience:
            return (
                base + jd_block +
                f"Task: Convert to STAR-like bullet points (max {cap} bullets). "
                "Each bullet: Action + Scope/Tech + Metric/Outcome. "
                "If no JD, choose the most data/analytics-relevant achievements. Remove duplicates.\n"
                f"Content to improve:\n{content}\n"
                "Return: bullet list only."
            )

        if section == Section.skills:
            return (
                base + jd_block +
                f"Task: Produce a deduplicated, ATS-friendly skill list (max {cap}). "
                "Prefer exact phrasing when JD provided; otherwise keep common Data/AI terms. "
                "No grouping in output text (grouping handled downstream if requested).\n"
                f"Content to improve:\n{content}\n"
                "Return: comma-separated items."
            )

        if section == Section.jd_highlights:
            return (
                base + jd_block +
                f"Task: Create JD-tailored resume highlight bullets (5–7 lines; cap {cap}). "
                "Each bullet = Action + Scope/Tool + Metric/Outcome. Avoid company names. "
                "No intro/outro. No numbering, one bullet per line.\n"
                "Return: bullet list."
            )

        if section == Section.projects:
            return (
                base + jd_block +
                f"Task: Normalize project entries (max {cap}). Each line: Project — Tech/Scope — Impact/Metric.\n"
                "Prefer role-relevant projects and quantifiable outcomes.\n"
                f"Content to improve:\n{content}\n"
                "Return: bullet list (one project per line)."
            )

        if section == Section.certifications:
            return (
                base + jd_block +
                f"Task: Normalize certifications (max {cap}). Each line: Provider — Certificate (Year) | key topics (optional).\n"
                f"Content to improve:\n{content}\n"
                "Return: bullet list."
            )

        if section == Section.languages:
            return (
                base + jd_block +
                f"Task: Normalize languages (max {cap}). Each line: Language — Level (e.g., Native, Fluent, Advanced, Intermediate).\n"
                f"Content to improve:\n{content}\n"
                "Return: bullet list."
            )

        if section == Section.achievements:
            return (
                base + jd_block +
                f"Task: Normalize achievements/awards (max {cap}); quantify where possible.\n"
                f"Content to improve:\n{content}\n"
                "Return: bullet list."
            )

        # contact
        return (
            base + jd_block + cv_block +
            "Task: Normalize contact header fields (Name, Title, Location, Email, Phone, Links). "
            "Fix capitalization/spacing. Only provide JSON object with these fields.\n"
            f"Content to improve:\n{content}\n"
            "Return strictly JSON object with keys: name, title, location, email, phone, links."
        )

    # --- post-processors ---
    def _postprocess(self, section: Section, text: str, cap: int,
                     sort_bullets: bool, return_mode: str, group_skills: bool):
        if section in (
            Section.experience, Section.education, Section.jd_highlights,
            Section.projects, Section.certifications, Section.languages, Section.achievements
        ):
            items = self._sanitize_list(text, cap)
            if sort_bullets and section in (Section.experience, Section.jd_highlights, Section.achievements, Section.projects):
                items = self._sort_by_impact(items)
            if return_mode == "json":
                key = (
                    "bullets" if section in (Section.experience, Section.jd_highlights, Section.projects,
                                             Section.certifications, Section.languages, Section.achievements)
                    else "items"
                )
                return {key: items}
            return items

        if section == Section.skills:
            parts = [p.strip(" ,;|") for p in text.replace("\n", ",").split(",") if p.strip()]
            parts = self._dedup_list(parts, cap)
            if return_mode == "json":
                if group_skills:
                    tech, tools, soft = [], [], []
                    for s in parts:
                        sl = s.lower()
                        if sl in {"communication", "leadership", "teamwork", "problem solving", "presentation"}:
                            soft.append(s)
                        elif any(k in sl for k in ["power bi", "tableau", "excel", "dbt", "git", "jupyter", "docker"]):
                            tools.append(s)
                        else:
                            tech.append(s)
                    return {"skills_grouped": {"technical": tech, "tools": tools, "soft": soft}}
                return {"skills": parts}
            return parts

        if section == Section.contact:
            # حاول نحاول نفهم JSON من الـ LLM، وإلا نرجّع النص كما هو
            import json as _json
            try:
                obj = _json.loads(text)
                # تأكد من الحقول
                out = {
                    "name": obj.get("name", "").strip(),
                    "title": obj.get("title", "").strip(),
                    "location": obj.get("location", "").strip(),
                    "email": obj.get("email", "").strip(),
                    "phone": obj.get("phone", "").strip(),
                    "links": obj.get("links", "").strip(),
                }
                return out if return_mode == "json" else str(out)
            except Exception:
                return {"raw": text} if return_mode == "json" else text

        # summary/plain
        clean = " ".join(line.strip() for line in text.splitlines())
        if return_mode == "json":
            return {"summary": clean}
        return clean

    # --- public API ---
    def improve(self, req: ImproveRequest) -> ImproveResponse:
        if not self.enabled:
            if req.force_llm:
                raise HTTPException(
                    status_code=503,
                    detail="LLM disabled: set OPENAI_API_KEY and install openai."
                )
            return self._fallback(req)

        prompt = self._prompt(
            section=req.section,
            content=req.content,
            jd_text=self._jd_text(req.job_description),
            lang=req.language,
            style=req.style,
            target_role=req.target_role,
            cap=req.max_items,
            cv=req.cv,
        )
        try:
            resp = self.client.chat.completions.create(
                model=self.model,
                messages=[
                    {"role": "system", "content": "You are a senior resume editor and ATS optimization expert."},
                    {"role": "user", "content": prompt},
                ],
                temperature=0.2,
            )
            text = (resp.choices[0].message.content or "").strip()
        except Exception as e:
            raise HTTPException(status_code=502, detail=f"LLM error: {e}")

        improved = self._postprocess(
            section=req.section,
            text=text,
            cap=req.max_items,
            sort_bullets=req.sort_bullets_by_impact,
            return_mode=req.return_mode,
            group_skills=req.group_skills,
        )

        matched, missing = self._matched_missing(self._jd_text(req.job_description), improved)
        return ImproveResponse(
            section=req.section,
            improved=improved,
            notes="AI enhanced. Verify facts/metrics before submission.",
            matched_keywords=matched,
            missing_keywords=missing,
            model=self.model,
        )

    # --- fallback if LLM disabled ---
    def _fallback(self, req: ImproveRequest) -> ImproveResponse:
        content = req.content or ""
        out: Any
        if req.section == Section.summary:
            base = str(content).strip()
            if len(base) < 40 and req.target_role:
                base = f"Results-driven {req.target_role} with hands-on analytics and automation experience."
            out = {"summary": base[:700]} if req.return_mode == "json" else base[:700]
            return ImproveResponse(section=req.section, improved=out, notes="Fallback: lightly cleaned.", model="fallback")

        if req.section in (
            Section.experience, Section.education, Section.jd_highlights,
            Section.projects, Section.certifications, Section.languages, Section.achievements
        ):
            items = content if isinstance(content, list) else [str(content)]
            items = [i.strip("•- \t") for i in items if str(i).strip()]
            items = self._dedup_list(items, req.max_items)
            if req.sort_bullets_by_impact and req.section in (Section.experience, Section.jd_highlights, Section.achievements, Section.projects):
                items = self._sort_by_impact(items)
            if req.return_mode == "json":
                out = {"bullets": items}
                if req.section == Section.education:
                    out = {"items": items}
            else:
                out = items
            return ImproveResponse(section=req.section, improved=out, notes="Fallback: normalized list.", model="fallback")

        if req.section == Section.skills:
            skills = content if isinstance(content, list) else [
                s.strip() for s in str(content).replace("\n", ",").split(",")
            ]
            skills = [s for s in skills if s]
            skills = self._dedup_list(skills, req.max_items)
            if req.return_mode == "json":
                out: Union[Dict[str, Any], List[str]] = {"skills": skills}
                if req.group_skills:
                    tech, tools, soft = [], [], []
                    for s in skills:
                        sl = s.lower()
                        if sl in {"communication", "leadership", "teamwork", "problem solving", "presentation"}:
                            soft.append(s)
                        elif any(k in sl for k in ["power bi", "tableau", "excel", "dbt", "git", "jupyter", "docker"]):
                            tools.append(s)
                        else:
                            tech.append(s)
                    out = {"skills_grouped": {"technical": tech, "tools": tools, "soft": soft}}
            else:
                out = skills
            return ImproveResponse(section=req.section, improved=out, notes="Fallback: deduplicated.", model="fallback")

        if req.section == Section.contact:
            # بس تنضيف بسيط في fallback
            if isinstance(content, dict) and req.return_mode == "json":
                keys = ["name","title","location","email","phone","links"]
                out = {k: str(content.get(k,"")).strip() for k in keys}
                return ImproveResponse(section=req.section, improved=out, notes="Fallback: normalized fields.", model="fallback")
            return ImproveResponse(section=req.section, improved=content, notes="Fallback: raw contact.", model="fallback")


# ================== FastAPI app ==================
app = FastAPI(title="V-AI Resume API", version="1.1", openapi_version="3.1.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],   # للإنتاج عدّلها
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

gateway = LLMGateway(api_key=OPENAI_API_KEY, model=DEFAULT_MODEL)


@app.get("/health")
def health():
    return {
        "status": "ok",
        "llm_enabled": gateway.enabled,
        "model": gateway.model if gateway.enabled else "fallback",
    }


@app.post("/v1/improve", response_model=ImproveResponse)
def improve(req: ImproveRequest):
    """
    Improve a CV section using AI. Works with or without JD.
    Sections: summary, education, experience, skills, jd_highlights, projects,
              certifications, languages, achievements, contact.
    Options:
      - return_mode='json' to get structured payload
      - sort_bullets_by_impact (experience/highlights/projects/achievements)
      - group_skills (for skills)
      - force_llm=true to avoid fallback
    """
    try:
        return gateway.improve(req)
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@app.post("/v1/improve_all", response_model=ImproveAllResponse)
def improve_all(req: ImproveAllRequest):
    """
    Take a full CV object and return a fully improved, ATS-optimized CV (JSON) in one call.
    Any missing section is skipped. Uses JD tailoring if provided.
    """
    if not gateway.enabled and req.force_llm:
        raise HTTPException(status_code=503, detail="LLM disabled: set OPENAI_API_KEY and install openai.")

    jd = req.job_description
    lang = req.language
    style = req.style
    role = req.target_role
    cap = req.max_items
    ret = req.return_mode
    group = req.group_skills
    sort_impact = req.sort_bullets_by_impact

    out: Dict[str, Any] = {}
    matched_all, missing_all = set(), set()

    # helper to run a section
    def run(section: Section, content: Any, extra: Dict[str, Any] = None):
        r = gateway.improve(ImproveRequest(
            section=section, content=content, job_description=jd, language=lang, style=style,
            target_role=role, max_items=cap, return_mode=ret, group_skills=group,
            sort_bullets_by_impact=sort_impact, force_llm=req.force_llm
        ))
        if r.matched_keywords:
            matched_all.update(r.matched_keywords)
        if r.missing_keywords:
            missing_all.update(r.missing_keywords)
        return r.improved

    cv = req.cv or FullCVIn()

    if cv.contact:
        out["contact"] = run(Section.contact, cv.contact)
    if cv.summary:
        out["summary"] = run(Section.summary, cv.summary)
    if cv.experience:
        out["experience"] = run(Section.experience, cv.experience)
    if cv.education:
        out["education"] = run(Section.education, cv.education)
    if cv.skills:
        out["skills"] = run(Section.skills, cv.skills)
    if cv.projects:
        out["projects"] = run(Section.projects, cv.projects)
    if cv.certifications:
        out["certifications"] = run(Section.certifications, cv.certifications)
    if cv.languages:
        out["languages"] = run(Section.languages, cv.languages)
    if cv.achievements:
        out["achievements"] = run(Section.achievements, cv.achievements)

    # سكشن Highlights من الـ JD (اختياري لو JD موجود)
    if jd:
        out["jd_highlights"] = run(Section.jd_highlights, "")

    matched = sorted(list(matched_all)) if matched_all else None
    missing = sorted(list(missing_all)) if missing_all else None

    return ImproveAllResponse(
        model=gateway.model if gateway.enabled else "fallback",
        llm_enabled=gateway.enabled,
        result=out,
        matched_keywords=matched,
        missing_keywords=missing,
        notes="AI enhanced full CV. Verify facts/metrics."
    )
# أعلى الملف (لو غير موجود)
import json
from fastapi import Body

def _safe_dump(obj) -> str:
    """
    يدعم Pydantic v2 (model_dump / model_dump_json) أو dict/namedtuple… الخ.
    يرجّع تمثيل JSON نصّي جميل لأي كائن.
    """
    try:
        # Pydantic v2 model?
        if hasattr(obj, "model_dump_json"):
            return obj.model_dump_json(indent=2, exclude_none=True)
        if hasattr(obj, "model_dump"):
            return json.dumps(obj.model_dump(), ensure_ascii=False, indent=2)
        # dict/list/…؟
        if isinstance(obj, (dict, list, tuple)):
            return json.dumps(obj, ensure_ascii=False, indent=2)
        # Pydantic v1 (احتياط)
        if hasattr(obj, "json"):
            return obj.json(indent=2, exclude_none=True)  # لو v1 موجودة
        return str(obj)
    except Exception:
        return str(obj)

@app.post("/v1/advice")
def give_advice(
    cv: FullCVIn = Body(..., description="User CV (partial or full)"),
    job_description: Optional[Union[str, List[str]]] = Body(None, description="Target job description or list of lines"),
    target_role: Optional[str] = Body(None, description="e.g., 'Data Analyst'"),
    language: str = Body("en"),
):
    """
    Generate AI advice for improving the CV based on the job description.
    Returns actionable insights: missing skills, recommended projects/certifications, section feedback, ATS tips.
    """
    # 1) تأكد أن الـ LLM مُفعّل
    if not gateway.enabled:
        raise HTTPException(status_code=503, detail="LLM disabled: set OPENAI_API_KEY and install openai.")

    # 2) حضّر الداتا للنص
    jd_text = gateway._jd_text(job_description)
    role = target_role or "Data Analyst"
    cv_json_pretty = _safe_dump(cv)

    # 3) البرومبت
    prompt = f"""
You are a senior AI Career Advisor and ATS Expert.

Task:
Review the candidate's CV and the provided job description, then produce concise, actionable advice
to improve the resume and career positioning for the target role: {role}.
Return **valid JSON only** with these keys:

- "section_feedback": object with brief feedback for each section 
  (Summary, Experience, Education, Skills, Projects, Certifications, Achievements, Languages).
- "missing_skills": array of skills required by the JD but absent/weak in CV.
- "recommended_projects": array of project ideas aligned with JD (each item short and practical).
- "recommended_certifications": array of relevant certs/courses (provider included).
- "highlight_experience": array of bullets describing which experiences to emphasize/rewrite and why.
- "ats_tips": 3–6 bullets, crisp and specific (keywords/formatting/metrics).
- "career_advice": one short paragraph (2–4 sentences).

Constraints:
- JSON only (no markdown, no prose outside JSON).
- Output language: {language}.  # ← دي أهم سطر بيفرض العربية لما تبعت "ar"

Candidate_CV (JSON-ish):
{cv_json_pretty}

Job_Description:
{jd_text or "(none provided)"}
""".strip()

    # 4) نداء الـ LLM + robust parsing
    try:
        resp = gateway.client.chat.completions.create(
            model=gateway.model,
            messages=[
                {"role": "system", "content": "You are a senior AI resume coach and career strategist."},
                {"role": "user", "content": prompt},
            ],
            temperature=0.3,
        )
        raw = (resp.choices[0].message.content or "").strip()
    except Exception as e:
        # عادةً مشاكل: مفتاح مفقود/موديل خاطئ/شبكة
        raise HTTPException(status_code=502, detail=f"LLM error: {e}")

    # 5) حاول نحول JSON—مع علاجات بسيطة لو الموديل أضاف نص محيط
    advice_json = None
    try:
        advice_json = json.loads(raw)
    except Exception:
        # جرّب اقتطاع أي شيء قبل/بعد أول/آخر { }
        try:
            start = raw.find("{")
            end = raw.rfind("}")
            if start != -1 and end != -1 and end > start:
                advice_json = json.loads(raw[start:end+1])
        except Exception:
            pass

    if advice_json is None:
        # لا تفشل بـ 500 — ارجع النص الخام للمراجعة
        return {
            "model": gateway.model,
            "llm_enabled": True,
            "target_role": role,
            "result": {"raw_text": raw},
            "notes": "Returned non-JSON content. Inspect 'raw_text'. Consider lowering temperature or tightening prompt."
        }

    return {
        "model": gateway.model,
        "llm_enabled": True,
        "target_role": role,
        "result": advice_json,
        "notes": "AI-generated personalized CV & career advice."
    }
