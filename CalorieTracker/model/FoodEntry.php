<?php

class FoodEntry {
    public $id;
    public $foodItemId;
    public $foodName;
    public $calories;
    public $servingSize;
    public $dateLogged;
    public $servings;
    public $caloriesPerServing;
    public $imageUrl;
    public $category;

    public function __construct(
        $id,
        $foodItemId,
        $foodName,
        $calories,
        $servingSize,
        $dateLogged,
        $servings,
        $caloriesPerServing,
        $imageUrl = null,
        $category = ''
    ) {
        $this->id = (string) $id;
        $this->foodItemId = $foodItemId !== null ? (string) $foodItemId : null;
        $this->foodName = $foodName;
        $this->calories = (int) $calories;
        $this->servingSize = $servingSize ?? '';
        $this->dateLogged = $dateLogged;
        $this->servings = (float) $servings;
        $this->caloriesPerServing = (float) $caloriesPerServing;
        $this->imageUrl = $imageUrl;
        $this->category = $category ?? '';
    }

    public static function fromRow(array $row) {
        return new FoodEntry(
            $row['food_entry_id'],
            $row['food_item_id'],
            $row['food_name'],
            $row['calories'],
            $row['serving_size'],
            $row['date_logged'],
            $row['servings'],
            $row['calories_per_serving'],
            $row['image_url'] ?? null,
            $row['category'] ?? ''
        );
    }
}
