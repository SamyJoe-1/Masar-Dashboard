from langchain.prompts import ChatPromptTemplate
from langchain.output_parsers import PydanticOutputParser
from pydantic import BaseModel, Field
import os
from langchain_community.chat_models import ChatOpenAI
from dotenv import load_dotenv

from pydantic import BaseModel, Field
from typing import List
from langchain_community.chat_models import ChatOpenAI
from langchain.prompts import ChatPromptTemplate
from langchain.output_parsers import PydanticOutputParser


from typing import List
from pydantic import BaseModel, Field
from langchain_community.chat_models import ChatOpenAI
from langchain.prompts import ChatPromptTemplate
from langchain.output_parsers import PydanticOutputParser
load_dotenv()
openai_api_key = os.getenv("OPENAI_API_KEY")

def extract_questions (context : str , job_reqirments , language) -> list:
    # Define the Pydantic model
    class Query(BaseModel):
        updated_queries: list[str] = Field(
            description=(
           f"""
         extract the all tectnical question from the provided context as python list  
     """
            )
        )

        # Initialize the LLM (e.g., Gemini or PaLM model deployed in Vertex AI)
    llm= ChatOpenAI(
                model_name="gpt-4o",  # Or use "gpt-3.5-turbo", "gpt-4-turbo"
                temperature=0,
                openai_api_key=openai_api_key
            )


    # Initialize the Pydantic output parser
    output_parser = PydanticOutputParser(pydantic_object=Query)

    # Define the chat prompt template
    prompt = ChatPromptTemplate.from_template("""
You are a Senior HR Professional with expertise in conducting comprehensive interviews across all job functions and industries. 
Your task is to analyze the provided CV/Resume text and generate targeted interview questions that will effectively assess the candidate's competencies, experience depth, cultural fit, and role-specific abilities.

**ANALYSIS REQUIREMENTS:**
1. Identify the candidate's primary field/industry and role type
2. Examine all relevant skills (technical, soft, industry-specific, leadership)
3. Assess experience levels, achievements, and career progression
4. Note any gaps, career changes, or areas requiring clarification
                                              
5. Consider the scope of responsibilities and impact in previous roles


**ROLE CATEGORIZATION:**
Automatically identify the primary role category:
- Technical (Software, Engineering, IT, Data, etc.)
- Management/Leadership (Team Lead, Manager, Executive, etc.)
- Sales & Marketing (Sales, Marketing, Business Development, etc.)
- Creative (Design, Content, Marketing Creative, etc.)
- Operations (Project Management, Operations, Supply Chain, etc.)
- Finance & Accounting (Accounting, Finance, Audit, etc.)
- Human Resources (HR, Recruitment, Training, etc.)
- Customer Service (Support, Success, Relations, etc.)
- Healthcare (Medical, Nursing, Allied Health, etc.)
- Education (Teaching, Training, Academic, etc.)
- Other Specialized Fields

**QUESTION GENERATION GUIDELINES:**

**Universal Question Categories:**
1. **Role-Specific Expertise** - Core competencies for their field
2. **Experience Validation** - Verify claimed achievements and responsibilities questions from pervious work and pervious projects the questions about why you used this solution and not used other solutions  this type of questions should be generated  
4. **Problem-Solving** - Analytical thinking and challenge resolution ask him about the pervious work and pervious projects 
this question will be asked in the interview to check if him fit the the open posation or not 
questions language make it the same language of the input  
you must generate only 9  questions 
                                              
```  
--- cv context start  ---
{context}
--- cv CONTEXT END ---

here is thie job reqirments 
{job_reqirments}
                                              
____ end of job reqirments _______
                                              
the generated questions should be focus on the job reqirments 

output language should be {language}
```





    Instructions:
                                              
    {format_instructions}
                                              

""")
    # Format the prompt with the instructions and parser format
    formatted_prompt = prompt.format_messages(
        prompt_=prompt,
        format_instructions=output_parser.get_format_instructions() ,
        context=context, 
        job_reqirments =job_reqirments, 
        language=language
    )
    # Get the LLM response
    response = llm.invoke(formatted_prompt)

    # Parse the LLM's response
    parsed_response = output_parser.parse(response.content)

    # Print the search queries
    return parsed_response.updated_queries 







def rate_answer(questions_with_answers: str, job_requirements: str , language: str):
    # One evaluation for all Q&As
    class OverallEvaluation(BaseModel):
        overall_score: int = Field(description="Overall score from 0 to 10 for the candidate based on all answers.")
        summary_strengths: str = Field(description="Summary of the candidate's key strengths based on all answers.")
        summary_weaknesses: str = Field(description="Summary of the candidate's key weaknesses or areas for improvement.")
        overall_fit_justification: str = Field(description="A final verdict on whether the candidate is a good fit for the role, with a brief justification.")
        fit_with_role: bool = Field(description="A simple boolean indicating if the candidate is considered a fit for the role.")
    # Initialize LLM
    llm = ChatOpenAI(
        model_name="gpt-4o",
        temperature=0,
        openai_api_key=openai_api_key
    )

    # Output parser
    output_parser = PydanticOutputParser(pydantic_object=OverallEvaluation)
    # Prompt Template
    prompt = ChatPromptTemplate.from_template("""
You are a senior technical interviewer evaluating a candidate for a job based on their responses to all interview questions.
**Instructions:**
You will be given:
- A job description or job requirements.
- A set of interview questions with the candidate’s answers.

Your task is to provide a HOLISTIC evaluation of the candidate based on ALL of their answers. Synthesize the information from all responses to form a single, comprehensive assessment.

Provide an overall evaluation with the following components:
1. **Overall Score (0-10):** A single score reflecting the candidate's overall performance across all answers.
2. **Summary of Strengths:** What were the consistent strengths demonstrated across all answers?
3. **Summary of Weaknesses:** What were the common weaknesses or red flags observed in the answers?
4. **Overall Fit Justification:** A detailed explanation supporting the final decision on whether the candidate is a good fit.
5. **Fit with Role:** A boolean (`true` or `false`) indicating if the candidate is a good fit for the role.
Return a single structured evaluation object for the entire interview.

---

Job Requirements:
{job_requirements}



Interview Questions and Answers:
{questions_with_answers}

output language should be {language}

{format_instructions}

""")

    # Format the prompt
    formatted_prompt = prompt.format_messages(
        job_requirements=job_requirements,
   
        questions_with_answers=questions_with_answers,
        language=language,
        format_instructions=output_parser.get_format_instructions()
    )

    # LLM call
    response = llm.invoke(formatted_prompt)

    # Parse response
    parsed = output_parser.parse(response.content)
    return parsed
"""

questions_with_answers = [
    {
        "question": "Tell me about a time you optimized a system for performance.",
        "answer": "Yes, in my last role, I worked on improving a search system. We moved from MySQL to Elasticsearch, and performance improved significantly."
    },
    {
        "question": "How do you handle conflict in a team?",
        "answer": "I usually just avoid it and try to do my own work."
    }
]
"""
job_requirements = """
Looking for a backend engineer with deep experience in system performance optimization, distributed systems, and team collaboration.
"""
"""
evaluations = rate_answer(str(questions_with_answers), job_requirements , "English")

for e in evaluations:
    print(f"Question: {e.question}\nAnswer: {e.answer}\nScore: {e.score}\nStrengths: {e.strengths}\nWeaknesses: {e.weaknesses}\nFit: {e.fit_with_role}\n")
"""
