image_proc_prompt="""You are an OCR‐and‐information‐extraction assistant. 

Your task:  
1. Transcribe the provided image into clean, readable text. 
"""

prompts_for_data_extraction_ = {
    "education": "Extract the university name, degree, and the education period (start and end dates).",
    "skills": "Extract all technical and soft skills mentioned in previous work experience, projects, and the skills section of the resume.",
    "job title": "Extract the current job title and its level (e.g., Senior, Mid-level, Junior, etc.).",
    "name": "Extract the full name of the resume owner.",
    "phone number": "Extract the phone number mentioned in the resume.",
    "email": "Extract the email address mentioned in the resume.",
    "projects": "Extract all the projects mentioned in the resume.",
    "address": "Extract the address of the resume owner.",
    "previous experiences": "Extract all previous work experiences, including the job title/position and the responsibilities for each job."
}


{
 
    "certifications": "Extract all certifications along with the issuing organization and date (if available).",
    "languages": "Extract all spoken or written languages and their proficiency levels (e.g., fluent, intermediate).",
    "summary": "Extract the professional summary or objective section that highlights the candidate’s profile.",
    "achievements": "Extract notable achievements or awards mentioned in the resume.",
    "publications": "Extract details of any research papers, articles, or publications listed.",
    "references": "Extract reference names, titles, and contact details if available.",
    "github_profile": "Extract GitHub or portfolio links if available.",
    "availability": "Extract availability information such as notice period or earliest start date.",
    "expected_salary": "Extract expected salary or compensation preferences, if mentioned.",
    "relocation_preference": "Extract any stated willingness to relocate or location preferences.",
    "industry_experience": "Extract the industries the candidate has previously worked in (e.g., fintech, healthcare, etc.).",
    "tools_and_technologies": "Extract specific tools, platforms, or software the candidate has experience with (e.g., Excel, AWS, Docker).",
    "volunteer_experience": "Extract details of any volunteer work mentioned."
}





system_prompt = """
You are a knowledgeable AI assistant utilizing a retrieval-augmented generation (RAG) approach. Your answers must be strictly based on the context provided to ensure accuracy and relevance. if the context doesn't have the answer don't answer"

- If the context does not contain the answer, respond with: "not avaliable "
- Do not include page numbers or references.
- Do not include introductory phrases such as: "بناءً على المعلومات المقدمة..." or similar.
- Provide only the direct answer — no extra explanations, commentary, or formatting.
- the output format should be [lnaguage] 100%
"""



