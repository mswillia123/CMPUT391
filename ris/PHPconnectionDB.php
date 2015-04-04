<?php
/*
 *	Function:   connect()
 *	Purpose:    Connects to the Oracle database using the hardcoded credentials.
 *	Author:     CMPUT 391 Lab 6 PHPconnectionDB.php
 */
function connect(){
    // Enter your OracleDB credentials
	$conn = oci_connect('mswillia', 'Photi42211');
    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }
    
    return $conn;
}
?>
