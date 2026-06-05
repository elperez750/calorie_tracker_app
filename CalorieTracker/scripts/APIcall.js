document.addEventListener('DOMContentLoaded', () => {
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
            const res = await fetch(`http://127.0.0.1:5001/search?q=${encodeURIComponent(query)}`);
            if (!res.ok) throw new Error('Search failed');
            const data = await res.json();
            
            if(minCal.value !== '' && maxCal.value !== ''){
                for(let i = data.foods.length - 1; i >= 0; i--){
                    if(data.foods[i].calories < minCal.value || data.foods[i].calories > maxCal.value){
                        data.foods.splice(i, 1);
                    }
                }
            }
            renderResults(data.foods);
        } catch (err) {
            resultsContainer.innerHTML = '<p class="search-status">Error fetching results. Make sure the API server is running.</p>';
        }
    });

    function renderResults(foods) {
        if (!foods || foods.length === 0) {
            resultsContainer.innerHTML = '<p class="search-status">No results found.</p>';
            return;
        }

        const html = foods.map(f => `
            <div class="food-result">
                <a class="button addTo" href="./index.php?action=add_to_goal&food_name=${escapeHtml(f.food_name)}&calories=${f.calories !== null ? f.calories: null}">Add to Goal</a>
                <span class="food-name">${escapeHtml(f.food_name)}</span>
                <span class="food-calories">${f.calories !== null ? f.calories + ' cal' : 'N/A'}</span>
            </div>
        `).join('');

        resultsContainer.innerHTML = html;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
