
<?php
// ============================================================
// DATABASE CONNECTION
// ============================================================

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "espdemo2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}


// ============================================================
// VARIABLES
// ============================================================

$txtStartDate = "";
$txtEndDate   = "";

$graphType = "Line";

$rows = array();


// ============================================================
// SEARCH BUTTON
// ============================================================

if (isset($_POST['Search'])) {

    $txtStartDate = $_POST['txtStartDate'];
    $txtEndDate   = $_POST['txtEndDate'];


    // Get graph type
    if (isset($_POST['graphType'])) {

        $graphType = $_POST['graphType'];
    }


    // --------------------------------------------------------
    // ALLOWED GRAPH TYPES
    // --------------------------------------------------------

    $allowedGraphs = array(
        "Line",
        "Area",
        "Bar",
        "Stacked Bar",
        "Stacked Area"
    );


    // If invalid graph type
    if (!in_array($graphType, $allowedGraphs)) {

        $graphType = "Line";
    }


    // --------------------------------------------------------
    // DATABASE QUERY
    // --------------------------------------------------------

    $sql = "
        SELECT *
        FROM logs
        WHERE `Date` BETWEEN '$txtStartDate' AND '$txtEndDate'
        ORDER BY `Date` ASC, `Time` ASC, id ASC
    ";


    $result = mysqli_query($conn, $sql);


    if ($result) {

        while ($row = mysqli_fetch_assoc($result)) {

            $rows[] = $row;
        }

        mysqli_free_result($result);
    }
}


mysqli_close($conn);


// ============================================================
// PREPARE GRAPH DATA
// ============================================================

$chart_data = "";


foreach ($rows as $row) {

    // Combine Date and Time
    $datetime = $row['Date'] . " " . $row['Time'];


    // Convert values to numbers
    $value1 = floatval($row['value1']);
    $value2 = floatval($row['value2']);
    $value3 = floatval($row['value3']);


    // Make Date + Time safe for JavaScript
    $datetime_js = htmlspecialchars(
        $datetime,
        ENT_QUOTES,
        'UTF-8'
    );


    $chart_data .= "{";

    $chart_data .= "datetime:'" . $datetime_js . "',";

    $chart_data .= "value1:" . $value1 . ",";

    $chart_data .= "value2:" . $value2 . ",";

    $chart_data .= "value3:" . $value3;

    $chart_data .= "},";
}


// Remove last comma
$chart_data = rtrim($chart_data, ",");

?>

<!DOCTYPE html>

<html>

<head>

<title>
ESP8266 Data Search and Graph
</title>


<!-- =========================================================
     MORRIS.JS CSS
     ========================================================= -->

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">


<!-- =========================================================
     JQUERY
     ========================================================= -->

<script
src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js">
</script>


<!-- =========================================================
     RAPHAEL
     ========================================================= -->

<script
src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js">
</script>


<!-- =========================================================
     MORRIS.JS
     ========================================================= -->

<script
src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js">
</script>


<style>

/* =========================================================
   NORMAL WEBPAGE
   ========================================================= */

body {

    font-family: Arial, Helvetica, sans-serif;

    margin: 20px;

    background-color: #f5f5f5;
}


/* =========================================================
   SEARCH BOX
   ========================================================= */

.searchBox {

    background-color: white;

    padding: 20px;

    border-radius: 8px;

    box-shadow: 0px 2px 8px rgba(0,0,0,0.15);

    margin-bottom: 25px;
}


.searchBox h2 {

    margin-top: 0;

    color: #333;
}


.searchBox label {

    font-weight: bold;

    margin-right: 5px;
}


.searchBox input,
.searchBox select {

    padding: 8px;

    margin-right: 15px;

    font-size: 15px;
}


.searchBox input[type="submit"] {

    background-color: #008c95;

    color: white;

    border: none;

    padding: 9px 20px;

    cursor: pointer;

    border-radius: 4px;
}


.searchBox input[type="submit"]:hover {

    background-color: #006f76;
}


/* =========================================================
   INFORMATION
   ========================================================= */

.info {

    text-align: center;

    margin: 15px;

    font-size: 16px;

    color: #555;
}


/* =========================================================
   TABLE
   ========================================================= */

#c4ytable {

    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;

    border-collapse: collapse;

    width: 100%;

    background-color: white;

    margin-top: 20px;
}


#c4ytable td,
#c4ytable th {

    border: 1px solid #ddd;

    padding: 8px;
}


#c4ytable tr:nth-child(even) {

    background-color: #f2f2f2;
}


#c4ytable tr:hover {

    background-color: #ddd;
}


#c4ytable th {

    padding-top: 12px;

    padding-bottom: 12px;

    text-align: left;

    background-color: #00A8A9;

    color: white;
}


/* =========================================================
   GRAPH BOX
   ========================================================= */

