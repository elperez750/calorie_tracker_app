# utils.py
import re

def normalize_food_list(search_response):
    """
    Convert FatSecret search response to a list of food dicts with a 'calories' field.
    Falls back to parsing calories from food_description if details aren't fetched.
    """
    foods = []
    items = search_response.get("foods", {}).get("food", []) or []
    if isinstance(items, dict):
        items = [items]
    cal_re = re.compile(r"Calories:\s*([\d.]+)\s*kcal", re.IGNORECASE)

    for f in items:
        desc = f.get("food_description", "") or ""
        calories = None

        # try parse from description like: "Per 101g - Calories: 197kcal | ..."
        m = cal_re.search(desc)
        if m:
            try:
                calories = float(m.group(1))
            except ValueError:
                calories = None

        foods.append({
            "food_id": f.get("food_id"),
            "food_name": f.get("food_name"),
            "food_description": desc,
            "food_url": f.get("food_url"),
            "calories": calories,  # may be None
        })
        
    

    return foods


def print_foods(foods):
    for food in foods:
        print(food)
        name = food.get("food_name", "Unknown")
        calories = food.get("calories", "N/A")
        print(f"{name} ({name}): {calories} calories")