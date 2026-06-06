# utils.py
import re


def extract_image_url(food):
    images = food.get("food_images")
    if not images:
        return None

    food_image = images.get("food_image") if isinstance(images, dict) else None
    if not food_image:
        return None

    if isinstance(food_image, dict):
        return food_image.get("image_url")

    if isinstance(food_image, list):
        preferred = None
        for image in food_image:
            if not isinstance(image, dict):
                continue
            url = image.get("image_url")
            if not url:
                continue
            if "72x72" in url or "400x400" in url:
                return url
            if preferred is None:
                preferred = url
        return preferred

    return None


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
    serving_re = re.compile(r"^Per\s+(.+?)\s*-\s*Calories:", re.IGNORECASE)

    for f in items:
        desc = f.get("food_description", "") or ""
        calories = None
        serving_size = ""

        m = cal_re.search(desc)
        if m:
            try:
                calories = float(m.group(1))
            except ValueError:
                calories = None

        serving_match = serving_re.search(desc)
        if serving_match:
            serving_size = serving_match.group(1).strip()

        foods.append({
            "food_id": f.get("food_id"),
            "food_name": f.get("food_name"),
            "food_description": desc,
            "food_url": f.get("food_url"),
            "calories": calories,
            "serving_size": serving_size,
            "category": f.get("food_type", ""),
            "image_url": extract_image_url(f),
        })

    return foods


def print_foods(foods):
    for food in foods:
        print(food)
        name = food.get("food_name", "Unknown")
        calories = food.get("calories", "N/A")
        print(f"{name} ({name}): {calories} calories")
