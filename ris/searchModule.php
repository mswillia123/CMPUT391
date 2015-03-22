<!-- 
    Search radiology records.
    
    Author: Costa
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
