<!-- 
    Search radiology records.
    
    Author: Costa

    Rank Query SELECT statements adapted from:
    http://stackoverflow.com/questions/8352796/ora-29908-missing-primary-invocation-for-ancillary-operator
    Author: Paul Kar.
    Date: 3/22/15
-->
<html>
    <body>
        <h2>Search Module</h2>
        <?php
        include("PHPconnectionDB.php");
        include("sqlQuery.php");
        include("sessionCheck.php");
        include("userInfoDisplay.php");
        if (sessionCheck()) {
            userInfoDisplay();
            // Search information recieved
            if (isset($_POST['validate'])) {
                $pattern = '/^[1-2][0-9][0-9][0-9][\-][0-1][0-9][\-][0-3][0-9]$/';
                $keywords = $_POST['keywords'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $ordering = $_POST['ranking'];

                /* MIGHT NOT NEED THIS SINCE
                 * CONTAINS FUNCTION AUTOMATICALLY SEPARATES
                 * SPACED INPUT AND CONSIDERS IT A PHRASE
                 */
                // Explodes and trims keywords
                if ($keywords != '') {
                    $keyword_list = explode(',', $keywords);
                    $list_size = count($keyword_list);

                    // echo $list_size;
                    for ($i = 0; $i < $list_size; $i++) {
                        $keyword_list[$i] = trim($keyword_list[$i]);
                        //echo $keyword_list[$i];
                    }
                }
                // Validate input
                if ($keywords == '' and $ordering == 'rank') {
                    echo 'Cannot search by closest match without keywords.<br/>';
                    echo '<a href="searchModule.php">Back</a>';
                } else if (($start_date == '') and ( $end_date != '')) {
                    echo 'Please enter a starting time period.<br/>';
                    echo '<a href="searchModule.php">Back</a>';
                } else if (($start_date != '') and ( $end_date == '')) {
                    echo 'Please enter an ending time period.<br/>';
                    echo '<a href="searchModule.php">Back</a>';
                } else if ($start_date != '' and ! preg_match($pattern, $start_date)) {
                    echo 'Please enter a valid starting time period.<br/>';
                    echo '<a href="searchModule.php">Back</a>';
                } else if ($end_date != '' and ! preg_match($pattern, $end_date)) {
                    echo 'Please enter a valid ending time period.<br/>';
                    echo '<a href="searchModule.php">Back</a>';
                } else {
                    $conn = connect();

                    // 
                    if (($keywords != '') and ( $start_date == '')) {
                        $sql = "SELECT rs.rank, rr.record_id, rs.patient_name, pd.first_name || ' ' || pd.last_name AS doc_name, pr.first_name || ' ' || pr.last_name AS rad_name, rr.test_type, rr.prescribing_date, rr.test_date, rr.diagnosis, rr.description
                            FROM   radiology_record rr, persons pd, persons pr, 
                            (   SELECT record_id, 6*SCORE(1) + 3*SCORE(2) + SCORE(3) AS rank, patient_name
                            FROM   radiology_search
                            WHERE  CONTAINS(patient_name, '" . $keywords . "', 1)>0
                            OR  CONTAINS(diagnosis, '" . $keywords . "', 2)>0
                            OR  CONTAINS(description, '" . $keywords . "', 3)>0
                            ) rs
                            WHERE rr.record_id = rs.record_id
                            AND rr.doctor_id = pd.person_id
                            AND rr.radiologist_id = pr.person_id 
                            ORDER BY rs.rank";

                        $stid = sqlQuery($conn, $sql);

                        if ($stid) {
                            $resultsFlag = 0;
                            while ($row = oci_fetch_array($stid, OCI_NUM)) {
                                // Display table of results
                                if ($resultsFlag == 0) {
                                    ?>
                                    <table border=2 style="width:80%">
                                        <tr>
                                            <th>Rank</th>
                                            <th>Record ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Radiologist</th>
                                            <th>Test Type</th>
                                            <th>Prescribing Date (dd-mmm-yy)</th>
                                            <th>Testing Date (dd-mmm-yy)</th>
                                            <th>Diagnosis</th>
                                            <th>Description</th>
                                        </tr>
                                        <tr>
                                            <?php
                                            for ($i = 0; $i < 10; $i++) {
                                                ?>
                                                <td><?php echo $row[$i]; ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        $resultsFlag = 1;
                                    } else {
                                        ?>
                                        <tr>
                                            <?php
                                            for ($i = 0; $i < 10; $i++) {
                                                ?>
                                                <td><?php echo $row[$i]; ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                if ($resultsFlag == 0) {
                                    echo "No results found!<br/>";
                                } else {
                                    ?>
                                </table>
                                <?php
                            }

                            oci_free_statement($stid);
                            oci_close($conn);
                            echo '<br/><a href="searchModule.php">Back</a>';
                        }
                    }
                }
            }
            // Enter search information
            else {
                ?>
                <b>Please enter search keywords <u>and/or</u> time period to search:</b> 
                <br/><br/>
                <form name="searchQuery" method="post" action="searchModule.php">
                    Search keywords: <input type="text" name="keywords" style="width: 30%"/> <br/>
                    Time Period Start (yyyy-mm-dd): <input type="text" name="start_date"/> <br/>
                    Time Period End (yyyy-mm-dd): <input type="text" name="end_date"/> <br/>
                    <fieldset style="width: 35%">
                        <legend> Result Ordering </legend>
                        <input type="radio" name="ranking" value="rank" checked>Closest Match
                        <br>
                        <input type="radio" name="ranking" value="time_new">Test Date (Most Recent)
                        <br>
                        <input type="radio" name="ranking" value="time_old">Test Date (Oldest)
                    </fieldset>
                    <input type="submit" name="validate" value="OK"/>
                    <!-- Cancel redirects to administrator menu -->
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
                                  \'logout.php\' "';
        }
        ?>
                           />
                </form>
                    <?php
                }
            }
            ?>
    </body>
</html>
