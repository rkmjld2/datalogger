<?php
//Creates new record as per request
    //Connect to database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "espdemo2";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Database Connection failed: " . $conn->connect_error);
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