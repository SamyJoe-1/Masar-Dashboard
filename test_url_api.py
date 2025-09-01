import requests
import json

# Base URL for your FastAPI application
BASE_URL = "http://127.0.0.1:8000"

def test_pdf_url_endpoint():
    """Test the PDF URL processing endpoint"""
    
    # Example PDF URL (you can replace with any valid PDF URL)
    test_pdf_url = "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf"
    
    # Test data
    test_data = {
        "pdf_url": test_pdf_url,
        "job_description": "Software Engineer position requiring Python, FastAPI, and API development skills",
        "language": "English"
    }
    
    print("Testing PDF URL endpoint...")
    print(f"PDF URL: {test_pdf_url}")
    print(f"Job Description: {test_data['job_description']}")
    print(f"Language: {test_data['language']}")
    print("-" * 50)
    
    try:
        # Test the new URL endpoint with language parameter
        response = requests.post(f"{BASE_URL}/extract-questions-from-cv-url/", data=test_data)
        
        print(f"Status Code: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            print("✅ Success!")
            print(f"Message: {result.get('message', 'No message')}")
            print(f"Elapsed Times: {json.dumps(result.get('elapsed_times', {}), indent=2)}")
            print(f"Structured Data: {json.dumps(result.get('structured_data', {}), indent=2)}")
        else:
            print("❌ Error!")
            print(f"Error: {response.text}")
            
    except requests.exceptions.ConnectionError:
        print("❌ Connection Error!")
        print("Make sure your FastAPI server is running on http://127.0.0.1:8000")
    except Exception as e:
        print(f"❌ Unexpected Error: {str(e)}")

def test_pdf_url_arabic_endpoint():
    """Test the Arabic PDF URL processing endpoint"""
    
    # Example PDF URL
    test_pdf_url = "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf"
    
    # Test data for Arabic
    test_data = {
        "pdf_url": test_pdf_url,
        "job_description": "مطور برمجيات يتطلب خبرة في Python وFastAPI وتطوير واجهات برمجة التطبيقات"
    }
    
    print("\nTesting PDF URL Arabic endpoint...")
    print(f"PDF URL: {test_pdf_url}")
    print(f"Job Description (Arabic): {test_data['job_description']}")
    print("-" * 50)
    
    try:
        # Test the Arabic URL endpoint
        response = requests.post(f"{BASE_URL}/extract-questions-from-cv-url_ar/", data=test_data)
        
        print(f"Status Code: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            print("✅ Success!")
            print(f"Message: {result.get('message', 'No message')}")
            print(f"Elapsed Times: {json.dumps(result.get('elapsed_times', {}), indent=2)}")
            print(f"Structured Data: {json.dumps(result.get('structured_data', {}), indent=2)}")
        else:
            print("❌ Error!")
            print(f"Error: {response.text}")
            
    except requests.exceptions.ConnectionError:
        print("❌ Connection Error!")
        print("Make sure your FastAPI server is running on http://127.0.0.1:8000")
    except Exception as e:
        print(f"❌ Unexpected Error: {str(e)}")

if __name__ == "__main__":
    print("=== PDF URL API Testing ===")
    test_pdf_url_endpoint()
    test_pdf_url_arabic_endpoint()
    print("\n=== Testing Complete ===") 