function loadInvoiceData(searchQuery = '') {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "assets/php/ims-fetch-invoice.php?search=" + encodeURIComponent(searchQuery), true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            document.getElementById("invoice-table-body").innerHTML = xhr.responseText;
        } else {
            console.error("Failed to load data.");
        }
    };
    xhr.send();
}

window.onload = function() {
    loadInvoiceData();

    document.getElementById("search-button").addEventListener("click", function() {
        var searchQuery = document.getElementById("search-input").value;
        loadInvoiceData(searchQuery);
    });
};
