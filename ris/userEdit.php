<!-- 
    Generates the form in which the user can see their current personal
    information and password, and edit it. Then Updates user's personal 
    information and password with data from edit form.
    
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
            // Establishes connection with database
            $conn = connect();

            // Update user's information with recieved data
            if (isset($_POST['validate'])) {
                // Checks for a NULL password
                if (trim($_POST['password']) != '') {
                    // Querying person_id of user in session
                    $sql = 'SELECT person_id 
                        FROM users 
                        WHERE user_name = \'' . $_SESSION['user'] . '\'';
                    $stid = sqlQuery($conn, $sql);

                    if ($stid) {
                        if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                            $PersonID = $row[0];
                            oci_free_statement($stid);
                            $patient_name = $_POST['first_name'] . " " . $_POST['last_name'];
                            // Updating user's personal information
                            $sql = 'UPDATE persons 
                                SET first_name = \'' . $_POST['first_name'] . '\', 
                                    last_name = \'' . $_POST['last_name'] . '\', 
                                    address = \'' . $_POST['address'] . '\', 
                                    email = \'' . $_POST['email'] . '\', 
                                    phone = \'' . $_POST['phone'] . '\' 
                                WHERE person_id = ' . $PersonID;
                            $stid = sqlQuery($conn, $sql);

                            if ($stid) {
                                oci_free_statement($stid);
                                // Updating user's password
                                $sql = 'UPDATE users 
                                    SET password = \'' . $_POST['password'] . '\' 
                                    WHERE user_name = \'' . $_SESSION['user'] . '\'';
                                $stid = sqlQuery($conn, $sql);

                                if ($stid) {
                                    // Update patient_name in radiology_search
                                    oci_free_statement($stid);
                                    $sql = "SELECT record_id
                                        FROM radiology_search
                                        WHERE patient_id = '" . $PersonID . "'";
                                    $stid = sqlQuery($conn, $sql);

                                    if ($stid) {
                                        while ($row = oci_fetch_array($stid, OCI_NUM)) {
                                            // Update each record in radiology_search relevant to patient
                                            $sql2 = 'UPDATE radiology_search
                                                 SET patient_name = \'' . $patient_name . '\'
                                                 WHERE record_id = ' . $row[0];
                                            $stid2 = sqlQuery($conn, $sql2);
                                            oci_free_statement($stid2);
                                        }
                                        oci_free_statement($stid);
                                        echo "Account Information Updated!<br/>";
                                        // Back link
                                        if ($_SESSION['userType'] == 'r') {
                                            echo '<a href="radiologistMenu.php">Back</a>';
                                        } else if ($_SESSION['userType'] == 'a') {
                                            echo '<a href="adminMenu.php">Back</a>';
                                        } else {
                                            echo '<a href="searchModule.php">Back</a>';
                                        }
                                    }
                                }
                            }
                        } else {
                            echo 'There was an error retrieving your data! <br/>';
                            echo '<a href="loginModule.html">Back to Login</a>';
                        }
                    }
                } else {
                    echo "Cannot have a null Password! Nothing updated.<br/>";
                    // Back link
                    if ($_SESSION['userType'] == 'r') {
                        echo '<a href="radiologistMenu.php">Back</a>';
                    } else if ($_SESSION['userType'] == 'a') {
                        echo '<a href="adminMenu.php">Back</a>';
                    } else {
                        echo '<a href="searchModule.php">Back</a>';
                    }
                }
            } 
            // Display form allowing user to edit user info
            else {
                // 0 password, 1 first_name, 2 last_name, 3 address, 4 email, 5 phone
                $sql = 'SELECT u.password, p.first_name, p.last_name, p.address, 
                       p.email, p.phone FROM users u, persons p 
                WHERE u.person_id = p.person_id 
                AND u.user_name = \'' . $_SESSION['user'] . '\'';
                $stid = sqlQuery($conn, $sql);

                if ($stid) {
                    if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                        ?>
                        <form name="userEdit" method="post" action="userEdit.php">
                            firstname : <input type="text" name="first_name" 
                                               value="<?php
                        if (isset($row[1])) {
                            echo $row[1];
                        }
                        ?>"/> <br/>
                            lastname : <input type="text" name="last_name" 
                                              value="<?php
                if (isset($row[2])) {
                    echo $row[2];
                }
                        ?>"/> <br/>
                            address : <input type="text" name="address" 
                                             value="<?php if (isset($row[3])) { echo $row[3]; } ?>"/> <br/>
                            email : <input type="text" name="email" 
                                           value="<?php if (isset($row[4])) { echo $row[4]; } ?>"/> <br/>
                            phone : <input type="text" name="phone" 
                                           value="<?php if (isset($row[5])) { echo $row[5]; } ?>"/> <br/>
                            password : <input type="password" name="password" 
                                              value="<?php if (isset($row[0])) { echo $row[0]; } ?>"/><br/>
                            <input type="submit" name="validate" value="OK"/>
                            <!-- Cancel redirects to usertype's mainpage -->
                            <input type="button" name="Cancel" value="Cancel" 
                            <?php
                            if ($_SESSION['userType'] == 'a') {
                                echo 'onclick="window.location = 
                                  \'adminMenu.php\' "';
                            } else if ($_SESSION['userType'] == 'r') {
                                echo 'onclick="window.location = 
                                  \'radiologistMenu.php\' "';
                            } else {
                                echo 'onclick="window.location = 
                                  \'searchModule.php\' "';
                            }
                            ?>
                                   />
                        </form>
                        <?php
                    } else {
                        echo 'There was an error retrieving your data! <br/>';
                        echo '<a href="loginModule.html">Back to Login</a>';
                    }
                }
                oci_free_statement($stid);
            }
            oci_close($conn);
        }
        ?>
    </body>
</html>
