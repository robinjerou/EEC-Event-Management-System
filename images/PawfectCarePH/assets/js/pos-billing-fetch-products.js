document.addEventListener("DOMContentLoaded", function () {
    fetch("assets/php/pos-billing-fetch-products.php")
        .then(response => response.json())
        .then(data => {
            const productSection = document.querySelector(".product-list");
            productSection.innerHTML = "";

            data.forEach(product => {
                const productItem = document.createElement("div");
                productItem.classList.add("product-item");
                productItem.setAttribute("data-name", `${product.Name} (${product.Size})`);
                productItem.setAttribute("data-price", product.Price);

                const img = document.createElement("img");
                img.src = product.ImageDir;
                img.alt = product.Name;
                img.classList.add("product-image");

                const nameParagraph = document.createElement("p");
                nameParagraph.textContent = `${product.Name} (${product.Size})`;

                const priceParagraph = document.createElement("p");
                priceParagraph.textContent = `₱${product.Price}`;

                productItem.appendChild(img);
                productItem.appendChild(nameParagraph);
                productItem.appendChild(priceParagraph);

                productSection.appendChild(productItem);
            });
        })
        .catch(error => console.error("Error fetching products:", error));
});
