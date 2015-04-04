<?php
/*
 *  Displays the logged in user's name, user type, a link to logout, and a link
 *  to the user documentation.
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
        echo ' - ';
        echo '<a href="https://docs.google.com/document/d/16ihhOwVXXW_k72kDrEZBDumvuH4eqr3jaat82jAxKio/edit?usp=sharing" target="_blank">Help</a>';
        echo'<br/><br/>';
    }
?>
