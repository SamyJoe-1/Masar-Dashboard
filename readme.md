# 🏥 CV Question Extractor API

A FastAPI-based web service for extracting interview questions from CV/Resume PDF documents and evaluating candidate answers.

This API:
- Accepts CV/Resume PDF files (upload or URL)
- Converts them to images using OCR
- Uses LLM-based analysis to extract relevant interview questions based on job descriptions
- Evaluates candidate answers against job requirements
- Returns structured JSON output with questions and evaluations
- Supports both English and Arabic languages

---

## 🚀 Features
- ✅ PDF to Image Conversion with OCR
- ✅ CV/Resume Analysis and Question Generation
- ✅ Job Description-based Question Extraction
- ✅ Multi-language Support (English/Arabic)
- ✅ URL-based PDF Processing
- ✅ Answer Evaluation and Scoring
- ✅ FastAPI-powered REST API
- ✅ JSON Response Ready for Integration

---

## 📂 Project Structure
CV/
│
├── app/
│   ├── main.py               # FastAPI application with all endpoints
│   ├── pdf_data_extraction/  # Core extraction modules
│   │   ├── __init__.py
│   │   ├── pdf_text_extraction.py  # PDF processing and OCR
│   │   ├── prompts.py              # LLM prompts for extraction
│   │   ├── utils.py                # Question extraction and evaluation utilities
│
├── row_data/                 # Sample PDF files folder
├── temp_uploads/             # Temporary file storage
├── extracted_images/         # Generated image files from PDFs
├── requirements.txt          # Dependencies
└── README.md

---

## 🔗 API Endpoints

### 1. Extract Questions from CV (File Upload)
**POST** `/extract-questions-from-cv/`
- **Description**: Upload a CV PDF and extract relevant interview questions
- **Parameters**:
  - `file`: PDF file (multipart/form-data)
  - `job_description`: Text description of the job role
  - `language`: Language preference ("English" or "Arabic", default: "English")
- **Response**: JSON with extracted questions and processing times

### 2. Extract Questions from CV - Arabic (File Upload)
**POST** `/extract-questions-from-cv_ar/`
- **Description**: Upload a CV PDF and extract questions in Arabic
- **Parameters**:
  - `file`: PDF file (multipart/form-data)
  - `job_description`: Text description of the job role
- **Response**: JSON with extracted questions in Arabic

### 3. Extract Questions from CV (URL)
**POST** `/extract-questions-from-cv-url/`
- **Description**: Process a CV PDF from a URL and extract interview questions
- **Parameters**:
  - `pdf_url`: Direct URL to PDF file
  - `job_description`: Text description of the job role
  - `language`: Language preference ("English" or "Arabic", default: "English")
- **Response**: JSON with extracted questions and processing times

### 4. Extract Questions from CV - Arabic (URL)
**POST** `/extract-questions-from-cv-url_ar/`
- **Description**: Process a CV PDF from URL and extract questions in Arabic
- **Parameters**:
  - `pdf_url`: Direct URL to PDF file
  - `job_description`: Text description of the job role
- **Response**: JSON with extracted questions in Arabic

### 5. Evaluate User Answer
**POST** `/evaluate-user-answer/`
- **Description**: Evaluate candidate answers against job requirements
- **Parameters**:
  - `job_description`: Text description of the job role
  - `questions_with_answers`: JSON string with questions and candidate answers
  - `language`: Language preference ("English" or "Arabic")
- **Response**: JSON with evaluation scores, strengths, weaknesses, and fit assessment

---

## 🚀 How to Run Locally

1. **Install Dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

2. **Set Environment Variables**:
   Create a `.env` file with your OpenAI API key:
   ```
   OPENAI_API_KEY=your_api_key_here
   ```

3. **Start the Server**:
   ```bash
   PYTHONPATH=./app uvicorn app.main:app --reload
   ```

4. **Access the API**:
   Open http://127.0.0.1:8000/docs in your browser to see the interactive API documentation

---

## 📝 Example Usage

### Upload CV and Extract Questions:
```bash
curl -X POST "http://127.0.0.1:8000/extract-questions-from-cv/" \
  -F "file=@resume.pdf" \
  -F "job_description=Software Engineer position requiring Python and FastAPI experience" \
  -F "language=English"
```

### Process CV from URL:
```bash
curl -X POST "http://127.0.0.1:8000/extract-questions-from-cv-url/" \
  -F "pdf_url=https://example.com/resume.pdf" \
  -F "job_description=Data Scientist role with machine learning expertise" \
  -F "language=English"
```

### Evaluate Candidate Answers:
```bash
curl -X POST "http://127.0.0.1:8000/evaluate-user-answer/" \
  -F "job_description=Software Engineer position" \
  -F "questions_with_answers={\"questions\":[{\"question\":\"What is your Python experience?\",\"answer\":\"5 years of Python development\"}]}" \
  -F "language=English"
```

---

## 🌐 Response Format

### Question Extraction Response:
```json
{
  "message": "✅ Document processed successfully.",
  "elapsed_times": {
    "pdf_to_images": "2.45s",
    "ocr": "8.32s",
    "data_extraction": "3.21s",
    "total_runtime": "14.12s"
  },
  "structured_data": [
    {
      "question": "What programming languages are you proficient in?",
      "category": "technical_skills"
    }
  ]
}
```

### Answer Evaluation Response:
```json
{
  "overall_score": 85,
  "strengths": ["Strong technical background", "Relevant experience"],
  "weaknesses": ["Limited leadership experience"],
  "overall_fit_justification": "Candidate shows strong technical skills matching job requirements",
  "fit_for_role": "High"
}
``` 
