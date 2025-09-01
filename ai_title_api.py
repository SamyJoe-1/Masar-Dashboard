import os
import re
import time
from typing import Optional, Dict, Any

from fastapi import FastAPI, Body, Request, Form
from fastapi.responses import JSONResponse, PlainTextResponse
from pydantic import BaseModel, Field

# -------- .env (اختياري) --------
try:
    from dotenv import load_dotenv
    load_dotenv()
except Exception:
    pass

# -------- OpenAI --------
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY", "")
OPENAI_MODEL   = os.getenv("OPENAI_MODEL", "gpt-4o-mini")

OPENAI_OK = True
_client = None
try:
    from openai import OpenAI
    if OPENAI_API_KEY:
        _client = OpenAI(api_key=OPENAI_API_KEY)
    else:
        OPENAI_OK = False
except Exception:
    OPENAI_OK = False
    _client = None

app = FastAPI(
    title="Resume ATS UPDATE – AI Job Title API",
    version="1.1.0",
    description="AI agent يفهم Job Description ويُرجع Job Title نظيف (بدون seniority/company). يقبل JSON أو text/plain."
)

# -------- Models --------
class JDRequest(BaseModel):
    job_description: str = Field(..., description="نص الـ JD كامل (عربي/إنجليزي)")
    debug: Optional[bool] = Field(False, description="أعد خطوات التنفيذ في الرد")

class JDResponse(BaseModel):
    job_title: str
    source: str                 # llm | fallback | failed
    model: Optional[str] = None
    elapsed_ms: int
    note: Optional[str] = None
    error: Optional[str] = None
    log: Optional[Dict[str, Any]] = None

# -------- Cleaning helpers --------
SENIORITY_WORDS = {
    # English
    "senior","jr","sr","junior","middle","mid-level","mid level",
    "principal","lead","head","intern","graduate","trainee","entry-level","entry level",
    # Arabic common
    "كبير","سينير","سينيور","جونيور","مبتدئ","متدرب","رئيس","قائد"
}
ROLE_NORMALIZE_RE = re.compile(r"\s{2,}")

def strip_seniority_company(title: str) -> str:
    t = title.strip()
    if not t:
        return t
    # remove seniority words
    t = re.sub(r"(?i)\b(" + "|".join(map(re.escape, SENIORITY_WORDS)) + r")\b", " ", t)
    # remove at / with Company
    t = re.split(r"(?i)\b(at|with|في|لدى)\b", t)[0]
    # basic tidy
    t = t.replace("•", " ").strip(" .,:;\"'“”‘’-/\\|()[]")
    t = ROLE_NORMALIZE_RE.sub(" ", t)
    return t[:80].strip()

# -------- Heuristic fallback (EN/AR) --------
PATTERNS = [
    r"(?i)job\s*title\s*:\s*([A-Za-z][A-Za-z0-9\s/&\-\(\)]{2,80})",
    r"(?i)position\s*:\s*([A-Za-z][A-Za-z0-9\s/&\-\(\)]{2,80})",
    r"(?i)role\s*:\s*([A-Za-z][A-Za-z0-9\s/&\-\(\)]{2,80})",
    r"(?i)as an?\s+([A-Za-z][A-Za-z0-9\s/&\-\(\)]{2,80})",
    r"\b([A-Za-z][A-Za-z\s/&\-]*(Engineer|Developer|Analyst|Scientist|Manager|Consultant|Specialist|Designer|Architect|Administrator|Coordinator|Officer|Product Manager|Project Manager|Program Manager))\b",
    # Arabic
    r"(?i)المسمى\s*الوظيفي\s*[:：]\s*([^\n\r]{2,80})",
    r"(?i)الوظيفة\s*[:：]\s*([^\n\r]{2,80})",
    r"(?i)نبحث\s+عن\s+([^\n\r]{2,80})",
    r"(?i)ك\s*ـ?\s*([^\n\r]{2,80})\s+ستقوم",  # "كـ مهندس ... ستقوم"
]

def heuristic_fallback(jd: str) -> str:
    text = jd.strip()
    if not text:
        return "-"
    for pat in PATTERNS:
        m = re.search(pat, text)
        if m:
            raw = m.group(1).strip()
            raw = re.split(r"[\n\r.،:;|/]", raw)[0]
            clean = strip_seniority_company(raw)
            if clean:
                return clean
    first = text.splitlines()[0] if "\n" in text else text
    first = re.split(r"[:\.\-–،؛]", first)[0]
    return strip_seniority_company(first) or "-"

