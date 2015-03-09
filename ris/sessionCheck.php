<?php
/*
 *  Confirms that there is an active session. Returns true if so, false if no 
 *  session is active.
 *
 *  Author: Costa Zervos
 *  Notes:  Code adapted from
 *  http://stackoverflow.com/questions/1243150/php-sessions-to-authenticate-user-on-login-form
 */
    function sessionCheck()
    {
        session_start();
        session_regenerate_id();
        if(!isset($_SESSION['user'])) {
            return false;
        }
        return true;
    }
?>
