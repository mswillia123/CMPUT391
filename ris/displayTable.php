<?php

/*
 * Contains functions to display report generator table and search result table
 * along with helper functions.
 * 
 * Author: Costa
 * 
 * Code to display images adapted from:
 * http://stackoverflow.com/questions/13214602/how-to-display-an-blob-image-stored-in-mysql-database
 * Author: jerza
 * Date: 3/26/15
 */

// If not defined
if (!function_exists('sqlQuery')) {
    include("sqlQuery.php");
}

function reportTable($stid) {
    $resultsFlag = false;
    $column_num = 5;
    // While there are rows of results
    while ($row = oci_fetch_array($stid, OCI_NUM)) {
        // Display table header
        if (!$resultsFlag) {
            echo "<table border=2 style=\"width:80%\">
                    <tr>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>Testing Date (dd-mmm-yy)</th>
                    </tr>";
        }
        // Display data of that row
        echo "<tr>";
        for ($i = 0; $i < $column_num; $i++) {
            echo "<td>" . $row[$i] . "</td>";
        }
        echo "</tr>";
        $resultsFlag = true;
    }
    // No row results
    if (!$resultsFlag) {
        echo "No results found!<br/>";
    } else {
        echo "</table>";
    }
}

function searchTable($stid) {
    $resultsFlag = false;
    $column_num = 10;
    // If there rows of results
    while ($row = oci_fetch_array($stid, OCI_NUM)) {
        // Display table header
        if (!$resultsFlag) {
            echo "<table border=2 style=\"width:100%\">
                    <tr>
                        <th>Record ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Radiologist</th>
                        <th>Test Type</th>
                        <th>Prescribing Date</th>
                        <th>Testing Date</th>
                        <th>Diagnosis</th>
                        <th>Description</th>
                        <th>PACS Images</th>
                    </tr>";
        }
        // Display data of that row
        echo "<tr>";
        for ($i = 0; $i < $column_num; $i++) {
            echo "<td align=\"center\">";
            // Last column, display thumbnails
            if ($i == $column_num - 1) {
                displayThumbnails($row);
            } else {
                echo $row[$i];
            }
            echo "</td>";
        }
        echo "</tr>";
        $resultsFlag = true;
    }
    // No row results
    if (!$resultsFlag) {
        echo "No results found!<br/>";
    } else {
        echo "</table>";
    }
}

function displayThumbnails($row) {
    $conn2 = connect();
    // 0 image_id, 1 thumbnail, 2 regular_size, 3 full_size
    $sql2 = "SELECT image_id, thumbnail, regular_size, full_size FROM pacs_images WHERE record_id =" . $row[0];
    $stid2 = sqlQuery($conn2, $sql2);
    if ($stid2) {
        $image_found = false;
        while ($row2 = oci_fetch_array($stid2, OCI_NUM)) {
            // Gets blobs for each size and loads them
            $thumbnail = $row2[1]->load();
            $regular = $row2[2]->load();
            $full = $row2[3]->load();
            
            // Displays image in form and sends other sized loaded blobs when clicked
            echo '<span style="float:left">';
            echo '<form method="POST" action="imageZoom.php" target="_blank" >';
            echo '<input type="image" src="data:image/jpeg;base64,'.base64_encode( $thumbnail ).'" name="thumbnail" />';
            echo '<input type="hidden" name="regular" value="'.base64_encode( $regular ).'" />';
            echo '<input type="hidden" name="full" value="'.base64_encode( $full ).'" />';
            echo '</form>';
            echo '</span>';
            $image_found = true;
        }
        if (!$image_found) {
            echo "No Images";
        }
        oci_free_statement($stid2);
        oci_close($conn2);
    }
}

?>
