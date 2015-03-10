<?php
/*
 *  Displays the logged in user's name, user type, and a link to logout.
 *
 *  Author: Costa Zervos
 */
    function userInfoDisplay()
    {
        $_UserType = $_SESSION['userType'];
        
        // Displays username
        echo $_SESSION['user'];
        // Displays usertype
        if ($_UserType == 'a') { echo ' - Administrator - '; }
        else if ($_UserType == 'd') { echo ' - Doctor - '; }
        else if ($_UserType == 'r') { echo ' - Radiologist - '; }
        else { echo ' - Patient - '; }
        // Displays an edit user link
        echo '<a href="userEdit.php">Edit</a>';
        echo ' - ';
        // Displays logout link
        echo '<a href="logout.php">Logout</a>';
    }
?>
