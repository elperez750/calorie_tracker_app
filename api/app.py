from flask import Flask, request, jsonify
from flask_cors import CORS
from fatsecret_client import get_access_token, search_foods
from utils import normalize_food_list

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


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5001, debug=True)
