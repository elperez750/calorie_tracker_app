import requests
from config import CLIENT_ID, CLIENT_SECRET, TOKEN_URL, SEARCH_URL, FOOD_URL, SCOPE


def get_access_token():
    response = requests.post(
        TOKEN_URL,
        data={
            "grant_type": "client_credentials",
            "scope": SCOPE,
            "client_id": CLIENT_ID,
            "client_secret": CLIENT_SECRET,
        },
        timeout=30,
    )
    response.raise_for_status()
    return response.json()["access_token"]


def search_foods(access_token, query, page_number=0, max_results=5):
    response = requests.get(
        SEARCH_URL,
        headers={"Authorization": f"Bearer {access_token}"},
        params={
            "search_expression": query,
            "format": "json",
            "page_number": page_number,
            "max_results": max_results,
        },
        timeout=30,
    )
    response.raise_for_status()
    return response.json()


def get_food_by_id(access_token, food_id):
    response = requests.get(
        FOOD_URL,
        headers={"Authorization": f"Bearer {access_token}"},
        params={
            "food_id": food_id,
            "format": "json",
        },
        timeout=30,
    )
    response.raise_for_status()
    return response.json()