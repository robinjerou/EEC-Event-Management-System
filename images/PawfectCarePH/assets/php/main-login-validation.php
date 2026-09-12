<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    include_once "db-config.php";
    
    $action = $_POST['action'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {

        header("Location: ../../main-login.html?error=empty");
        exit;

    } else {
        // Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $sql = "SELECT * FROM tblUsers WHERE Email = ? AND Password = ?";
        
        $stmt = sqlsrv_prepare($conn, $sql, array(&$email, &$password));

        if ($stmt && sqlsrv_execute($stmt)) {

            if (sqlsrv_fetch($stmt) === true) {

                // Get user's information
                $userID = sqlsrv_get_field($stmt, 0); // first col
                $firstName = sqlsrv_get_field($stmt, 1); // second col
                $lastName = sqlsrv_get_field($stmt, 2); // third col
                $userType = sqlsrv_get_field($stmt, 6); // seventh col

                if ($userType === "Administrator") {
                    $_SESSION['email'] = $email;
                    $_SESSION['firstName'] = $firstName;
                    $_SESSION['lastName'] = $lastName;
                    
                    logUserLogin($conn, $userID, $email, $firstName, $lastName, $action);

                    if ($action === 'pos-billing-system.html') {
                        header("Location: ../../pos-billing-system.html");
                    } elseif ($action === 'ims-menu.html') {
                        header("Location: ../../ims-menu.html");
                    } else {
                        header("Location: ../../main-login.html");
                    }
                    exit;
                } elseif ($userType === "Cashier" && $action === 'pos-billing-system.html') {
                    $_SESSION['email'] = $email;
                    $_SESSION['firstName'] = $firstName;
                    $_SESSION['lastName'] = $lastName;

                    // Logs the login event
                    logUserLogin($conn, $userID, $email, $firstName, $lastName, $action);

                    header("Location: ../../pos-billing-system.html");
                    exit;
                } else {
                    header("Location: ../../main-login.html?error=unauthorized");
                    exit;
                }

            } else {
                header("Location: ../../main-login.html?error=invalid");
                exit;
            }
        } else {
            header("Location: ../../main-login.html?error=db_error");
            exit;
        }
    }
} else {
    header("Location: ../../main-login.html");
    exit;
}

function logUserLogin($conn, $userID, $email, $firstName, $lastName, $action) {
    $description = ($action === 'pos-billing-system.html') ? 'POS Login' : 'Inventory Login';
    
    $sqlLog = "INSERT INTO tblLogs (UserID, Description, Date) VALUES (?, ?, GETDATE())";
    $paramsLog = array($userID, $description);
    $stmtLog = sqlsrv_query($conn, $sqlLog, $paramsLog);

    if ($stmtLog === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmtLog);
}
?>
