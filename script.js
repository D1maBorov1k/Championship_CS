// Динамічне відображення полів для додавання даних
function showFields(table) {
    const fieldsContainer = document.getElementById('fields');
    fieldsContainer.innerHTML = ''; // Очищаємо попередні поля

    if (table) {
        fetch(`get_fields.php?table=${table}`)
            .then(response => response.json())
            .then(fields => {
                if (fields.error) {
                    console.error('Помилка:', fields.error);
                    return;
                }
                fields.forEach(field => {
                    if (field !== 'id') { // Пропускаємо ID, якщо він автоматично генерується
                        const label = document.createElement('label');
                        label.textContent = field;

                        let input;

                        // Якщо поле типу дата, використовуємо <input type="date">
                        if (field.toLowerCase().includes('date')) {
                            input = document.createElement('input');
                            input.type = 'date';
                        } 
                        // Якщо поле типу час, використовуємо <input type="time">
                        else if (field.toLowerCase().includes('time')) {
                            input = document.createElement('input');
                            input.type = 'time';
                        } 
                        // В іншому випадку, використовуємо <input type="text">
                        else {
                            input = document.createElement('input');
                            input.type = 'text';
                        }

                        input.name = `field_${field}`;
                        input.placeholder = `Введіть ${field}`;
                        fieldsContainer.appendChild(label);
                        fieldsContainer.appendChild(input);
                        fieldsContainer.appendChild(document.createElement('br'));
                    }
                });
            })
            .catch(error => console.error('Помилка завантаження полів:', error));
    }
}
