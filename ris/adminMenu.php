<html>
    <body>
    <!-- TEMP TEXT -->
    Temp Admin Menu <br/>
    <?php
        include("sessionCheck.php");
        include("userInfoDisplay.php");
        if (!sessionCheck()) {
            echo 'Not logged in! <br/>';
        }
        else {
            userInfoDisplay();
        }
    ?>
    
    <p>
    <a href="manageUsers.php">User Management</a>
    </p>
    </body>
</html>
