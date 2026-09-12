<?php
include_once "db-config.php";

$sql = "
    SELECT ImageDir, Name, Size, Price
    FROM tblProducts
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$products = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $product = array(
        'ImageDir' => $row['ImageDir'],
        'Name' => $row['Name'],
        'Size' => $row['Size'],
        'Price' => $row['Price']
    );
    $products[] = $product;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode($products);
?>
