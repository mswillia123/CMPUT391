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
                //$sql = 'SELECT u.password, p.first_name, p.last_name, p.address, p.email, p.phone FROM users u, persons p WHERE u.person_id = p.person_id AND u.user_name = \''.$_SESSION['user'].'\'';
				$sql="SELECT * FROM users";
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
					/*
					if (($row = oci_fetch_array($stid, OCI_NUM)) != false) {
                        $password = $row[0];
                        $first_name = $row[1];
                        $last_name = $row[2];
                        $address = $row[3];
                        $email = $row[4];
                        $phone = $row[5];
                        ?>
                    <!-- NEED TO IMPLEMENT userEditExec.php NEXT -->
                    <form name="userEdit" method="post" action="userEditExec.php">
                        firstname : <input type="text" name="first_name" value="<?php echo $first_name; ?>"/> <br/>
                        lastname : <input type="text" name="last_name" value="<?php echo $last_name; ?>"/> <br/>
                        address : <input type="text" name="address" value="<?php echo $address; ?>"/> <br/>
                        email : <input type="text" name="email" value="<?php echo $email; ?>"/> <br/>
                        phone : <input type="text" name="phone" value="<?php echo $phone; ?>"/> <br/>
                        password : <input type="password" name="password" value="<?php echo $password; ?>"/><br/>
                        <input type="submit" name="validate" value="OK"/>
                    </form>
                        <?php
                    }
                    else {
                        echo 'There was an error retrieving your data! <br/>';
                        echo '<a href="loginModule.html">Back to Login</a>';
                    }
					*/
					print '<table border="1">';
					while ($row=oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) { 
					$id=$row["person_id"]; 
					$thing=$row["user_name"]; 
					$options.="<OPTION VALUE=\"$id\">".$thing.'</OPTION>';
					print '<tr>';
					foreach ($row as $item) {
					   print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
					}
					print '<td>'. $thing.'</td>';
					echo $thing;
					print '</tr>';
					}
					print '</table>';
					

					
					/*
					echo "<select name='person_id'>";
					while ($row = oci_fetch_array($stid, OCI_NUM)) {
						echo "<option value='" . $row['user_name'] . "'>" . $row['user_name'] . "</option>";
					}
					echo "</select>";
					*/
                }
            }              
        ?>
		
							<SELECT NAME=thing> 
					<OPTION VALUE=0>Choose 
					<?=$options?> 
					</SELECT> 

    </body>
</html>
