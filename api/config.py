# config.py
import os
from dotenv import load_dotenv, find_dotenv

load_dotenv(find_dotenv())

CLIENT_ID = os.getenv("FATSECRET_CLIENT_ID")
CLIENT_SECRET = os.getenv("FATSECRET_CLIENT_SECRET")

TOKEN_URL = "https://oauth.fatsecret.com/connect/token"
SEARCH_URL = "https://platform.fatsecret.com/rest/foods/search/v1"
FOOD_URL = "https://platform.fatsecret.com/rest/food/v4"
SCOPE = "basic"