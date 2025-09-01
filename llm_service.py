import openai
from config import OPENAI_API_KEY

# Detect if new client API is available
try:
    from openai import OpenAI
    client = OpenAI(api_key=OPENAI_API_KEY)

    def call_gpt(prompt: str, model: str = "gpt-4o") -> str:
        """
        استدعاء GPT-4 لتحليل السير الذاتية (new API style)
        """
        try:
            response = client.chat.completions.create(
                model=model,
                messages=[
                    {"role": "system", "content": "You are a helpful resume analysis assistant."},
                    {"role": "user", "content": prompt}
                ],
                temperature=0.3,
                max_tokens=1500
            )
            return response.choices[0].message.content.strip()
        except Exception as e:
            print(f"❌ GPT Error: {e}")
            return ""

except ImportError:
    # Old API style fallback
    openai.api_key = OPENAI_API_KEY

    def call_gpt(prompt: str, model: str = "gpt-4") -> str:
        """
        استدعاء GPT-4 لتحليل السير الذاتية (old API style)
        """
        try:
            response = openai.ChatCompletion.create(
                model=model,
                messages=[
                    {"role": "system", "content": "You are a helpful resume analysis assistant."},
                    {"role": "user", "content": prompt}
                ],
                temperature=0.3,
                max_tokens=1500
            )
            return response.choices[0].message["content"].strip()
        except Exception as e:
            print(f"❌ GPT Error: {e}")
            return ""
