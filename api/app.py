import sys
from flask import Flask, request, jsonify
from flask_cors import CORS
from fatsecret_client import get_access_token, search_foods
from utils import normalize_food_list
from config import CLIENT_ID, CLIENT_SECRET

app = Flask(__name__)
CORS(app)


@app.route("/search")
def search():
    query = request.args.get("q", "").strip()
    if not query:
        return jsonify({"foods": [], "error": "No query provided"}), 400

    token = get_access_token()
    search_response = search_foods(token, query)
    foods = normalize_food_list(search_response)
    return jsonify({"foods": foods})


@app.route("/health")
def health():
    return jsonify({"status": "ok"})


if __name__ == "__main__":
    if not CLIENT_ID or not CLIENT_SECRET:
        print("\n ERROR: Missing credentials.")
        print(" Make sure you have a .env file with:")
        print("   FATSECRET_CLIENT_ID=...")
        print("   FATSECRET_CLIENT_SECRET=...")
        print(" Place the .env file in the api/ folder or the project root.\n")
        sys.exit(1)

    print(f"\n Credentials loaded. Starting server on http://127.0.0.1:5001\n")
    app.run(host="127.0.0.1", port=5001, debug=True)
