<?php

include_once "db-config.php";
session_start();

if (isset($_SESSION['totalPrice'], $_POST['cash'], $_POST['change'], $_SESSION['firstName'], $_SESSION['lastName'], $_SESSION['email'])) {
    $totalPrice = $_SESSION['totalPrice'];
    $cash = $_POST['cash'];
    $change = $_POST['change'];
    $firstName = $_SESSION['firstName'];
    $lastName = $_SESSION['lastName'];
    $email = $_SESSION['email'];

    try {
        $sqlUserId = "
            SELECT UserID
            FROM tblUsers
            WHERE FirstName = ? AND LastName = ? AND Email = ?
        ";
        $params = array($firstName, $lastName, $email);
        $stmtUserId = sqlsrv_query($conn, $sqlUserId, $params);
        if ($stmtUserId === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmtUserId, SQLSRV_FETCH_ASSOC);
        if ($row === null) {
            die("User not found.");
        }
        $userId = $row['UserID'];

        // Get maximum ReceiptID from tblReceipts
        $sqlMaxReceiptId = "
            SELECT MAX(CAST(SUBSTRING(ReceiptID, 4, LEN(ReceiptID)) AS INT)) AS MAX_RECEIPTID
            FROM tblReceipts
        ";
        $stmtMaxReceiptId = sqlsrv_query($conn, $sqlMaxReceiptId);
        if ($stmtMaxReceiptId === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $maxReceiptId = sqlsrv_fetch_array($stmtMaxReceiptId, SQLSRV_FETCH_ASSOC)['MAX_RECEIPTID'];
        if ($maxReceiptId === null) {
            // If no receipts exist, start from RID1000
            $nextReceiptId = "RID1000";
        } else {
            // Increment the maximum ReceiptID to generate the next one
            $nextReceiptId = "RID" . str_pad(($maxReceiptId + 1), 4, '0', STR_PAD_LEFT);
        }

        $sqlInsertReceipt = "
            INSERT INTO tblReceipts (ReceiptID, Total, Paid, Change, UserID, Date)
            VALUES (?, ?, ?, ?, ?, GETDATE())
        ";
        $paramsInsert = array($nextReceiptId, $totalPrice, $cash, $change, $userId);
        $stmtInsertReceipt = sqlsrv_query($conn, $sqlInsertReceipt, $paramsInsert);
        if ($stmtInsertReceipt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($stmtUserId);
        sqlsrv_free_stmt($stmtMaxReceiptId);
        sqlsrv_free_stmt($stmtInsertReceipt);

        echo "Receipt generated successfully. Redirecting...";
        echo '<script>
                setTimeout(function() {
                    window.location.href = "../../pos-billing-system.html";
                }, 1000);
              </script>';

    } catch (Exception $e) {
        echo "Transaction rolled back. Error: " . $e->getMessage();
    } finally {
        sqlsrv_close($conn);
    }
} else {
    echo "Session data or POST data missing.";
}
?>
