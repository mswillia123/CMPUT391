<html>
    <body>
        <?php
            include("sessionCheck.php");
            include("PHPconnectionDB.php");
            if (!sessionCheck()) {
                echo 'Not logged in! <br/>';
            }
            else {

                // Retrieves data from userEdit form
                if (isset($_POST['validate']))
                    // Establishes connection with database
                    $conn = connect();
                    $sql = 'SELECT person_id FROM users WHERE user_name = \''.$_SESSION['user'].'\'';
                    
                    // Prepare sql using conn and returns the statement identifier
                    $stid = oci_parse($conn, $sql);
                
                    // Execute a statement returned from oci_parse()
                    $res = oci_execute($stid);
                    
                    //if error, retrieve the error using the oci_error() function & output an error
                    if (!$res) {
                        $err = oci_error($stid);
                        echo htmlentities($err['message']);
                    }
                    else {
                        if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                            $PersonID = $row[0];
                            
                            oci_free_statement($stid);
                            $sql = 'UPDATE persons SET first_name = \''.$_POST['first_name'].'\', last_name = \''.$_POST['last_name'].'\', address = \''.$_POST['address'].'\', email = \''.$_POST['email'].'\', phone = \''.$_POST['phone'].'\' WHERE person_id = '.$PersonID;
                            
                            $stid = oci_parse($conn, $sql);
                            $res = oci_execute($stid);
                            if (!$res) {
                               $err = oci_error($stid);
                               echo htmlentities($err['message']);
                            }
                            else {
                                oci_free_statement($stid);
                                $sql = 'UPDATE users SET password = \''.$_POST['password'].'\' WHERE user_name = \''.$_SESSION['user'].'\'';
                                $stid = oci_parse($conn, $sql);
                                $res = oci_execute($stid);
                                if (!$res) {
                                    $err = oci_error($stid);
                                    echo htmlentities($err['message']);
                                }
                                else {
                                    echo "Data updated!<br/>";
                                }
                            }
                            
                        }
                        else {
                           echo 'There was an error retrieving your data! <br/>';
                           echo '<a href="loginModule.html">Back to Login</a>';
                        }
                    }
                }
        ?>

    </body>
</html>
