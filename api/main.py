# main.py
from fatsecret_client import get_access_token, search_foods, get_food_by_id
from utils import normalize_food_list, print_foods


def main():
    token = get_access_token()

    search_response = search_foods(token, "chicken")
    foods = normalize_food_list(search_response)

    print("Search results:")
    print_foods(foods)

    if foods:
        first_food_id = foods[0]["food_id"]
        details = get_food_by_id(token, first_food_id)

        print("\nFirst food details:")
        print(details)
    


if __name__ == "__main__":
    main()