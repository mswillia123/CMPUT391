<html>
    <body>
    <h2>Radiologist Menu</h2>
    <?php
        include("sessionCheck.php");
        include("userInfoDisplay.php");
        if (!sessionCheck()) {
            echo 'Not logged in! <br/>';
        }
        else {
            userInfoDisplay();
            echo '<a href="manageRadiologyRecords.php">Radiology Records</a><br/>';
            echo '<a href="searchModule.php">Search</a><br/>';
        }
    ?>
    </body>
</html>
