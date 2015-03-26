<!-- 
    Updates user's personal information and password with data from edit 
    form.
    
    Author: Costa Zervos
-->
<html>
<body>
<h2>Edit User</h2>
<?php
    include("sessionCheck.php");
    include("PHPconnectionDB.php");
    include("sqlQuery.php");
    if (sessionCheck()) {
        $conn = connect();
        // Retrieves data from userEdit form
        if (isset($_POST['validate'])) {
            // Checks for a NULL password
            if (trim($_POST['password'])!='') {
                // Querying person_id of user in session
                $sql = 'SELECT person_id 
                        FROM users 
                        WHERE user_name = \''.$_SESSION['user'].'\'';
                $stid = sqlQuery($conn, $sql);
                
                if ($stid) {
                    if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                        $PersonID = $row[0];
                        oci_free_statement($stid);
                        $patient_name = $_POST['first_name']." ".$_POST['last_name'];
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
                                // Update patient_name in radiology_search
                                oci_free_statement($stid);
                                $sql = "SELECT record_id
                                        FROM radiology_search
                                        WHERE patient_id = '".$PersonID."'";
                                $stid = sqlQuery($conn, $sql);
                                
                                if ($stid) {
                                    while ($row = oci_fetch_array($stid, OCI_NUM)) {
                                        $sql2 = 'UPDATE radiology_search
                                                 SET patient_name = \''.$patient_name.'\'
                                                 WHERE record_id = '.$row[0];
                                        $stid2 = sqlQuery($conn, $sql2);
                                        oci_free_statement($stid2);
                                    }
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
                    }
                    else {
                       echo 'There was an error retrieving your data! <br/>';
                       echo '<a href="loginModule.html">Back to Login</a>';
                    }
                }
            }
            else {
                echo "Cannot have a null Password! Nothing updated.<br/>";
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
        oci_close($conn);
    }
?>
</body>
</html>
