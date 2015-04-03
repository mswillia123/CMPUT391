<!--
	Data Analysis Module:
	Allows system administrator to create an OLAP report
	Information generated as a datacube on patient_name, test_type, and test_date
	Display selectable by patient_name (optional), test_type (optional), and test_date
	Generalization allows roll up and drill down on time periods weekly, monthly, yearly, or full data (daily)
	
	Author: Michael Williams
-->


<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
<body>
<?php 
include("sessionCheck.php");
include("PHPconnectionDB.php");
if (!sessionCheck()) {
	header('Location: loginModule.php');

} else {
	
	if (isset($_POST['submit']))  {	

		echo "<h2>Analysis Results</h2>";
		echo"<a href='adminMenu.php'>Administrator menu</a><br>";
		echo"<a href='dataAnalysisModule.php'>Continue Data Analysis</a><br><br>";
		
		error_reporting(E_ALL ^ E_NOTICE);
		$conn = connect();	
		
		//Generate a data cube for the number of records for patient_name,
		//test_type, and time (from test_date)	
		$patient = $_POST['patient'];
		$test_type = $_POST['test_type'];
		$period = $_POST['period'];

		//Create query criteria string based upon form selections
		//Group by rollup, grouping organization priority: patient_id (when selected), 
		//test_type (when selected), and time period (daily, weekly, monthly, or yearly) 
		if ($patient != "") {
			$patient_format = "R.PATIENT_ID, ";
		}
		if ($test_type != "") {
			$test_format = "R.TEST_TYPE, ";
		}	
		
		switch ($period) {
			case "month":
				$date_format = "'Mon YYYY'";
				$date_string = "day_year";
				break;
			case "week":
				$date_format = "'WW-YYYY'";
				$date_string = "week-year";
				break;
			case "year":
				$date_format = "'YYYY'";
				$date_string = "year";
				break;
			default: //All (daily)
				$date_format .= "'Mon DD, YYYY '";
				$date_string = "full_date";
		}
		
		$query = "SELECT "
		.$patient_format.$test_format
		."TO_CHAR(R.TEST_DATE, ".$date_format.") as ".$date_string
		." ,COUNT(*) as images "
		."FROM RADIOLOGY_RECORD R, PACS_IMAGES P "
		."WHERE R.RECORD_ID = P.RECORD_ID "
		."GROUP BY ROLLUP ("
		.$patient_format.$test_format
		."R.TEST_DATE) ";
		
		$stid = oci_parse($conn, $query);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Select [" . $e['message'] . "]";
		}
		
		//Display selected data
		echo "<table>";
		$number_columns = oci_num_fields($stid);
		for ($i = 1; $i <= $number_columns; ++$i) {
			echo " <th style='text-align:center'>".strtolower(oci_field_name($stid, $i))."</th>";
		}
		while ($row = oci_fetch_array($stid, OCI_NUM)) {
			echo "<tr>";
			//foreach ($row as $item) {
				//echo "<td>". ($item !== null ? htmlentities($item, ENT_QUOTES):".") . "</td>";
			//}
			for ($i = 0; $i < $number_columns; $i++) {
				echo "<td style='text-align:center'>" . ($row[$i] != null ? $row[$i] : "-------"). "</td>";
			}
			echo "</tr>";			
		}
		echo "</table>";
		
		oci_close($conn);
	} else {
		//Display analysis options form
		?>
		<h2>Data Analysis Module</h2>
		<a href='adminMenu.php'> Administrator menu</a>
		<form name="reportQuery" method="post" action="dataAnalysisModule.php">
		<p>
			Select time period:<br><br>
			<input type="radio" name="period" value = "all" checked>Day
			<input type="radio" name="period" value = "week" >Week
			<input type="radio" name="period" value = "month">Month
			<input type="radio" name="period" value = "year">Year
		</p>
		Selection criteria:<br><br>
		<input type="checkbox" name="patient" value="patient">Patient<br>
  		<input type="checkbox" name="test_type" value="test_type">Test Type<br>
		<br><br>
	
		<input type="submit" name="submit" value="Submit"/>
		<!-- Cancel redirects to administrator menu -->
		<input type="button" name="Cancel" value="Cancel"		
		onclick="window.location = 'adminMenu.php'"/>
		
		</form>
		<?php
	} 
}	
?>
</body>
</html>		