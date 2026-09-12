document.addEventListener("DOMContentLoaded", function () {
    fetch("assets/php/ims-fetch-product-options.php")
        .then(response => response.json())
        .then(data => {
            const productSelect = document.getElementById("productSelect");
            productSelect.innerHTML = "";
            data.forEach(name => {
                const option = document.createElement("option");
                option.value = name;
                option.textContent = name;
                productSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching product options:", error));
});
