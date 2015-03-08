<html>
	<body>
		<?php
			include("PHPconnectionDB.php");

			// Retrieves data from login form
			if (isset($_POST['validate'])) {
				$USERNAME = $_POST['username'];
				$PASSWORD = $_POST['password'];

				// Establishes connection with database
				$conn = connect();

				// Prepares SQL query to retrieve credentials
				$sql = 'SELECT * FROM users WHERE user_name = \''.$USERNAME.'\' AND password = \''.$PASSWORD.'\'';
				//$sql = 'SELECT user_name FROM users';
           		// Prepare sql using conn and returns the statement identifier
           		$stid = oci_parse($conn, $sql);
           
           		// Execute a statement returned from oci_parse()
           		$res = oci_execute($stid);

				//if error, retrieve the error using the oci_error() function & output an error
				if (!$res) {
					$err = oci_error($stid);
					echo htmlentities($err['message']);
           		} 

				// Correct credentials
	   			if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
					echo $row[0].' logged in correctly! <br/>';
	   			}
				// Incorrect credentials
				else { echo 'Incorrect credentials! <br/>'; }

				// Free the statement identifier when closing the connection
			    oci_free_statement($stid);
			    oci_close($conn);
			}
		?>
	</body>
</html>
