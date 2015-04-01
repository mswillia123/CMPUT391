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
            echo '<a href="reportModule.php">Report Generator</a><br/>';
            echo '<a href="searchModule.php">Search</a><br/>';
            echo '<a href="manageUsers.php">User Management</a></br>';
            echo '<a href="analysisModule.php">Data Analysis Module</a><br/>';
            /* use this code instead if only the user 'admin' is allowed access to analysis module
            if ($_SESSION['user'] = 'admin') {
            	echo '<a href="analysisModule.php">Data Analysis Module</a><br/>';
            }
            */
        }
    ?>

    </body>
</html>
