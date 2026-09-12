<?php
include_once "db-config.php";

$sql = "
    SELECT ImageDir, Name, Size, Price, Stock, Status
    FROM tblProducts
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rows = '';
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $imageDir = $row['ImageDir'];
    $name = $row['Name'] . " (" . $row['Size'] . ")";
    $price = $row['Price'];
    $stock = $row['Stock'];
    $status = $row['Status'];

    $rows .= "
        <tr>
            <td>
                <img src=\"{$imageDir}\" alt=\"{$name}\">
                <p>{$name}</p>
            </td>
            <td>{$price}</td>
            <td>{$stock}</td>
            <td class=\"status\">{$status}</td>
        </tr>
    ";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo $rows;
?>
