<!--
	Data Analysis Module:
	Allows system administrator to create an OLAP report
	Information generated as a datacube on patient_name, test_type, and test_date
	Display selectable by patient_name (optional), test_type (optional), and test_date
	Generalization allows roll up and drill down on time periods weekly, monthly, yearly, or full data (daily)
	
	Author: Michael Williams
	
	Code reference: http://docs.oracle.com/cd/B19306_01/server.102/b14223/aggreg.htm#i1007413
	Author: Oracle
-->


<html>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<head><link href="base.css" rel="stylesheet" type="text/css"></head>
	<body>
		<?php 

		/*
		// warning handler prevents system printing of warning messages
		// this is handled individually at the code level
		function warning_handler($errno, $errstr) { 
			//no message to be printed, 
		}
		set_error_handler("warning_handler", E_WARNING);
		*/

		include("sessionCheck.php");
		include("PHPconnectionDB.php");
		if (!sessionCheck()) {
			header('Location: loginModule.php');

		} else {

			//Drop fact table on first entry to dataAnalysisModule
			//to account for any data changes since last visit
			//to data analysis page
			$conn = connect();	
			$query = "DROP VIEW fact_table";
			$stid = oci_parse($conn, $query);
			$res  = oci_execute($stid);
			if ($res) {
				oci_commit($conn);
			} else {
				$e = oci_error($stid);
				echo "Error Selecting data [" . $e['message'] . "]";
			}
			oci_free_statement($stid);
			oci_close($conn);

			//Create fact_table to speed up OLAP queries for data_cube/roll-up
			//Contains only data relevant to the queries:
			//Radiology record columns:patient_id, test_type, test_date where the record_id has
			//images associated with it in pacs_images
			$conn = connect();
			$query ="CREATE VIEW fact_table AS "
			."SELECT R.PATIENT_ID, R.TEST_DATE, R.TEST_TYPE "
			."FROM RADIOLOGY_RECORD R, PACS_IMAGES P "
			."WHERE R.RECORD_ID = P.RECORD_ID ";
			$stid = oci_parse($conn, $query);
			$res  = oci_execute($stid);
			if ($res) {
				oci_commit($conn);
			} else {
				$e = oci_error($stid);
				echo "Error Selecting data [" . $e['message'] . "]";
			}
			oci_free_statement($stid);
			oci_close($conn);


			//On POST (submit button selected):
			//Generate a data analysis by querying the fact_table for the number of images for patient_name,
			//test_type, and/or time period (from test_date) based on user selected criteria
			if (isset($_POST['submit']))  {	

				echo "<h2>Analysis Results</h2>";
				echo"<a href='adminMenu.php'>Administrator menu</a><br>";
				echo"<a href='dataAnalysisModule.php'>Continue Data Analysis</a><br><br>";
	
				error_reporting(E_ALL ^ E_NOTICE);
				$conn = connect();	
	

				$patient = $_POST['patient'];
				$test_type = $_POST['test_type'];
				$period = $_POST['period'];
	
				if ($patient != "") {
					$patient_format = "PATIENT_ID, ";
				}
				if ($test_type != "") {
					$test_format = "TEST_TYPE, ";
				}	
	
				switch ($period) {
					case "month":
						$date_format = "TO_CHAR(TEST_DATE, 'YYYY') as year, TO_CHAR(TEST_DATE, 'Mon') as month";
						$date_rollup = "TO_CHAR(TEST_DATE, 'YYYY'), TO_CHAR(TEST_DATE, 'Mon')";
						break;
					case "week":
						//if month is required along with week, use the following instead
						//$date_format = "TO_CHAR(R.TEST_DATE, 'YYYY') as year, TO_CHAR(R.TEST_DATE, 'Mon') as month, TO_CHAR(R.TEST_DATE, 'W') as week";
						//$date_rollup = "TO_CHAR(R.TEST_DATE, 'YYYY'), TO_CHAR(R.TEST_DATE, 'Mon'), TO_CHAR(R.TEST_DATE, 'W')";

						$date_format = "TO_CHAR(TEST_DATE, 'YYYY') as year, TO_CHAR(TEST_DATE, 'WW') as week";
						$date_rollup = "TO_CHAR(TEST_DATE, 'YYYY'), TO_CHAR(TEST_DATE, 'WW')";
						break;
					case "year":
						$date_format ="TO_CHAR(TEST_DATE, 'YYYY') as year";
						$date_rollup ="TO_CHAR(TEST_DATE, 'YYYY')";
						break;
					default: //as per Dr. Yuan, day/all should not be an option. This code can be uncommented if day/all report is required
						//$date_format = "TO_CHAR(TEST_DATE, 'YYYY') as year, TO_CHAR(TEST_DATE, 'Mon') as month, TO_CHAR(TEST_DATE, 'DD') as day";			
						//$date_rollup = "TO_CHAR(TEST_DATE, 'YYYY'), TO_CHAR(TEST_DATE, 'Mon'), TO_CHAR(TEST_DATE, 'DD')";
				}
	
				$query = "SELECT "
				.$patient_format.$test_format.$date_format
				." ,COUNT(*) as images "
				."FROM fact_table "
				."GROUP BY ROLLUP ("
				.$patient_format.$test_format.$date_rollup
				.") ";
	
				$stid = oci_parse($conn, $query);
				$res  = oci_execute($stid);
				if ($res) {
					oci_commit($conn);
				} else {
					$e = oci_error($stid);
					echo "Error Selecting data [" . $e['message'] . "]";
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
					<!-- as per Dr. Yuan, day/all should not be an option, can be uncommented if day/all is required
					<input type="radio" name="period" value = "all" checked>Day
					-->

					<input type="radio" name="period" value = "week" >Week
					<input type="radio" name="period" value = "month">Month
					<input type="radio" name="period" value = "year" checked >Year
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
