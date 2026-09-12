document.addEventListener("DOMContentLoaded", function () {
    const productSection = document.querySelector(".product-list");
    const cartItemsSection = document.getElementById("cart-items");
    const subtotalElement = document.getElementById("subtotal");
    const totalElement = document.getElementById("total");

    let cartItems = JSON.parse(sessionStorage.getItem('cartItems')) || [];

    function updateSubtotalAndTotal() {
        let subtotal = 0;
        cartItems.forEach(item => {
            subtotal += item.quantity * item.price;
        });
        totalElement.textContent = subtotal.toFixed(2);
    }

    function renderCartItems() {
        cartItemsSection.innerHTML = "";
        cartItems.forEach((item, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.name} (${item.size})</td>
                <td>${item.quantity}</td>
                <td>${(item.quantity * item.price).toFixed(2)}</td>
                <td>
                    <button class="subtract-btn" data-name="${item.name}" data-size="${item.size}">-</button>
                    <button class="delete-btn" data-name="${item.name}" data-size="${item.size}">Del</button>
                </td>
            `;
            cartItemsSection.appendChild(row);
        });
        updateSubtotalAndTotal();
        sessionStorage.setItem('cartItems', JSON.stringify(cartItems));

        // Update hidden input field with cartItems data
        const cartItemsInput = document.getElementById("cart-items-input");
        cartItemsInput.value = JSON.stringify(cartItems);
    }

    function addToCart(name, size, price) {
        const index = cartItems.findIndex(item => item.name === name && item.size === size);
        if (index !== -1) {
            cartItems[index].quantity++;
        } else {
            cartItems.push({
                name: name,
                size: size,
                quantity: 1,
                price: price
            });
        }
        renderCartItems();
    }

    function removeFromCart(name, size) {
        const index = cartItems.findIndex(item => item.name === name && item.size === size);
        if (index !== -1) {
            cartItems.splice(index, 1);
            renderCartItems();
        }
    }

    productSection.addEventListener("click", function (event) {
        const productItem = event.target.closest(".product-item");
        if (productItem) {
            const name = productItem.getAttribute("data-name");
            const size = productItem.getAttribute("data-size");
            const price = parseFloat(productItem.getAttribute("data-price"));
            addToCart(name, size, price);
        }
    });

    cartItemsSection.addEventListener("click", function (event) {
        if (event.target.classList.contains("subtract-btn")) {
            const name = event.target.getAttribute("data-name");
            const size = event.target.getAttribute("data-size");
            const item = cartItems.find(item => item.name === name && item.size === size);
            if (item && item.quantity > 1) {
                item.quantity--;
                renderCartItems();
            }
        }

        if (event.target.classList.contains("delete-btn")) {
            const name = event.target.getAttribute("data-name");
            const size = event.target.getAttribute("data-size");
            removeFromCart(name, size);
        }
    });

    fetch("assets/php/pos-billing-fetch-products.php")
        .then(response => response.json())
        .then(data => {
            productSection.innerHTML = "";
            data.forEach(product => {
                const productItem = document.createElement("div");
                productItem.classList.add("product-item");
                productItem.setAttribute("data-name", product.Name);
                productItem.setAttribute("data-size", product.Size);
                productItem.setAttribute("data-price", product.Price);

                const img = document.createElement("img");
                img.src = product.ImageDir;
                img.alt = product.Name;
                img.classList.add("product-image");

                const nameParagraph = document.createElement("p");
                nameParagraph.textContent = `${product.Name} (${product.Size})`;

                const priceParagraph = document.createElement("p");
                priceParagraph.textContent = `${product.Price}`;

                productItem.appendChild(img);
                productItem.appendChild(nameParagraph);
                productItem.appendChild(priceParagraph);

                productSection.appendChild(productItem);
            });
        })
        .catch(error => console.error("Error fetching products:", error));
});
