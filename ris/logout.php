<?php
/*
 *  Logs user out of session and returns to the login module.
 *
 *  Author: Costa Zervos
 *  Notes:  Code adapted from
 *  http://stackoverflow.com/questions/1243150/php-sessions-to-authenticate-user-on-login-form
 */
    session_start();
    unset($_SESSION['user']);
    unset($_SESSION['userType']);
    session_destroy();
    header('Location: loginModule.html');
?>
