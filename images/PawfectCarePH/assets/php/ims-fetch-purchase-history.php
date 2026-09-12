<?php

include_once "db-config.php";

try {
    $sql = "
        SELECT ph.Quantity, p.ImageDir, p.Name, ph.Size
        FROM tblPurchaseHistory ph
        INNER JOIN tblProducts p ON ph.ProductID = p.ProductID
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $purchaseHistory = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $item = [
            'ImageDir' => $row['ImageDir'],
            'ProductName' => $row['Name'] . ' (' . $row['Size'] . ')',
            'Quantity' => $row['Quantity']
        ];
        $purchaseHistory[] = $item;
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    header('Content-Type: application/json');
    echo json_encode($purchaseHistory);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
