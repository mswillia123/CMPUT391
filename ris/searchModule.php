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
        include("displayTable.php");
        include("searchFunctions.php");

        if (sessionCheck()) {
            userInfoDisplay();
            // Search information recieved
            if (isset($_POST['validate'])) {
                $keywords = $_POST['keywords'];
                $ordering = $_POST['ranking'];
                $time_ref = $_POST['time_ref'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $pattern = '/^[1-2][0-9][0-9][0-9][\-][0-1][0-9][\-][0-3][0-9]$/';

                // Validate input
                if (verifyInput($keywords, $ordering, $start_date, $end_date, $pattern)) {
                    $conn = connect();

                    $sql = generateSearchQuery($_SESSION['userType'], $_SESSION['person_id'], $keywords, $ordering, $time_ref, $start_date, $end_date);
                    $stid = sqlQuery($conn, $sql);
                    if ($stid) {
                        searchTable($stid);

                        oci_free_statement($stid);
                        oci_close($conn);
                        echo '<br/><a href="searchModule.php">Back</a>';
                    }
                }
            }
            // Enter search information
            else {
                ?>
                <b>Please enter search keywords <u>and/or</u> time period to search:</b> 
                <br/><br/>
                <form name="searchQuery" method="post" action="searchModule.php">
                    <fieldset style="width: 35%">
                        <legend> <b>Search Criteria</b> </legend>
                        <i>Keywords:</i> <input type="text" name="keywords" style="width: 100%"/> <br/>
                        <i>Time Period:</i> <br/>
                        Start Date (yyyy-mm-dd) <input type="text" name="start_date"/> <br/>
                        End Date (yyyy-mm-dd)&nbsp; <input type="text" name="end_date"/> <br/>
                    </fieldset>
                    <fieldset style="width: 35%">
                        <legend> <b>Date Reference</b> </legend>
                        <input type="radio" name="time_ref" value="t_date" checked>Test Date
                        <br>
                        <input type="radio" name="time_ref" value="p_date">Prescription Date
                    </fieldset>
                    <fieldset style="width: 35%">
                        <legend> <b>Search Result Order</b></legend>
                        <input type="radio" name="ranking" value="rank" checked>Relevance (Requires Keywords)
                        <br>
                        <input type="radio" name="ranking" value="time_new">Date (Newest to Oldest)
                        <br>
                        <input type="radio" name="ranking" value="time_old">Date (Oldest to Newest)
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
