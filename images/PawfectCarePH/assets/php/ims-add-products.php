<?php
include_once "db-config.php";
session_start();

$productName = $_POST['productName'];
$productSize = $_POST['productSize'];
$productPrice = $_POST['productPrice'];
$productQuantity = $_POST['productQuantity'];
$imgDir = $_POST['imgDir'];

$firstName = $_SESSION['firstName'];
$lastName = $_SESSION['lastName'];
$email = $_SESSION['email'];

if (!isset($firstName, $lastName, $email)) {
    die("User information is missing from session.");
}

$sql = "SELECT UserID FROM tblUsers WHERE FirstName = ? AND LastName = ? AND Email = ?";
$stmt = sqlsrv_prepare($conn, $sql, array($firstName, $lastName, $email));
if (!$stmt) {
    die(print_r(sqlsrv_errors(), true));
}
sqlsrv_execute($stmt);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($row) {
    $userID = $row['UserID'];
} else {
    die("User not found.");
}

if ($productQuantity > 99) {
    $status = 'HIGH';
} elseif ($productQuantity > 49) {
    $status = 'AVERAGE';
} else {
    $status = 'LOW';
}

$updatedOn = date('Y-m-d H:i:s');

$sql = "INSERT INTO tblProducts (UserID, Name, Size, Price, Stock, Status, UpdatedOn, ImageDir) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$params = array($userID, $productName, $productSize, $productPrice, $productQuantity, $status, $updatedOn, $imgDir);
$stmt = sqlsrv_prepare($conn, $sql, $params);

if (!$stmt) {
    die(print_r(sqlsrv_errors(), true));
}

if (sqlsrv_execute($stmt)) {
    header("Location: ../../ims-menu.html");
    exit();
} else {
    echo "Error: " . print_r(sqlsrv_errors(), true);
}

sqlsrv_close($conn);
?>
