import time
import aiohttp
import aiofiles
from urllib.parse import urlparse
from fastapi import FastAPI, UploadFile, File, HTTPException
import shutil
import uuid
import os
import sys

# Get the absolute path to the "app" directory
base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), "app"))

# Add "app" to sys.path if it's not already there
if base_dir not in sys.path:
    sys.path.insert(0, base_dir)

from pdf_data_extraction.prompts import image_proc_prompt, system_prompt, prompts_for_data_extraction_
from pdf_data_extraction.pdf_text_extraction import PDFTextExtraction
from pdf_data_extraction.utils import extract_questions , rate_answer
from fastapi import FastAPI, UploadFile, File, Form
app = FastAPI()

async def download_pdf_from_url(url: str) -> str:
    """Download PDF from URL and save to temp directory"""
    # Validate URL
    parsed_url = urlparse(url)
    if not parsed_url.scheme or not parsed_url.netloc:
        raise HTTPException(status_code=400, detail="Invalid URL provided")
    
    # Create temp directory
    temp_dir = "temp_uploads"
    os.makedirs(temp_dir, exist_ok=True)
    
    # Generate unique filename with original name if possible
    original_filename = os.path.basename(parsed_url.path)
    if not original_filename or not original_filename.endswith('.pdf'):
        original_filename = f"{uuid.uuid4()}.pdf"
    else:
        # Add UUID to avoid conflicts
        name, ext = os.path.splitext(original_filename)
        original_filename = f"{uuid.uuid4()}_{name}{ext}"
    
    file_path = os.path.join(temp_dir, original_filename)
    
    try:
        timeout = aiohttp.ClientTimeout(total=300)  # 5 minutes timeout
        async with aiohttp.ClientSession(timeout=timeout) as session:
            async with session.get(url, allow_redirects=True) as response:
                if response.status != 200:
                    raise HTTPException(status_code=400, detail=f"Failed to download PDF from URL. Status: {response.status}")
                
                # Check content length
                content_length = response.headers.get('content-length')
                if content_length and int(content_length) > 50 * 1024 * 1024:  # 50MB limit
                    raise HTTPException(status_code=400, detail="PDF file too large (max 50MB)")
                
                # Check if content type is PDF (optional check)
                content_type = response.headers.get('content-type', '')
                if content_type and 'pdf' not in content_type.lower() and 'application/octet-stream' not in content_type.lower():
                    # Log warning but don't fail - some servers don't set correct content-type
                    print(f"Warning: Content-Type is {content_type}, expected PDF")
                
                # Download and save file
                async with aiofiles.open(file_path, 'wb') as f:
                    async for chunk in response.content.iter_chunked(8192):
                        await f.write(chunk)
        
        # Verify file was created and has content
        if not os.path.exists(file_path) or os.path.getsize(file_path) == 0:
            raise HTTPException(status_code=400, detail="Downloaded file is empty or corrupted")
        
        return file_path
    except aiohttp.ClientError as e:
        # Clean up file if download failed
        if os.path.exists(file_path):
            os.remove(file_path)
        raise HTTPException(status_code=400, detail=f"Network error downloading PDF: {str(e)}")
    except Exception as e:
        # Clean up file if download failed
        if os.path.exists(file_path):
            os.remove(file_path)
        raise HTTPException(status_code=400, detail=f"Failed to download PDF: {str(e)}")

