<!-- 
    Assess user's input to determine if user credentials are correct then 
    redirects user to appropriate page.
    
    Author: Costa Zervos
    Notes:  Database connection code adapted from CMPUT 391 Lab 6 
            PHPexample3.html.
-->
<html>
    <body>
        <?php
            include("PHPconnectionDB.php");
            session_start();

            // Retrieves data from login form
            if (isset($_POST['validate'])) {
                $USERNAME = $_POST['username'];
                $PASSWORD = $_POST['password'];

                // Establishes connection with database
                $conn = connect();

				// Prepares SQL query to retrieve credentials
                $sql = 'SELECT * FROM users WHERE user_name = \''.$USERNAME.'\' AND password = \''.$PASSWORD.'\'';
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
                    // Stores user's information in session
                    $_SESSION['user'] = $USERNAME;
                    $_SESSION['userType'] = $row[2];
                    // Directs user type to correct page
                    if ($row[2] == 'a') {
                        header('Location: adminMenu.php');
                    }
                    else if ($row[2] == 'r') {
                        header('Location: radiologistMenu.html');
                    }
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
