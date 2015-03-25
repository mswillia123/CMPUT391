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
            echo '<a href="searchModule.php">Search</a><br/>';
        }
    ?>
    </body>
</html>
