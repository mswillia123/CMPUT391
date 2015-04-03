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
		
		/*Generate a data cube for the number of records for patient_name,
		 * test_type, and time (from test_date)
		 */
		
		/*
		 $query ="SELECT PATIENT_ID, TEST_TYPE, TEST_DATE, COUNT(*) "
		."FROM   RADIOLOGY_RECORD "
		."GROUP BY CUBE (PATIENT_ID, TEST_TYPE, TEST_DATE)";
		*/
		
		/*
	SELECT R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'YYYY'), COUNT(*) 
	FROM   RADIOLOGY_RECORD R, PACS_IMAGES P
	WHERE  R.RECORD_ID = P.RECORD_ID
	GROUP BY CUBE (R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'YYYY'));
  --GROUP BY ROLLUP (to_char(R.TEST_DATE,'MON'))
		 */
		/*
		 * SELECT R.PATIENT_ID, R.TEST_TYPE,TO_CHAR(R.TEST_DATE, 'mon yyyy'), COUNT(*) 
	FROM   RADIOLOGY_RECORD R, PACS_IMAGES P
	WHERE  R.RECORD_ID = P.RECORD_ID
  --SELECT R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'YYYY'), COUNT(*)
	--GROUP BY CUBE (R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'YYYY'));
  GROUP BY ROLLUP(R.PATIENT_ID, R.TEST_TYPE,TO_CHAR(R.TEST_DATE, 'mon yyyy')) --, TO_CHAR(R.TEST_DATE, 'YYYY'))
		  ORDER BY (R.TEST_TYPE)
		 */
		/*
		 * SELECT R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'Mon DD YY'), COUNT(*) 
	FROM   RADIOLOGY_RECORD R, PACS_IMAGES P
	WHERE  R.RECORD_ID = P.RECORD_ID
  --SELECT R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'YYYY'), COUNT(*)
	--GROUP BY CUBE (R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'YYYY'));
  GROUP BY ROLLUP(R.PATIENT_ID, R.TEST_TYPE,TO_CHAR(R.TEST_DATE, 'Mon DD YY')) --, TO_CHAR(R.TEST_DATE, 'YYYY'))
  ORDER BY (R.TEST_TYPE)
		 */
		
		
		$patient = $_POST['patient'];
		$test_type = $_POST['test_type'];
		$period = $_POST['period'];
		//echo $patient;
		if ($patient != "") {
			$patient_format = "R.PATIENT_ID, ";
		}
		if ($test_type != "") {
			$test_format = "R.TEST_TYPE, ";
		}	
		
		switch ($period) {
			//case "all":
			//	code to be executed if n=label1;
			//	break;
			case "month":
				//$date_format = "YYYY Mon";
				$date_format = "'Mon YYYY'";
				$date_string = "day_year";
				break;
			case "week":
				//$date_format = "YYYY WW";
				$date_format = "'WW-YYYY'";
				$date_string = "week-year";
				break;
			case "year":
				//$date_format = "YYYY";
				$date_format = "'YYYY'";
				$date_string = "year";
				break;
			default: //All
				//$date_format = "DD Mon yy";
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
		
		/*
		."ORDER BY "
		.$patient_format.$test_format
		."R.TEST_DATE";
		*/
		
		/*
		 * 		$query ="SELECT R.PATIENT_ID, R.TEST_TYPE, TO_CHAR(R.TEST_DATE, 'Mon yyyy'), COUNT(*) "
		."FROM RADIOLOGY_RECORD R, PACS_IMAGES P "
		."WHERE R.RECORD_ID = P.RECORD_ID "
		."GROUP BY CUBE (PATIENT_ID, TEST_TYPE, TO_CHAR(R.TEST_DATE, 'Mon yyyy'))";
		
		if ($patient_format !="") {
			$query .="R.PATIENT_ID, ";
		}
		if ($test_format != ""){
			$query .="R.TEST_TYPE, ";
		}
		 */

		//echo $query;
		$stid = oci_parse($conn, $query);
		$res  = oci_execute($stid);
		if ($res) {
			oci_commit($conn);
		} else {
			$e = oci_error($stid);
			echo "Error Select [" . $e['message'] . "]";
		}
		
		/*Display data cube
		 * 
		 */
		echo "<table>";
		$number_columns = oci_num_fields($stid);
		for ($i = 1; $i <= $number_columns; ++$i) {
			echo " <th style='text-align:center'>".strtolower(oci_field_name($stid, $i))."</th>";
		}
		while ($row = oci_fetch_array($stid, OCI_NUM)) {
			//https://community.oracle.com/thread/1097016
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