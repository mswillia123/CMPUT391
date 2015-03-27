<!-- 
    Login form that asks for user's credentials. Assess user's input to 
    determine if user credentials are correct then redirects user to 
    appropriate page.
    
    Author: Costa Zervos
    Notes:  Code adapted from CMPUT 391 Lab 6 PHPexample3.html
-->
<html>
    <body>
        <h2>Welcome to Radiology Information System</h2>
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
            $sql = 'SELECT * FROM users WHERE user_name = \'' . $USERNAME . '\' AND 
            password = \'' . $_POST['password'] . '\'';
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
                    } else if ($row[2] == 'r') {
                        header('Location: radiologistMenu.php');
                    } else {
                        header('Location: searchModule.php');
                    }
                }
                // Incorrect credentials
                else {
                    echo 'Incorrect credentials! <br/>';
                    echo '<a href="loginModule.php">Back to Login</a>';
                }

                // Free the statement identifier when closing the connection
                oci_free_statement($stid);
                oci_close($conn);
            }
        }
        // Display login
        else {
            ?>
            <form name="login" method="post" action="loginModule.php">
                username : <input type="text" name="username"/> <br/>
                password : <input type="password" name="password"/><br/>
                <input type="submit" name="validate" value="OK"/>
            </form>
            <?php
        }
        ?>
    </body>
</html>
