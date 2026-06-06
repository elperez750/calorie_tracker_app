document.addEventListener('DOMContentLoaded', () => {
    const progressBar = document.getElementById('progress-bar');
    const totalCaloriesEl = document.getElementById('total-calories');
    const foodList = document.getElementById('food-list');
    const goalBlock = document.querySelector('.calorie-goal-block');
    const calorieGoal = goalBlock ? parseFloat(goalBlock.dataset.calorieGoal) || 0 : 0;

    document.querySelectorAll('.servings-edit').forEach((input) => {
        let lastValid = input.value;

        input.addEventListener('focus', () => {
            lastValid = input.value;
            input.select();
        });

        input.addEventListener('change', () => updateServings(input, lastValid));
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            }
        });
    });

    document.querySelectorAll('.delete-entry-btn').forEach((button) => {
        button.addEventListener('click', () => deleteEntry(button));
    });

    async function updateServings(input, lastValid) {
        const servings = parseFloat(input.value);
        if (!servings || servings <= 0) {
            input.value = lastValid;
            return;
        }

        const entryId = input.dataset.entryId;
        const row = input.closest('.food-row');
        const caloriesEl = row?.querySelector('.food-calories-value');
        const caloriesPerServing = parseFloat(input.dataset.caloriesPerServing) || 0;

        const newCalories = Math.round(caloriesPerServing * servings);
        if (caloriesEl) {
            caloriesEl.textContent = newCalories;
        }

        updateTotals();

        try {
            const formData = new FormData();
            formData.append('entry_id', entryId);
            formData.append('servings', servings);

            const res = await fetch('./index.php?action=update_servings', {
                method: 'POST',
                body: formData
            });

            if (!res.ok) throw new Error('Update failed');

            const data = await res.json();
            if (data.error) throw new Error(data.error);

            applyTotals(data.total_calories, data.percent);
            if (caloriesEl) {
                caloriesEl.textContent = data.calories;
            }
        } catch {
            input.value = lastValid;
            if (caloriesEl) {
                caloriesEl.textContent = Math.round(caloriesPerServing * parseFloat(lastValid));
            }
            updateTotals();
        }
    }

    async function deleteEntry(button) {
        const entryId = button.dataset.entryId;
        const row = button.closest('.food-row');
        if (!entryId || !row) return;

        const foodName = row.querySelector('.food-name-text')?.textContent?.trim() || 'this food';
        if (!window.confirm(`Remove ${foodName} from today's log?`)) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('entry_id', entryId);

            const res = await fetch('./index.php?action=delete_entry', {
                method: 'POST',
                body: formData
            });

            if (!res.ok) throw new Error('Delete failed');

            const data = await res.json();
            if (data.error) throw new Error(data.error);

            row.remove();
            applyTotals(data.total_calories, data.percent);

            if (foodList && !foodList.querySelector('.food-row:not(.food-row-header)')) {
                window.location.reload();
            }
        } catch {
            alert('Could not remove that food. Please try again.');
        }
    }

    function applyTotals(total, percent) {
        if (totalCaloriesEl) {
            totalCaloriesEl.textContent = total;
        }
        if (progressBar) {
            progressBar.style.backgroundImage =
                `linear-gradient(90deg, #81c979 ${percent}%, #413d47 ${percent}%)`;
        }
    }

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('.food-calories-value').forEach((el) => {
            total += parseInt(el.textContent, 10) || 0;
        });

        const percent = calorieGoal > 0 ? Math.min(100, (total / calorieGoal) * 100) : 0;
        applyTotals(total, percent);
    }
});
