<?php
include_once "db-config.php";

$sql = "SELECT DISTINCT Name FROM tblProducts";

$result = sqlsrv_query($conn, $sql);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

$productNames = array();
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $productNames[] = $row['Name'];
}

sqlsrv_free_stmt($result);
sqlsrv_close($conn);
echo json_encode($productNames);
?>
