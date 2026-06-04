document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('search_box')?.closest('form');
    const resultsContainer = document.getElementById('search-results');

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
                <span class="food-name">${escapeHtml(f.food_name)}</span>
                <span class="food-calories">${f.calories !== null ? f.calories + ' kcal' : 'N/A'}</span>
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
