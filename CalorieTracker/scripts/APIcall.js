document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://127.0.0.1:5001';
    const searchForm = document.getElementById('search_box')?.closest('form');
    const resultsContainer = document.getElementById('search-results');
    const minCal = document.getElementById('minCal');
    const maxCal = document.getElementById('maxCal');

    if (!searchForm || !resultsContainer) return;

    searchForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const query = document.getElementById('search_box').value.trim();
        if (!query) return;

        resultsContainer.innerHTML = '<p class="search-status">Searching...</p>';

        try {
            const res = await fetch(`${API_BASE_URL}/search?q=${encodeURIComponent(query)}`);
            let data = null;

            try {
                data = await res.json();
            } catch {
                throw { error_type: 'invalid_response', error: 'The API returned an invalid response.' };
            }

            if (!res.ok || data.error) {
                throw data;
            }

            let foods = data.foods || [];
            const totalFromApi = foods.length;

            if (minCal.value !== '' && maxCal.value !== '') {
                foods = foods.filter((food) => {
                    return food.calories >= minCal.value && food.calories <= maxCal.value;
                });
            }

            if (totalFromApi > 0 && foods.length === 0) {
                showStatus(
                    'No results matched your calorie filter. Try widening the From/To range or clear the filter.',
                    'search-status-warn'
                );
                return;
            }

            renderSearchResults(foods);

            if (foods.length > 0) {
                await saveFoodItems(foods);
            }
        } catch (err) {
            showStatus(getErrorMessage(err), 'search-status-error');
        }
    });

    function getErrorMessage(err) {
        if (!err) {
            return 'Something went wrong. Please try again.';
        }

        if (err instanceof TypeError || err.name === 'TypeError') {
            return (
                'Cannot reach the food API server. Start it with: cd api && python app.py ' +
                `(expected at ${API_BASE_URL})`
            );
        }

        const type = err.error_type || '';
        const message = err.error || err.message || '';

        if (type === 'ip_not_allowed') {
            return message || (
                'FatSecret blocked the request because your IP is not whitelisted. ' +
                'Open http://127.0.0.1:5001/diagnostics to see the IP to add in your FatSecret developer account.'
            );
        }

        if (type === 'auth_failed') {
            return message || 'FatSecret credentials are invalid. Check your .env file in the api folder.';
        }

        if (type === 'network_error') {
            return message || 'Could not connect to FatSecret. Check your internet connection.';
        }

        if (type === 'invalid_query') {
            return message || 'Please enter a search term.';
        }

        if (type === 'server_error' || type === 'api_error') {
            return message || 'The food API returned an error. Check the Flask server terminal for details.';
        }

        if (message) {
            return message;
        }

        return 'Search failed. Make sure the API server is running (cd api && python app.py).';
    }

    function showStatus(message, className = 'search-status') {
        resultsContainer.innerHTML = `<p class="${className}">${escapeHtml(message)}</p>`;
    }

    function renderSearchResults(foods) {
        if (!foods || foods.length === 0) {
            showStatus('No results found for that search. Try a different food name.');
            return;
        }

        resultsContainer.innerHTML = `
            <h2 class="search-results-title">Search Results</h2>
            ${foods.map((food) => buildFoodResultHtml(food)).join('')}
        `;
    }

    function buildFoodThumb(food) {
        const name = escapeHtml(food.food_name || '');
        if (food.image_url) {
            return `<img src="${escapeAttr(food.image_url)}" alt="" class="food-thumb" loading="lazy">`;
        }
        const initial = (food.food_name || '?').charAt(0).toUpperCase();
        return `<span class="food-thumb food-thumb-placeholder" aria-hidden="true">${escapeHtml(initial)}</span>`;
    }

    function buildFoodResultHtml(food) {
        const foodName = food.food_name || '';
        const name = escapeHtml(foodName);
        const calories = food.calories !== null && food.calories !== undefined ? food.calories : 0;
        const caloriesDisplay = Number.isInteger(calories)
            ? calories
            : Math.round(calories * 10) / 10;
        const servingSize = food.serving_size || 'N/A';
        const servingSizeEscaped = escapeHtml(servingSize);

        return `
            <div class="food-result">
                <span class="food-col-img">${buildFoodThumb(food)}</span>
                <span class="food-name" title="${name}">${name}</span>
                <span class="food-serving-size">${servingSizeEscaped}</span>
                <span class="food-calories">${caloriesDisplay} cal/serving</span>
                <form class="add-food-form" action="./index.php?action=add_to_goal" method="POST">
                    <input type="hidden" name="food_name" value="${escapeAttr(foodName)}">
                    <input type="hidden" name="calories_per_serving" value="${calories}">
                    <input type="hidden" name="serving_size" value="${escapeAttr(food.serving_size || '')}">
                    <input type="hidden" name="food_item_id" value="${escapeAttr(food.food_id || '')}">
                    <input type="hidden" name="image_url" value="${escapeAttr(food.image_url || '')}">
                    <label class="servings-label">
                        <span class="servings-text">Servings</span>
                        <input class="servings-input" type="number" name="servings" value="1" min="0.25" step="0.25" required>
                    </label>
                    <button type="submit" class="button addTo">Add</button>
                </form>
            </div>
        `;
    }

    async function saveFoodItems(foods) {
        try {
            const res = await fetch('./index.php?action=save_food_items', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ foods })
            });

            if (!res.ok) throw new Error('Failed to save food items');
            const data = await res.json();
            if (data.error) throw new Error(data.error);
        } catch (err) {
            console.error('Could not save food items:', err);
        }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escapeAttr(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;');
    }
});