.graphBox {

    background-color: white;

    margin-top: 30px;

    padding: 20px;

    border-radius: 8px;

    box-shadow: 0px 2px 8px rgba(0,0,0,0.15);
}


.graphBox h2 {

    text-align: center;

    color: #333;
}


#chart {

    width: 100%;

    height: 500px;
}


/* =========================================================
   PRINT BUTTON AREA
   ========================================================= */

.printButtons {

    text-align: center;

    margin-top: 25px;

    margin-bottom: 25px;
}


.printButtons button {

    padding: 10px 18px;

    margin: 5px;

    font-size: 15px;

    border: none;

    border-radius: 5px;

    cursor: pointer;

    color: white;

    background-color: #444;
}


.printButtons button:hover {

    opacity: 0.8;
}


/* =========================================================
   PRINT INFORMATION
   ========================================================= */

.printTitle {

    display: none;

}


/* =========================================================
   PRINT SETTINGS
   ========================================================= */

@media print {


    body {

        background-color: white;

        margin: 10px;
    }


    /*
       Hide normal webpage controls
    */

    .searchBox {

        display: none;
    }


    .printButtons {

        display: none;
    }


    /*
       Hide graph initially
    */

    .graphBox {

        display: none;
    }


    /*
       Hide information initially
    */

    .info {

        display: none;
    }


    /*
       Print title
    */

    .printTitle {

        display: block;

        text-align: center;

        font-size: 22px;

        margin-bottom: 15px;
    }


    /*
       Table print settings
    */

    #c4ytable {

        width: 100%;

        border-collapse: collapse;
    }


    #c4ytable th,
    #c4ytable td {

        border: 1px solid black;

        padding: 6px;
    }


    #c4ytable th {

        background-color: #eeeeee !important;

        color: black !important;
    }


    /*
       Graph print settings
    */

    #chart {

        width: 100% !important;

        height: 600px !important;
    }


    /*
       When printing GRAPH
    */

    body.printGraph .graphBox {

        display: block;
    }


    body.printGraph #c4ytable {

        display: none;
    }


    body.printGraph .graphBox {

        box-shadow: none;

        margin: 0;

        padding: 0;
    }


    body.printGraph .printTitle {

        display: block;
    }


    /*
       When printing DATA
    */

    body.printData #c4ytable {

        display: table;
    }


    body.printData .graphBox {

        display: none;
    }


    /*
       When printing BOTH
    */

    body.printBoth #c4ytable {

        display: table;
    }


    body.printBoth .graphBox {

        display: block;

        box-shadow: none;
    }

}


/* =========================================================
   PAGE BREAK
   ========================================================= */

@media print {

    .graphBox {

        page-break-before: auto;
    }

}

</style>

</head>


<body>


<!-- =========================================================
     SEARCH FORM
     ========================================================= -->

<div class="searchBox">

<h2>
ESP8266 Data Search
</h2>


<form method="post" action="">


<!-- FROM DATE -->

<label>
From Date:
</label>

<input
type="date"
name="txtStartDate"
value="<?php echo htmlspecialchars($txtStartDate); ?>"
required
>


<!-- TO DATE -->

<label>
To Date:
</label>

<input
type="date"
name="txtEndDate"
value="<?php echo htmlspecialchars($txtEndDate); ?>"
required
>


<!-- =====================================================
     GRAPH TYPE
     ===================================================== -->

<label>
Graph Type:
</label>


<select name="graphType">


<option value="Line"
<?php
if ($graphType == "Line") echo "selected";
?>
>
Line
</option>


<option value="Area"
<?php
if ($graphType == "Area") echo "selected";
?>
>
Area
</option>


<option value="Bar"
<?php
if ($graphType == "Bar") echo "selected";
?>
>
Bar
</option>


<option value="Stacked Bar"
<?php
if ($graphType == "Stacked Bar") echo "selected";
?>
>
Stacked Bar
</option>


<option value="Stacked Area"
<?php
if ($graphType == "Stacked Area") echo "selected";
?>
>
Stacked Area
</option>


</select>


<!-- SEARCH BUTTON -->

<input
type="submit"
name="Search"
value="Search"
>


</form>

</div>


<?php

// ============================================================
// DISPLAY SEARCH INFORMATION
// ============================================================

if (isset($_POST['Search'])) {

?>

<div class="info">

<b>From Date:</b>
<?php echo htmlspecialchars($txtStartDate); ?>

&nbsp;&nbsp;&nbsp;

<b>To Date:</b>
<?php echo htmlspecialchars($txtEndDate); ?>

&nbsp;&nbsp;&nbsp;

<b>Graph:</b>
<?php echo htmlspecialchars($graphType); ?>

</div>

<?php

}


// ============================================================
// PRINT TITLE
// ============================================================

if (isset($_POST['Search']) && count($rows) > 0) {

?>

<div class="printTitle">

ESP8266 Data Report

<br>

From
<?php echo htmlspecialchars($txtStartDate); ?>

&nbsp;

To
<?php echo htmlspecialchars($txtEndDate); ?>

</div>

<?php

}


