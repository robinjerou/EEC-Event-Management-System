<?php
include_once "db-config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $productName = $_POST['productSelect'];
    $productSize = $_POST['productSize'];
    $productPrice = $_POST['productPrice'];
    $productQuantity = $_POST['productQuantity'];

    if ($productQuantity > 99) {
        $status = 'HIGH';
    } elseif ($productQuantity > 49) {
        $status = 'AVERAGE';
    } else {
        $status = 'LOW';
    }

    $sql = "
        UPDATE tblProducts
        SET Price = ?,
            Stock = ?,
            Status = ?,
            UpdatedOn = GETDATE()  -- Assuming UpdatedOn should reflect current date/time
        WHERE Name = ? AND Size = ?
    ";

    $params = array($productPrice, $productQuantity, $status, $productName, $productSize);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        header("Location: ../../ims-menu.html");
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
