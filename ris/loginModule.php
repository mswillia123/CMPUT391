<!-- 
    Assess user's input to determine if user credentials are correct then 
    redirects user to appropriate page.
    
    Author: Costa Zervos
-->
<html>
<body>
<?php
    include("PHPconnectionDB.php");
    include("sqlQuery.php");
    // Starts a user session
    session_start();

    // Retrieves data from login form
    if (isset($_POST['validate'])) {
        $USERNAME = $_POST['username'];

        // Establishes connection with database
        $conn = connect();
        // Prepares SQL query to retrieve credentials
        $sql = 'SELECT * FROM users WHERE user_name = \''.$USERNAME.'\' AND 
            password = \''.$_POST['password'].'\'';
        // Executes sql query
        $stid = sqlQuery($conn, $sql);
        
        if ($stid) {
            // Correct credentials
            if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                // Stores user's information in session
                $_SESSION['user'] = $USERNAME;
                $_SESSION['userType'] = $row[2];
                $_SESSION['person_id'] = $row[3];
                // Directs usertype to correct page
                if ($row[2] == 'a') {
                    header('Location: adminMenu.php');
                }
                else if ($row[2] == 'r') {
                    header('Location: radiologistMenu.php');
                }
                else {
                    header('Location: searchModule.php');
                }
            }
            // Incorrect credentials
            else { 
                echo 'Incorrect credentials! <br/>';
                echo '<a href="loginModule.html">Back to Login</a>';
            }

            // Free the statement identifier when closing the connection
            oci_free_statement($stid);
            oci_close($conn);
        }
    }
?>
</body>
</html>
