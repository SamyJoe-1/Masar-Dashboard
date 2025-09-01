# config.py
import os
from dotenv import load_dotenv

# تحميل القيم من ملف .env
load_dotenv()

# قراءة API Key من .env
OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")

if not OPENAI_API_KEY:
    raise ValueError("❌ لم يتم العثور على OPENAI_API_KEY في ملف .env")
