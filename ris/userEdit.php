<!-- 
    Generates the form in which the user can see their current personal
    information and password, and edit it.
    
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
        // Establishes connection with database
        $conn = connect();
        // 0 password, 1 first_name, 2 last_name, 3 address, 4 email, 5 phone
        $sql = 'SELECT u.password, p.first_name, p.last_name, p.address, 
                       p.email, p.phone FROM users u, persons p 
                WHERE u.person_id = p.person_id 
                AND u.user_name = \''.$_SESSION['user'].'\'';
        $stid = sqlQuery($conn, $sql);

        if ($stid) {
            if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
?>
            <form name="userEdit" method="post" action="userEditExe.php">
                firstname : <input type="text" name="first_name" 
                                   value="<?php echo $row[1]; ?>"/> <br/>
                lastname : <input type="text" name="last_name" 
                                  value="<?php echo $row[2]; ?>"/> <br/>
                address : <input type="text" name="address" 
                                 value="<?php echo $row[3]; ?>"/> <br/>
                email : <input type="text" name="email" 
                               value="<?php echo $row[4]; ?>"/> <br/>
                phone : <input type="text" name="phone" 
                               value="<?php echo $row[5]; ?>"/> <br/>
                password : <input type="password" name="password" 
                                  value="<?php echo $row[0]; ?>"/><br/>
                <input type="submit" name="validate" value="OK"/>
                <!-- Cancel redirects to usertype's mainpage -->
                <input type="button" name="Cancel" value="Cancel" 
                    <?php
                        if ($_SESSION['userType'] == 'a') {
                            echo 'onclick="window.location = 
                                  \'adminMenu.php\' "';
                        }
                        else if ($_SESSION['userType'] == 'r') {
                            echo 'onclick="window.location = 
                                  \'radiologistMenu.php\' "';
                        }
                        else {
                            echo 'onclick="window.location = 
                                  \'searchModule.php\' "';
                        }
                    ?>
                />
            </form>
<?php
            }
            else {
                echo 'There was an error retrieving your data! <br/>';
                echo '<a href="loginModule.html">Back to Login</a>';
            }
        }
        oci_free_statement($stid);
        oci_close($conn);
    }              
?>
</body>
</html>
