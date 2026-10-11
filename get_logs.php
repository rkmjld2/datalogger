<?php
1
// =====================================================
// DATABASE CONNECTION — SECURE TLS/SSL
// =====================================================
$servername = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
$username   = "ax6KHc1BNkyuaor.root";
$password   = "EP8isIWoEOQk7DSr";
$dbname     = "espdemo2";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // Initialize MySQL connection
    $conn = mysqli_init();

    // Connect using encrypted SSL/TLS transport
    mysqli_real_connect(
        $conn,
        $servername,
        $username,
        $password,
        $dbname,
        4000,
        NULL,
        MYSQLI_CLIENT_SSL
    );

    // Set character encoding
    mysqli_set_charset($conn, "utf8mb4");

} catch (mysqli_sql_exception $e) {

    die("Secure database connection failed: "
        . htmlspecialchars($e->getMessage()));

}



// =====================================================
// CHECK DATABASE CONNECTION
// =====================================================

if (!$conn) {

    die(
        "Database connection failed: "
        . mysqli_connect_error()
    );
}


// =====================================================
// GET ALL RECORDS
// =====================================================

$sql = "SELECT
            id,
            value1,
            value2,
            value3,
            `Date`,
            `Time`,
            `TimeStamp`
        FROM logs
        ORDER BY id ASC";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "SQL Error: "
        . mysqli_error($conn)
    );
}


// =====================================================
// COUNT RECORDS
// =====================================================

$totalRecords = mysqli_num_rows($result);


// =====================================================
// CREATE HTML PAGE
// =====================================================

echo "<!DOCTYPE html>";

echo "<html>";

echo "<head>";

echo "<meta name='viewport' ";
echo "content='width=device-width, initial-scale=1'>";

echo "<meta http-equiv='refresh' content='5'>";

echo "<title>ESP8266 Database Logs</title>";


// =====================================================
// CSS
// =====================================================

echo "<style>";

echo "body {";
echo "font-family: Arial;";
echo "margin: 20px;";
echo "background: #f2f2f2;";
echo "}";


echo "h1 {";
echo "text-align: center;";
echo "}";


echo ".count {";
echo "text-align: center;";
echo "font-size: 20px;";
echo "margin-bottom: 20px;";
echo "}";


echo ".table-container {";
echo "overflow-x: auto;";
echo "}";


echo "table {";
echo "border-collapse: collapse;";
echo "width: 100%;";
echo "background: white;";
echo "}";


echo "th {";
echo "background: #333;";
echo "color: white;";
echo "padding: 10px;";
echo "border: 1px solid #777;";
echo "}";


echo "td {";
echo "padding: 8px;";
echo "border: 1px solid #999;";
echo "text-align: center;";
echo "}";


echo "tr:nth-child(even) {";
echo "background: #eeeeee;";
echo "}";


echo "</style>";

echo "</head>";


// =====================================================
// BODY
// =====================================================

echo "<body>";


echo "<h1>ESP8266 Database Logs</h1>";


echo "<div class='count'>";

echo "Total Records: ";

echo $totalRecords;

echo "</div>";


echo "<div class='table-container'>";


// =====================================================
// TABLE
// =====================================================

echo "<table>";


// =====================================================
// TABLE HEADER
// =====================================================

echo "<tr>";

echo "<th>ID</th>";

echo "<th>Value 1</th>";

echo "<th>Value 2</th>";

echo "<th>Value 3</th>";

echo "<th>Date</th>";

echo "<th>Time</th>";

echo "<th>Timestamp</th>";

echo "</tr>";


// =====================================================
// DISPLAY ALL RECORDS
// =====================================================

while ($row = mysqli_fetch_assoc($result)) {

    echo "<tr>";


    echo "<td>";

    echo htmlspecialchars(
        $row["id"]
    );

    echo "</td>";


    echo "<td>";

    echo htmlspecialchars(
        $row["value1"]
    );

    echo "</td>";


    echo "<td>";

    echo htmlspecialchars(
        $row["value2"]
    );

    echo "</td>";


    echo "<td>";

    echo htmlspecialchars(
        $row["value3"]
    );

    echo "</td>";


    echo "<td>";

    echo htmlspecialchars(
        $row["Date"]
    );

    echo "</td>";


    echo "<td>";

    echo htmlspecialchars(
        $row["Time"]
    );

    echo "</td>";


    echo "<td>";

    echo htmlspecialchars(
        $row["TimeStamp"]
    );

    echo "</td>";


    echo "</tr>";
}


echo "</table>";

echo "</div>";


// =====================================================
// FOOTER
// =====================================================

echo "<p style='text-align:center;'>";

echo "Page automatically refreshes every 5 seconds";

echo "</p>";


echo "</body>";

echo "</html>";


// =====================================================
// CLOSE DATABASE
// =====================================================

mysqli_close($conn);

?>
```
