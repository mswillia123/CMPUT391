<?php
/*
 *  Takes in a database connection and an sql query and runs the query. Returns
 *  the stid if successful, 0 if not.
 *
 *  Author: Costa Zervos
 *  Notes:  Database connection code adapted from CMPUT 391 Lab 6 
 *          PHPexample3.html.
 */
    function sqlQuery($conn, $sql)
    {
        // Prepare sql using conn and returns the statement identifier
        $stid = oci_parse($conn, $sql);
        // Execute a statement returned from oci_parse()
        $res = oci_execute($stid);
        // Error checks SQL query
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
