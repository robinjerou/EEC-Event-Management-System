<?php
session_start();

if (isset($_SESSION['email'])) {
    include_once "db-config.php";
    
    $email = $_SESSION['email'];
    $sql = "SELECT FirstName, LastName FROM tblUsers WHERE Email = ?";
    
    $params = array(&$email);
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if ($stmt && sqlsrv_execute($stmt)) {

        if (sqlsrv_fetch($stmt) === true) {
            $firstName = sqlsrv_get_field($stmt, 0);
            $lastName = sqlsrv_get_field($stmt, 1);

            echo json_encode(array('firstName' => $firstName, 'lastName' => $lastName));
        } else {
            echo json_encode(array('error' => 'User not found'));
        }
    } else {
        echo json_encode(array('error' => 'SQL execution error: ' . print_r(sqlsrv_errors(), true)));
    }
} else {
    echo json_encode(array('error' => 'Unauthorized access'));
}
?>
