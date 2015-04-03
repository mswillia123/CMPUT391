<!--
    Administrator's menu that displays the administrator's module options.
-->
<html>
    <body>
        <h2>Administrator Menu</h2>
    <?php
        include("sessionCheck.php");
        include("userInfoDisplay.php");
        if (!sessionCheck()) {
            echo 'Not logged in! <br/>';
        }
        else {
            userInfoDisplay();
            echo '<h4>Administrative options</h4>';
            echo '<a href="reportModule.php">Report Generator</a><br/>';
            echo '<a href="searchModule.php">Search</a><br/>';
            echo '<a href="manageUsers.php">User Management</a></br>';
            echo '<a href="dataAnalysisModule.php">Data Analysis Module</a><br/>';
            /* use this code instead if only the user 'admin' is allowed access to analysis module
            if ($_SESSION['user'] = 'admin') {
            	echo '<a href="analysisModule.php">Data Analysis Module</a><br/>';
            }
            */
        }
    ?>

    </body>
</html>
