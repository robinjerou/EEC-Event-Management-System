document.addEventListener('DOMContentLoaded', function() {
    fetchPurchaseHistory(); // Fetch initial purchase history on page load
});

function fetchPurchaseHistory() {
    fetch('assets/php/ims-fetch-purchase-history.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('purchase-history-body');
            tbody.innerHTML = '';

            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <img src="${item.ImageDir}" alt="Product Image">
                        <p>${item.ProductName}</p>
                    </td>
                    <td>${item.Quantity}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching purchase history:', error));
}

function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    fetch('assets/php/ims-fetch-purchase-history.php')
        .then(response => response.json())
        .then(data => {
            const filteredData = data.filter(item => item.ProductName.toLowerCase().includes(input));
            const tbody = document.getElementById('purchase-history-body');
            tbody.innerHTML = '';

            filteredData.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <img src="${item.ImageDir}" alt="Product Image">
                        <p>${item.ProductName}</p>
                    </td>
                    <td>${item.Quantity}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching and filtering purchase history:', error));
}