<!-- 
    Updates user's personal information and password with data from edit 
    form.
    
    Author: Costa Zervos
-->
<html>
<body>
<?php
    include("sessionCheck.php");
    include("PHPconnectionDB.php");
    include("sqlQuery.php");
    if (!sessionCheck()) {
        echo 'Not logged in! <br/>';
    }
    else {
        // Retrieves data from userEdit form
        if (isset($_POST['validate'])) {
            $conn = connect();
            // Querying person_id of user in session
            $sql = 'SELECT person_id 
                    FROM users 
                    WHERE user_name = \''.$_SESSION['user'].'\'';
            $stid = sqlQuery($conn, $sql);
            
            if ($stid) {
                if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                    $PersonID = $row[0];
                    oci_free_statement($stid);
                    // Updating user's personal information
                    $sql = 'UPDATE persons 
                            SET first_name = \''.$_POST['first_name'].'\', 
                                last_name = \''.$_POST['last_name'].'\', 
                                address = \''.$_POST['address'].'\', 
                                email = \''.$_POST['email'].'\', 
                                phone = \''.$_POST['phone'].'\' 
                            WHERE person_id = '.$PersonID;
                    $stid = sqlQuery($conn, $sql);

                    if ($stid) {
                        oci_free_statement($stid);
                        // Updating user's password
                        $sql = 'UPDATE users 
                                SET password = \''.$_POST['password'].'\' 
                                WHERE user_name = \''.$_SESSION['user'].'\'';
                        $stid = sqlQuery($conn, $sql);

                        if ($stid) {
                            oci_free_statement($stid);
                            echo "Account Information Updated!<br/>";
                            // Back link
                            if ($_SESSION['userType'] == 'r') {
                                echo '<a href="radiologistMenu.php">Back</a>';
                            }
                            else if ($_SESSION['userType'] == 'a') {
                                echo '<a href="adminMenu.php">Back</a>';
                            }
                            else {
                                echo '<a href="searchModule.php">Back</a>';
                            }
                        }
                    }
                    
                }
                else {
                   echo 'There was an error retrieving your data! <br/>';
                   echo '<a href="loginModule.html">Back to Login</a>';
                }
            }
            oci_close($conn);
        }
    }
?>
</body>
</html>
