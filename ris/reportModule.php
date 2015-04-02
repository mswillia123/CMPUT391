<!-- 
    Generates report list for administrator.
    
    Author: Costa

    Code for displaying date in form adapted from:
    http://stackoverflow.com/questions/5687637/ask-users-to-input-the-date-using-the-yyyy-mm-dd-format
    Author: Pascal MARTIN
    Date: 3/21/15
-->
<html>
    <body>
        <h2>Report Generating Module</h2>
        <?php
        include("PHPconnectionDB.php");
        include("sqlQuery.php");
        include("sessionCheck.php");
        include("userInfoDisplay.php");
        include("displayTable.php");

        if (sessionCheck()) {
            userInfoDisplay();
            // Report information recieved
            if (isset($_POST['validate'])) {
                $pattern = '/^[1-2][0-9][0-9][0-9][\-][0-1][0-9][\-][0-3][0-9]$/';
                $diagnosis = $_POST['diagnosis'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                // Verify input
                if ($diagnosis == '') {
                    //header("Location: reportModule.php");
                    echo 'Please enter a diagnosis.<br/>';
                    echo '<a href="reportModule.php">Back</a>';
                } else if (!preg_match($pattern, $start_date)) {
                    echo 'Please enter a valid starting time period.<br/>';
                    echo '<a href="reportModule.php">Back</a>';
                } else if (!preg_match($pattern, $end_date)) {
                    echo 'Please enter a valid ending time period.<br/>';
                    echo '<a href="reportModule.php">Back</a>';
                } else {
                    $conn = connect();
                    /*
                     * Displays the information of the patient who has recieved
                     * the specified diagnosis within the specified timeframe.
                     * In cases where the patient has multiple reports with the
                     * same diagnosis, only the earliest test date is displayed
                     * as per speficications. Orders the list from oldest test 
                     * date to newest.
                     * 
                     * 0 last_name, 1 first_name, 2 address, 3 phone, 4 test_date
                     * 
                     */
                    $sql = "SELECT p.last_name, p.first_name, p.address, p.phone, MIN(r.test_date) "
                            . "FROM persons p, radiology_record r "
                            . "WHERE p.person_id = r.patient_id "
                            . "AND r.diagnosis = '" . $diagnosis . "' "
                            . "AND r.test_date >= TO_DATE('" . $start_date . "','yyyy-mm-dd') "
                            . "AND r.test_date <= TO_DATE('" . $end_date . "','yyyy-mm-dd') "
                            //. "ORDER BY r.test_date DESC";
                            . "GROUP BY p.last_name, p.first_name, p.address, p.phone "
                            . "ORDER BY MIN(r.test_date)";
                    $stid = sqlQuery($conn, $sql);

                    if ($stid) {
                        reportTable($stid);

                        oci_free_statement($stid);
                        oci_close($conn);
                        echo '<br/><a href="reportModule.php">Back</a>';
                    }
                }
                // Enter report information
            } else {
                $date = date('Y-m-d');
                ?>
                <b>Please enter a diagnosis and a time period:</b> 
                <br/><br/>
                <form name="reportQuery" method="post" action="reportModule.php">
                    Diagnosis : <input type="text" name="diagnosis" /> <br/>
                    Time Period Start (yyyy-mm-dd): <input type="text" name="start_date" 
                                                           value="<?php echo date('Y-m-d'); ?>"/> <br/>
                    Time Period End (yyyy-mm-dd): <input type="text" name="end_date" 
                                                         value="<?php echo date('Y-m-d'); ?>"/> <br/>
                    <input type="submit" name="validate" value="OK"/>
                    <!-- Cancel redirects to administrator menu -->
                    <input type="button" name="Cancel" value="Cancel" 
                           onclick="window.location = 'adminMenu.php'"/>
                </form>
                <?php
            }
        }
        ?>
    </body>
</html>
