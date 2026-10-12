<?php
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

    //Get current date and time
    date_default_timezone_set('Asia/Kolkata');
    $d = date("Y-m-d");
    //echo " Date:".$d."<BR>";
    $t = date("H:i:s");

    if(!empty($_POST['value1']) && !empty($_POST['value2']) && !empty($_POST['value3']))
    {
    	$value1 = $_POST['value1'];
    	$value2 = $_POST['value2'];
		$value3 = $_POST['value3'];
	    $sql = "INSERT INTO logs (value1, value2,value3, Date, Time)
		
		VALUES ('".$value1."', '".$value2."', '".$value3."', '".$d."', '".$t."')";
 	if ($conn->query($sql) === TRUE) {
		    echo "OK";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}


	$conn->close();
?>
