import re

import requests
from config import CLIENT_ID, CLIENT_SECRET, TOKEN_URL, SEARCH_URL, SEARCH_URL_FALLBACK, FOOD_URL, SCOPE


class FatSecretAPIError(Exception):
    def __init__(self, code, message, error_type="api_error"):
        self.code = int(code) if code is not None else None
        self.message = message
        self.error_type = error_type
        super().__init__(message)


def get_api_error_info(code, message):
    code = int(code) if code is not None else None
    message = message or "Unknown FatSecret API error."

    if code == 21:
        ip_match = re.search(r"'([^']+)'", message)
        ip = ip_match.group(1) if ip_match else None
        detail = (
            f"Add {ip} to IP Restrictions in your FatSecret developer account "
            "(Manage API Keys). Your IP may change on campus Wi‑Fi — use a range "
            "like 72.233.242.0/24 or 0.0.0.0/0 for development only."
            if ip
            else "Add your server's public IP to IP Restrictions in your FatSecret developer account."
        )
        return "ip_not_allowed", f"FatSecret blocked this request: IP not whitelisted. {detail}"

    if code == 14:
        return "premier_required", "FatSecret premier scope is required for that API version."

    if code in (2, 8):
        return "auth_failed", "Invalid FatSecret API credentials. Check FATSECRET_CLIENT_ID and FATSECRET_CLIENT_SECRET in your .env file."

    if code == 101:
        return "no_results", "No foods matched that search on FatSecret."

    return "api_error", f"FatSecret API error ({code}): {message}"


def _parse_api_error(data):
    if not isinstance(data, dict):
        return None, None

    error = data.get("error")
    if not error:
        return None, None

    if isinstance(error, dict):
        return error.get("code"), error.get("message")

    return None, str(error)


def _request_json(url, headers, params):
    response = requests.get(url, headers=headers, params=params, timeout=30)
    response.raise_for_status()
    return response.json()


def get_access_token():
    try:
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
        data = response.json()
    except requests.RequestException as exc:
        raise FatSecretAPIError(
            None,
            "Could not connect to FatSecret to get an access token. Check your internet connection.",
            "network_error",
        ) from exc

    code, message = _parse_api_error(data)
    if code is not None:
        error_type, friendly = get_api_error_info(code, message)
        raise FatSecretAPIError(code, friendly, error_type)

    token = data.get("access_token")
    if not token:
        raise FatSecretAPIError(None, "FatSecret did not return an access token.", "auth_failed")

    return token


def search_foods_v1(access_token, query, page_number=0, max_results=5):
    headers = {"Authorization": f"Bearer {access_token}"}
    params = {
        "search_expression": query,
        "format": "json",
        "page_number": page_number,
        "max_results": max_results,
    }

    try:
        data = _request_json(SEARCH_URL_FALLBACK, headers, params)
    except requests.RequestException as exc:
        raise FatSecretAPIError(
            None,
            "Could not reach FatSecret search API. Check your internet connection.",
            "network_error",
        ) from exc

    code, message = _parse_api_error(data)
    if code is not None:
        error_type, friendly = get_api_error_info(code, message)
        raise FatSecretAPIError(code, friendly, error_type)

    return data


def search_foods(access_token, query, page_number=0, max_results=5):
    headers = {"Authorization": f"Bearer {access_token}"}
    base_params = {
        "search_expression": query,
        "format": "json",
        "page_number": page_number,
        "max_results": max_results,
    }

    try:
        data = _request_json(
            SEARCH_URL,
            headers,
            {**base_params, "include_food_images": "true"},
        )
        code, message = _parse_api_error(data)
        if code is not None:
            if code == 14:
                return search_foods_v1(access_token, query, page_number, max_results)
            error_type, friendly = get_api_error_info(code, message)
            raise FatSecretAPIError(code, friendly, error_type)
        return data
    except requests.RequestException:
        return search_foods_v1(access_token, query, page_number, max_results)


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


def get_public_ip():
    try:
        response = requests.get("https://api.ipify.org?format=json", timeout=5)
        response.raise_for_status()
        return response.json().get("ip")
    except requests.RequestException:
        return None
