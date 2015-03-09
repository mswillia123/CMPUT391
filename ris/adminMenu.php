<html>
    <body>
    <?php
        session_start();
        session_regenerate_id();
        if(!isset($_SESSION['user'])) {
            echo 'Incorrect session <br/>';
        }
        else {
            echo 'User: '.$_SESSION['user'].'<br/>';
            echo 'User type: '.$_SESSION['userType'].'<br/>';
            echo '<a href="logout.php">Logout</a>';
        }
    ?>
    </body>
</html>
