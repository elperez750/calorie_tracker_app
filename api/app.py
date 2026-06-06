import sys

from flask import Flask, request, jsonify
from flask_cors import CORS
from fatsecret_client import (
    FatSecretAPIError,
    get_access_token,
    get_public_ip,
    search_foods,
)
from utils import normalize_food_list
from config import CLIENT_ID, CLIENT_SECRET

app = Flask(__name__)
CORS(app)


def api_error_response(exc, status=502):
    return jsonify({
        "foods": [],
        "error": exc.message,
        "error_code": exc.code,
        "error_type": exc.error_type,
    }), status


@app.route("/search")
def search():
    query = request.args.get("q", "").strip()
    if not query:
        return jsonify({
            "foods": [],
            "error": "Please enter a search term.",
            "error_type": "invalid_query",
        }), 400

    try:
        token = get_access_token()
        search_response = search_foods(token, query)
        foods = normalize_food_list(search_response)
        return jsonify({"foods": foods})
    except FatSecretAPIError as exc:
        status = 403 if exc.error_type == "ip_not_allowed" else 502
        if exc.error_type == "auth_failed":
            status = 401
        return api_error_response(exc, status)
    except Exception as exc:
        return jsonify({
            "foods": [],
            "error": f"Unexpected server error: {exc}",
            "error_type": "server_error",
        }), 500


@app.route("/health")
def health():
    return jsonify({"status": "ok"})


@app.route("/diagnostics")
def diagnostics():
    result = {
        "credentials_loaded": bool(CLIENT_ID and CLIENT_SECRET),
        "public_ip": get_public_ip(),
        "token_ok": False,
        "search_ok": False,
        "message": "",
    }

    if not result["credentials_loaded"]:
        result["message"] = "Missing FATSECRET_CLIENT_ID or FATSECRET_CLIENT_SECRET in .env"
        return jsonify(result), 500

    try:
        token = get_access_token()
        result["token_ok"] = True
        search_response = search_foods(token, "apple")
        foods = normalize_food_list(search_response)
        result["search_ok"] = len(foods) > 0
        result["sample_count"] = len(foods)
        if not result["search_ok"]:
            result["message"] = "Connected to FatSecret but test search returned no foods."
        else:
            result["message"] = "API is working."
    except FatSecretAPIError as exc:
        result["message"] = exc.message
        result["error_type"] = exc.error_type
        result["error_code"] = exc.code

    status = 200 if result.get("search_ok") else 503
    return jsonify(result), status


if __name__ == "__main__":
    if not CLIENT_ID or not CLIENT_SECRET:
        print("\n ERROR: Missing credentials.")
        print(" Make sure you have a .env file with:")
        print("   FATSECRET_CLIENT_ID=...")
        print("   FATSECRET_CLIENT_SECRET=...")
        print(" Place the .env file in the api/ folder or the project root.\n")
        sys.exit(1)

    public_ip = get_public_ip()
    print("\n Credentials loaded. Starting server on http://127.0.0.1:5001")
    if public_ip:
        print(f" Whitelist this IP in FatSecret if searches fail: {public_ip}")
    print(" Run http://127.0.0.1:5001/diagnostics to test the API.\n")
    app.run(host="127.0.0.1", port=5001, debug=True)
