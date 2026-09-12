 function loadProductData() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "assets/php/ims-fetch-products.php", true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            document.getElementById("product-table-body").innerHTML = xhr.responseText;
        } else {
            console.error("Failed to load data.");
        }
    };
    xhr.send();
}
window.onload = loadProductData;