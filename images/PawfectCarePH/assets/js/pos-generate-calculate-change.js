document.addEventListener("DOMContentLoaded", function() {
    const totalElement = document.getElementById("total");
    const cashInput = document.getElementById("cash");
    const changeInput = document.getElementById("change");

    function calculateChange() {
        const total = parseFloat(totalElement.textContent);
        const cash = parseFloat(cashInput.value);

        if (!isNaN(total) && !isNaN(cash)) {
            const change = cash - total;
            changeInput.value = change.toFixed(2);
        } else {
            changeInput.value = "";
        }
    }

    cashInput.addEventListener("input", calculateChange);
});