async def process_pdf_optimized (file, system_prompt,  job_description ,language = "Arabic"):
    """Optimized PDF processing with full async pipeline"""
    start_total = time.perf_counter()

    # Save uploaded file to temp directory
    temp_dir = "temp_uploads"
    os.makedirs(temp_dir, exist_ok=True)
    filename = f"{uuid.uuid4()}_{file.filename}"
    file_path = os.path.join(temp_dir, filename)

    with open(file_path, "wb") as buffer:
        shutil.copyfileobj(file.file, buffer)

    try:
        system_prompt=system_prompt.replace("lnaguage" , language)
        extractor = PDFTextExtraction(file_path , system_prompt   , openai=True)
        
        # OPTION 1: Full async pipeline (fastest)
        start = time.perf_counter()
        
        # Convert PDF to images (optimized)
        extractor.convert_pdf_to_images()
        elapsed_pdf_images = time.perf_counter() - start
        
        # Process images with async method (conservative rate limiting)
        start = time.perf_counter()
        text = await extractor.process_images_async_parallel(
            prompt=image_proc_prompt, 
            max_concurrent=8  # Reduced from 20 to prevent API overload
        )
        
        # Save extracted text
        with open("output.txt", "w", encoding="utf-8") as f:
            f.write(text)
        
        elapsed_ocr = time.perf_counter() - start
        
        
       
        
        # Structured data extraction with async method
        #start = time.perf_counter()
        #results = await extractor.extract_without_classes(
            #prompts_for_data_extraction_,
           
            
       
       # )

        elapsed_data_extraction = time.perf_counter() - start
        
        total_elapsed = time.perf_counter() - start_total
        extracted_questions =extract_questions(text , job_description , language=language)
        
        return {
            "message": "✅ Document processed successfully.",
        
            "elapsed_times": {
                "pdf_to_images": f"{elapsed_pdf_images:.2f}s",
                "ocr": f"{elapsed_ocr:.2f}s",
            
                "data_extraction": f"{elapsed_data_extraction:.2f}s",
                "total_runtime": f"{total_elapsed:.2f}s"
            },
            "structured_data": extracted_questions
        }
        
    finally:
        # Cleanup uploaded file after processing
        if os.path.exists(file_path):
            os.remove(file_path)

async def process_pdf_from_url_optimized(url: str, system_prompt, job_description, language="Arabic"):
    """Optimized PDF processing from URL with full async pipeline"""
    start_total = time.perf_counter()
    
    # Download PDF from URL
    file_path = await download_pdf_from_url(url)
    
    try:
        system_prompt = system_prompt.replace("lnaguage", language)
        extractor = PDFTextExtraction(file_path, system_prompt, openai=True)
        
        # OPTION 1: Full async pipeline (fastest)
        start = time.perf_counter()
        
        # Convert PDF to images (optimized)
        extractor.convert_pdf_to_images()
        elapsed_pdf_images = time.perf_counter() - start
        
        # Process images with async method (conservative rate limiting)
        start = time.perf_counter()
        text = await extractor.process_images_async_parallel(
            prompt=image_proc_prompt, 
            max_concurrent=8  # Reduced from 20 to prevent API overload
        )
        
        # Save extracted text
        with open("output.txt", "w", encoding="utf-8") as f:
            f.write(text)
        
        elapsed_ocr = time.perf_counter() - start
        elapsed_data_extraction = time.perf_counter() - start
        
        total_elapsed = time.perf_counter() - start_total
        extracted_questions = extract_questions(text, job_description, language=language)
        
        return {
            "message": "✅ Document processed successfully from URL.",
            "elapsed_times": {
                "pdf_to_images": f"{elapsed_pdf_images:.2f}s",
                "ocr": f"{elapsed_ocr:.2f}s",
                "data_extraction": f"{elapsed_data_extraction:.2f}s",
                "total_runtime": f"{total_elapsed:.2f}s"
            },
            "structured_data": extracted_questions
        }
        
    finally:
        # Cleanup downloaded file after processing
        if os.path.exists(file_path):
            os.remove(file_path)

@app.post("/extract-questions-from-cv/")
async def process_pdf_with_file(
    file: UploadFile = File(...),
    job_description: str = Form(...),
    language: str = Form(default="English")
):
    return await process_pdf_optimized(file, system_prompt, job_description, language.upper())



@app.post("/extract-questions-from-cv-url/")
async def process_pdf_from_url_with_language(
    pdf_url: str = Form(...),
    job_description: str = Form(...),
    language: str = Form(default="English")
):
    """Extract questions from CV PDF via URL with language parameter"""
    return await process_pdf_from_url_optimized(pdf_url, system_prompt, job_description, language.upper())


@app.post("/evaluate-user-answer/")
def eval_user_naswer(

     job_description: str = Form(...),
     questions_with_answers : str = Form(...),
     language: str = Form(...)
):
    evaluation= rate_answer(job_description , questions_with_answers , language.upper())
    if evaluation:
        print(evaluation)
        result = {
    "overall_score": evaluation.overall_score,
    "strengths": evaluation.summary_strengths,
    "weaknesses": evaluation.summary_weaknesses,
    "overall_fit_justification": evaluation.overall_fit_justification,
    "fit_for_role": evaluation.fit_with_role
}
       
        return result
    else:
        return {"error": "Failed to evaluate user answer"}
    
application = app


  