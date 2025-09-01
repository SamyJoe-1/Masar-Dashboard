import time
import aiohttp
import aiofiles
from urllib.parse import urlparse
from fastapi import FastAPI, UploadFile, File, HTTPException
import shutil
import uuid
import os
import sys


app = FastAPI()

@app.get("/")
async def root():
    return {"message": "FastAPI is working!"}

@app.get("/index")
async def index():
    return {"message": "Hello World"}

# Add your other routes here...