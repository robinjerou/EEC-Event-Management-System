<?php
include_once "db-config.php";

session_start();

if (isset($_POST['cartItems']) && !empty($_POST['cartItems'])) {
    $cartItems = json_decode($_POST['cartItems'], true);

    if ($cartItems !== null) {
        if (!empty($cartItems)) {
            try {

                sqlsrv_begin_transaction($conn);

                $totalPrice = 0;

                foreach ($cartItems as $item) {
                    $productName = $item['name'];
                    $productSize = $item['size'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];

                    $sqlProductId = "
                        SELECT ProductID, Stock
                        FROM tblProducts
                        WHERE Name = ? AND Size = ?
                    ";

                    $params = array($productName, $productSize);
                    $stmtProductId = sqlsrv_query($conn, $sqlProductId, $params);
                    if ($stmtProductId === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    $row = sqlsrv_fetch_array($stmtProductId, SQLSRV_FETCH_ASSOC);
                    if ($row === null) {
                        die("Product not found: $productName, Size: $productSize");
                    }
                    $productId = $row['ProductID'];
                    $currentStock = $row['Stock'];

                    $sqlCheckPurchase = "
                        SELECT * 
                        FROM tblPurchaseHistory
                        WHERE ProductID = ? AND Name = ? AND Size = ?
                    ";

                    $paramsCheck = array($productId, $productName, $productSize);
                    $stmtCheck = sqlsrv_query($conn, $sqlCheckPurchase, $paramsCheck);
                    if ($stmtCheck === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    $existingPurchase = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

                    if ($existingPurchase) {

                        $newQuantity = $existingPurchase['Quantity'] + $quantity;
                        $sqlUpdatePurchase = "
                            UPDATE tblPurchaseHistory
                            SET Quantity = ?
                            WHERE PurchaseHistoryID = ?
                        ";

                        $paramsUpdate = array($newQuantity, $existingPurchase['PurchaseHistoryID']);
                        $stmtUpdate = sqlsrv_query($conn, $sqlUpdatePurchase, $paramsUpdate);
                        if ($stmtUpdate === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                    } else {

                        $sqlInsertPurchase = "
                            INSERT INTO tblPurchaseHistory (ProductID, Name, Size, Quantity)
                            VALUES (?, ?, ?, ?)
                        ";

                        $paramsInsert = array($productId, $productName, $productSize, $quantity);
                        $stmtInsert = sqlsrv_query($conn, $sqlInsertPurchase, $paramsInsert);
                        if ($stmtInsert === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                    }

                    $totalPrice += $price * $quantity;

                    $newStock = $currentStock - $quantity;
                    $status = ($newStock > 99) ? 'HIGH' : (($newStock > 49) ? 'AVERAGE' : 'LOW');

                    $sqlUpdateStock = "
                        UPDATE tblProducts
                        SET Stock = ?, Status = ?, UpdatedOn = GETDATE()
                        WHERE ProductID = ?
                    ";

                    $paramsUpdateStock = array($newStock, $status, $productId);
                    $stmtUpdateStock = sqlsrv_query($conn, $sqlUpdateStock, $paramsUpdateStock);
                    if ($stmtUpdateStock === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    sqlsrv_free_stmt($stmtProductId);
                    sqlsrv_free_stmt($stmtCheck);
                    if (isset($stmtUpdate)) {
                        sqlsrv_free_stmt($stmtUpdate);
                    }
                    if (isset($stmtInsert)) {
                        sqlsrv_free_stmt($stmtInsert);
                    }
                    sqlsrv_free_stmt($stmtUpdateStock);
                }

                sqlsrv_commit($conn);

                $_SESSION['totalPrice'] = $totalPrice;

                header("Location: ../../pos-generate-invoice.html?totalPrice=" . urlencode($totalPrice));
                exit();
            } catch (Exception $e) {
                sqlsrv_rollback($conn);
                echo "Transaction rolled back. Error: " . $e->getMessage();
            }
        } else {
            echo "Cart is empty.";
        }
    } else {
        echo "Error decoding cart items.";
    }
} else {
    echo "Cart is empty.";
}
?>