// ============================================================
// DISPLAY TABLE
// ============================================================

if (isset($_POST['Search'])) {

    if (count($rows) > 0) {

?>

<table id="c4ytable">

<tr>

<th>id</th>

<th>Value1</th>

<th>Value2</th>

<th>Value3</th>

<th>Date</th>

<th>Time</th>

</tr>


<?php

        // ----------------------------------------------------
        // DISPLAY DATABASE ROWS
        // ----------------------------------------------------

        foreach ($rows as $row) {

?>

<tr>

<td>
<?php
echo htmlspecialchars($row['id']);
?>
</td>


<td>
<?php
echo htmlspecialchars($row['value1']);
?>
</td>


<td>
<?php
echo htmlspecialchars($row['value2']);
?>
</td>


<td>
<?php
echo htmlspecialchars($row['value3']);
?>
</td>


<td>
<?php
echo htmlspecialchars($row['Date']);
?>
</td>


<td>
<?php
echo htmlspecialchars($row['Time']);
?>
</td>

</tr>

<?php

        }

?>

</table>

<?php

    }
    else {

?>

<div class="info">

<b>
No records found for the selected dates.
</b>

</div>

<?php

    }

}


// ============================================================
// GRAPH
// ============================================================

if (isset($_POST['Search']) && count($rows) > 0) {

?>

<div class="graphBox">

<h2>

<?php
echo htmlspecialchars($graphType);
?>

Graph

</h2>


<div id="chart"></div>

</div>


<!-- =========================================================
     PRINT BUTTONS
     ========================================================= -->

<div class="printButtons">


<button onclick="printData()">

Print Search Data

</button>


<button onclick="printGraph()">

Print Selected Graph

</button>


<button onclick="printBoth()">

Print Both

</button>


</div>

<?php

}

?>


<!-- =========================================================
     JAVASCRIPT
     ========================================================= -->

<?php

if (isset($_POST['Search']) && count($rows) > 0) {

?>

<script>


// ============================================================
// GRAPH DATA
// ============================================================

var chartData = [

<?php echo $chart_data; ?>

];


// ============================================================
// COMMON GRAPH OPTIONS
// ============================================================

var graphOptions = {

    element: 'chart',

    data: chartData,

    xkey: 'datetime',

    ykeys: [

        'value1',

        'value2',

        'value3'

    ],

    labels: [

        'Value 1',

        'Value 2',

        'Value 3'

    ],

    parseTime: false,

    pointSize: 4,

    lineWidth: 2,

    xLabelAngle: 45,

    hideHover: false,

    resize: true,


    // --------------------------------------------------------
    // HOVER INFORMATION
    // --------------------------------------------------------

    hoverCallback: function(index, options, content) {

        var row = options.data[index];

        return (

            '<div style="padding:5px;">' +

            '<b>Date / Time:</b> ' +

            row.datetime +

            '<br>' +

            '<b>Value 1:</b> ' +

            row.value1 +

            '<br>' +

            '<b>Value 2:</b> ' +

            row.value2 +

            '<br>' +

            '<b>Value 3:</b> ' +

            row.value3 +

            '</div>'

        );

    }

};


// ============================================================
// CREATE SELECTED GRAPH
// ============================================================

<?php

if ($graphType == "Line") {

?>

Morris.Line(graphOptions);

<?php

}

elseif ($graphType == "Area") {

?>

Morris.Area(graphOptions);

<?php

}

elseif ($graphType == "Bar") {

?>

Morris.Bar(graphOptions);

<?php

}

elseif ($graphType == "Stacked Bar") {

?>

graphOptions.stacked = true;

Morris.Bar(graphOptions);

<?php

}

elseif ($graphType == "Stacked Area") {

?>

graphOptions.stacked = true;

Morris.Area(graphOptions);

<?php

}

?>



// ============================================================
// PRINT SEARCH DATA
// ============================================================

function printData() {

    document.body.classList.remove("printGraph");

    document.body.classList.remove("printBoth");

    document.body.classList.add("printData");

    window.print();

}


// ============================================================
// PRINT SELECTED GRAPH
// ============================================================

function printGraph() {

    document.body.classList.remove("printData");

    document.body.classList.remove("printBoth");

    document.body.classList.add("printGraph");

    window.print();

}


// ============================================================
// PRINT BOTH DATA AND GRAPH
// ============================================================

function printBoth() {

    document.body.classList.remove("printData");

    document.body.classList.remove("printGraph");

    document.body.classList.add("printBoth");

    window.print();

}


// ============================================================
// AFTER PRINTING
// RETURN TO NORMAL WEBPAGE
// ============================================================

window.onafterprint = function() {

    document.body.classList.remove("printData");

    document.body.classList.remove("printGraph");

    document.body.classList.remove("printBoth");

};


</script>

<?php

}

?>

</body>

</html>
