<!--
    Radiologist's menu that displays the radiologist's module options.
-->
<html>
    <body>
    <h2>Radiologist Menu</h2>
    <?php
        include("sessionCheck.php");
        include("userInfoDisplay.php");
        if (sessionCheck()) {
        	userInfoDisplay();
            echo '<a href="manageRadiologyRecords.php">Radiology Records</a><br/>';
            echo '<a href="searchModule.php">Search</a><br/>';
        }
        else {
			header("Location: loginModule.html");
        }
    ?>
    </body>
</html>
