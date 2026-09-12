<?php
include_once "db-config.php";

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "
    SELECT r.ReceiptID, r.Total, r.Paid, r.Change, r.Date, u.FirstName, u.LastName 
    FROM tblReceipts r 
    JOIN tblUsers u ON r.UserID = u.UserID
    WHERE r.ReceiptID LIKE ? OR r.Total LIKE ? OR r.Paid LIKE ? OR r.Change LIKE ? 
    OR CONVERT(VARCHAR, r.Date, 23) LIKE ? OR (u.FirstName + ' ' + u.LastName) LIKE ?
";

$params = ["%$searchQuery%", "%$searchQuery%", "%$searchQuery%", "%$searchQuery%", "%$searchQuery%", "%$searchQuery%"];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rows = '';
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $receiptID = $row['ReceiptID'];
    $total = $row['Total'];
    $paid = $row['Paid'];
    $change = $row['Change'];
    $date = $row['Date']->format('Y-m-d');
    $cashier = $row['FirstName'] . ' ' . $row['LastName'];

    $rows .= "
        <tr>
            <td>{$receiptID}</td>
            <td>{$total}</td>
            <td>{$paid}</td>
            <td>{$change}</td>
            <td>{$date}</td>
            <td>{$cashier}</td>
        </tr>
    ";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo $rows;
?>
