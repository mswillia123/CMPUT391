<?php
/*
 *	Function: 	connect()
 *	Purpose:	Connects to the Oracle database using the hardcoded credentials.
 *	Author:		CMPUT 391 Lab 6 PHPconnectionDB.php
 */
function connect(){
	$conn = oci_connect('czervos', 'qwerty123');
	if (!$conn) {
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	return $conn;
}
?>
