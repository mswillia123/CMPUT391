<?php
/*
 *  Confirms that there is an active session. Returns true if so, false if no 
 *  session is active.
 *
 *  Author: Costa Zervos
 *  Notes:  Code adapted from
 *  http://stackoverflow.com/questions/1243150/php-sessions-to-authenticate-user-on-login-form
 */
    function sqlQuery($conn, $sql)
    {
        $stid = oci_parse($conn, $sql);
        $res = oci_execute($stid);
        if (!$res) {
            $err = oci_error($stid);
            echo htmlentities($err['message']);
            return 0;
        }
        else {
            return $stid;
        }
    }
?>
