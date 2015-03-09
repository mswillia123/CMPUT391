<html>
    <body>
    <!-- TEMP TEXT -->
    Temp Search Module <br/>
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
    </body>
</html>