# -------- LLM inference --------
SYS_PROMPT = (
    "You are a precise extractor. Given a job description (in English or Arabic), "
    "return ONLY the clean job title without seniority (no Senior/Junior/Lead/Head) and without company names. "
    "Keep it short (max 6 words). Respond with plain text only."
)

def llm_infer_title(jd: str) -> str:
    if not OPENAI_OK or not _client:
        raise RuntimeError("OpenAI client not configured")
    resp = _client.chat.completions.create(
        model=OPENAI_MODEL,
        messages=[
            {"role": "system", "content": SYS_PROMPT},
            {"role": "user", "content": jd},
        ],
        temperature=0,
        max_tokens=24,
    )
    content = (resp.choices[0].message.content or "").strip()
    content = content.replace("\n", " ").strip(" \"'“”‘’.،")
    return strip_seniority_company(content)

# -------- Common pipeline --------
def infer_pipeline(jd: str, debug: bool=False) -> JDResponse:
    t0 = time.time()
    logs = {"steps": [], "len": len(jd or "")} if debug else None
    try:
        if OPENAI_OK and _client and OPENAI_API_KEY:
            if logs: logs["steps"].append("llm")
            title = llm_infer_title(jd)
            return JDResponse(
                job_title=title or "-",
                source="llm",
                model=OPENAI_MODEL,
                elapsed_ms=int((time.time() - t0) * 1000),
                log=logs
            )
        else:
            raise RuntimeError("LLM disabled")
    except Exception as e:
        if logs: logs["steps"].append(f"llm_error: {e}")
        try:
            fb = heuristic_fallback(jd)
            return JDResponse(
                job_title=fb or "-",
                source="fallback",
                model=None,
                elapsed_ms=int((time.time() - t0) * 1000),
                note="Returned by local heuristic fallback.",
                error=str(e) if debug else None,
                log=logs
            )
        except Exception as e2:
            return JDResponse(
                job_title="-",
                source="failed",
                model=None,
                elapsed_ms=int((time.time() - t0) * 1000),
                error=f"LLM & Fallback failed: {e2}",
                log=logs
            )

# -------- Endpoints --------
@app.get("/")
def home():
    return {"message": "🚀 AI Job Title API running", "docs": "/docs"}

# (A) ذكاء: نفس المسار يقبل JSON أو نص خام تلقائيًا
@app.post("/ai-job-title", response_model=JDResponse)
async def ai_job_title(request: Request):
    """
    يقبل:
      - application/json: { "job_description": "...", "debug": false }
      - text/plain: البودي كله يكون JD كنص عادي
      - multipart/form-data: job_description, debug
    """
    ctype = (request.headers.get("content-type") or "").lower()
    jd_text = ""
    debug = False

    if "application/json" in ctype:
        body = await request.json()
        jd_text = (body.get("job_description") or "").strip()
        debug = bool(body.get("debug", False))
    elif "text/plain" in ctype:
        raw = await request.body()
        jd_text = raw.decode("utf-8", errors="ignore").strip()
        debug = bool(request.query_params.get("debug", "false").lower() == "true")
    elif "multipart/form-data" in ctype:
        form = await request.form()
        jd_text = (form.get("job_description") or "").strip()
        debug = str(form.get("debug", "false")).lower() == "true"
    else:
        # محاولة أخيرة لقراءة نص خام
        raw = await request.body()
        jd_text = raw.decode("utf-8", errors="ignore").strip()

    if not jd_text:
        return JSONResponse({"detail": "job description is empty"}, status_code=400)

    return infer_pipeline(jd_text, debug)

# (B) اختياري: نص خام صِرف (للتبسيط لو حابب)
@app.post("/ai-job-title-text", response_model=JDResponse)
async def ai_job_title_text(body: str = Body(..., media_type="text/plain"), debug: bool=False):
    return infer_pipeline(body, debug)

# (C) اختياري: فورم
@app.post("/ai-job-title-form", response_model=JDResponse)
async def ai_job_title_form(job_description: str = Form(...), debug: bool = Form(False)):
    return infer_pipeline(job_description, bool(debug))
