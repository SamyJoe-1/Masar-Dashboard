import os
import base64
import asyncio
from concurrent.futures import ThreadPoolExecutor, as_completed
from langchain_core.runnables.utils import Output
from tqdm import tqdm
from langchain_google_vertexai import ChatVertexAI
from langchain_core.messages import HumanMessage
from dotenv import load_dotenv
import fitz  # PyMuPDF
from langchain_community.chat_models import ChatOpenAI
load_dotenv()


class PDFTextExtraction:
    def __init__(self, pdf_path: str , system_prompt , openai=True):
        self.system_prompt=system_prompt
        self.pdf_path = pdf_path
        self.image_proc_prompt = "Extract the text from the image"
        if openai:
            # Setup OpenAI LLM
            openai_api_key = os.getenv("OPENAI_API_KEY")
            self.llm= ChatOpenAI(
                model_name="gpt-4o",  # Or use "gpt-3.5-turbo", "gpt-4-turbo"
                temperature=0,
                openai_api_key=openai_api_key
            )
            self.vlm= ChatOpenAI(
                model_name="gpt-4o",  # Or use "gpt-3.5-turbo", "gpt-4-turbo"
                temperature=0,
                openai_api_key=openai_api_key
            )
            

        else:
            # Load environment variables
            os.environ['GOOGLE_APPLICATION_CREDENTIALS'] = os.environ["GOOGLE_APPLICATION_CREDENTIALS"]
            self.llm = ChatVertexAI(
                model="gemini-2.5-pro",
                temperature=0,
            )
            
            self.vlm = ChatVertexAI(
                model="gemini-2.5-pro",
                temperature=0,
            )

        self.image_paths = []
        self.extracted_data = ""
        self.results_dict = {}
        self.lock = asyncio.Lock()

    def convert_pdf_to_images(self, output_dir="extracted_images", zoom=2):
        """Optimized PDF to image conversion with better memory management"""
        if not os.path.exists(output_dir):
            os.makedirs(output_dir)
        
        doc = fitz.open(self.pdf_path)
        mat = fitz.Matrix(zoom, zoom)
        
        # Process pages in batches to manage memory
        batch_size = 10
        total_pages = len(doc)
        
        for batch_start in range(0, total_pages, batch_size):
            batch_end = min(batch_start + batch_size, total_pages)
            
            for page_num in range(batch_start, batch_end):
                page = doc.load_page(page_num)
                pix = page.get_pixmap(matrix=mat, alpha=False, colorspace=fitz.csRGB)
                image_path = os.path.join(output_dir, f"page_{page_num + 1}.png")
                pix.save(image_path)
                self.image_paths.append(image_path)
                print(f"✅ Saved: {image_path}")
                
                # Clean up memory
                pix = None
                page = None
        
        doc.close()

    async def process_image_async(self, index, path, prompt):
        """Async version of image processing"""
        try:
            # Read image in executor to avoid blocking
            loop = asyncio.get_event_loop()
            
            def read_and_encode():
                with open(path, "rb") as image_file:
                    return base64.b64encode(image_file.read()).decode("utf-8")
            
            encoded_image = await loop.run_in_executor(None, read_and_encode)
            
            message_local = HumanMessage(
                content=[
                    {"type": "text", "text": prompt},
                    {"type": "image_url", "image_url": {"url": f"data:image/png;base64,{encoded_image}"}},
                ]
            )
            
            # Use async invoke if available, otherwise run in executor
            def invoke_model():
                return self.vlm.invoke([message_local])
            
            result_local = await loop.run_in_executor(None, invoke_model)
            return index, result_local.content
            
        except Exception as e:
            print(f" Error processing image {index}: {str(e)}")
            return index, ""

    async def process_images_async_parallel(self, prompt, max_concurrent=20):
        """Fully async image processing with semaphore for rate limiting"""
        if not self.image_paths:
            return ""
        
        # Use semaphore to limit concurrent requests
        semaphore = asyncio.Semaphore(max_concurrent)
        
        async def process_with_semaphore(index, path):
            async with semaphore:
                return await self.process_image_async(index, path, prompt)
        
        # Create all tasks
        tasks = [
            process_with_semaphore(i, path)
            for i, path in enumerate(self.image_paths)
        ]
        
        # Process with progress bar
        output = [""] * len(self.image_paths)
        
        with tqdm(total=len(tasks), desc="Processing Images", ncols=80) as pbar:
            for coro in asyncio.as_completed(tasks):
                index, text = await coro
                output[index] = text
                pbar.update(1)
        
        self.extracted_data = "\n\n".join(output)
        return self.extracted_data

    def process_images_parallel(self, prompt, max_workers=25):
        """Optimized threaded version with better worker management"""
        if not self.image_paths:
            return ""
        
        output = [""] * len(self.image_paths)
        
        # Optimize worker count based on number of images
        optimal_workers = min(max_workers, len(self.image_paths), os.cpu_count() * 2)
        
        with ThreadPoolExecutor(max_workers=optimal_workers) as executor:
            futures = {
                executor.submit(self.process_image, i, path, prompt): i
                for i, path in enumerate(self.image_paths)
            }
            
            with tqdm(total=len(futures), desc="Processing Images", ncols=80) as pbar:
                for future in as_completed(futures):
                    try:
                        index, text = future.result()
                        output[index] = text
                    except Exception as e:
                        print(f"Error in thread: {str(e)}")
                        output[futures[future]] = ""
                    pbar.update(1)
        
        self.extracted_data = "\n\n".join(output)
        return self.extracted_data

    def process_image(self, index, path, prompt):
        """Optimized image processing with error handling"""
        try:
            with open(path, "rb") as image_file:
                encoded_image = base64.b64encode(image_file.read()).decode("utf-8")

            message_local = HumanMessage(
                content=[
                    {"type": "text", "text": prompt},
                    {"type": "image_url", "image_url": {"url": f"data:image/png;base64,{encoded_image}"}},
                ]
            )

            result_local = self.vlm.invoke([message_local])
            return index, result_local.content
        except Exception as e:
            print(f"❌ Error processing image {index}: {str(e)}")
            return index, ""

    async def extract_item(self, item, description):
        # Fix the backslash issue by extracting the string operations outside the f-string
        cleaned_description = description.replace("\n", " ").strip()
        
        prompt_text = f"""
        You are an information extraction assistant.

        Below is a context extracted from a document:

        --- CONTEXT START ---
        {self.extracted_data}
        --- CONTEXT END ---

        Based on the above context, extract the following information:

        - {item} for all classes  
        This is the description of the item you need to extract:  
        {cleaned_description}
        """
        messages = [
            ("system", "You are a helpful assistant."),
            ("human", prompt_text)
        ]
        # Run sync llm.invoke() in a thread to avoid blocking
        result = await asyncio.to_thread(self.llm.invoke, messages)
        return item, result.content if hasattr(result, 'content') else result

    async def extract_without_classes(self, prompts: dict):
        tasks = [
            self.extract_item(item, desc)
            for item, desc in prompts.items()
        ]
        results = await asyncio.gather(*tasks)
        return {item: content for item, content in results}