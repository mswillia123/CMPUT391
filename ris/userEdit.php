<html>
    <body>
        <?php
            include("sessionCheck.php");
            include("PHPconnectionDB.php");
            if (!sessionCheck()) {
                echo 'Not logged in! <br/>';
            }
            else {
                // Establishes connection with database
                $conn = connect();
                // 0 password, 1 first_name, 2 last_name, 3 address, 4 email, 5 phone
                $sql = 'SELECT u.password, p.first_name, p.last_name, p.address, p.email, p.phone FROM users u, persons p WHERE u.person_id = p.person_id AND u.user_name = \''.$_SESSION['user'].'\'';
                //echo S_SESSION['user'];
                //$sql = 'SELECT * FROM users, persons WHERE users.person_id = persons.person_id AND user_name = \'leo\'';
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
                        ?>
                    <!-- NEED TO IMPLEMENT userEditExec.php NEXT -->
                    <form name="userEdit" method="post" action="userEditExe.php">
                        firstname : <input type="text" name="first_name" value="<?php echo $row[1]; ?>"/> <br/>
                        lastname : <input type="text" name="last_name" value="<?php echo $row[2]; ?>"/> <br/>
                        address : <input type="text" name="address" value="<?php echo $row[3]; ?>"/> <br/>
                        email : <input type="text" name="email" value="<?php echo $row[4]; ?>"/> <br/>
                        phone : <input type="text" name="phone" value="<?php echo $row[5]; ?>"/> <br/>
                        password : <input type="password" name="password" value="<?php echo $row[0]; ?>"/><br/>
                        <input type="submit" name="validate" value="OK"/>
                    </form>
                        <?php
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
