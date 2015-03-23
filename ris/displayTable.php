<?php

/*
 * Contains functions to display report generator table and search result table.
 * 
 * Author: Costa
 */

function reportTable($stid) {
    $resultsFlag = 0;
    $column_num = 5;
    while ($row = oci_fetch_array($stid, OCI_NUM)) {
        // Display table of results
        if ($resultsFlag == 0) {
            echo "<table border=2 style=\"width:80%\">
                    <tr>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>Testing Date (dd-mmm-yy)</th>
                    </tr>
                    <tr>";
            for ($i = 0; $i < $column_num; $i++) {
                echo "<td>" . $row[$i] . "</td>";
            }
            echo "</tr>";
            $resultsFlag = 1;
        } else {
            echo "<tr>";
            for ($i = 0; $i < $column_num; $i++) {
                echo "<td>" . $row[$i] . "</td>";
            }
            echo "</tr>";
        }
    }
    if ($resultsFlag == 0) {
        echo "No results found!<br/>";
    } else {
        echo "</table>";
    }
}

function searchTable($stid) {
    $resultsFlag = 0;
    $column_num = 11;
    while ($row = oci_fetch_array($stid, OCI_NUM)) {
        // Display table of results
        if ($resultsFlag == 0) {
            echo "<table border=2 style=\"width:80%\">
                        <tr>
                            <th>Search Rank</th>
                            <th>Record ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Radiologist</th>
                            <th>Test Type</th>
                            <th>Prescribing Date (dd-mmm-yy)</th>
                            <th>Testing Date (dd-mmm-yy)</th>
                            <th>Diagnosis</th>
                            <th>Description</th>
                            <th>PACS Image</th>
                        </tr>
                        <tr>";
            for ($i = 0; $i < $column_num; $i++) {
                echo "<td align=\"center\">";
                if ($i == $column_num - 1) {
                    echo "<form name =\"pacs_image\" method =\"get\" action =\"searchImage.php\">";
                    echo "<input type=\"hidden\" name=\"record_id\" value=\"" . $i . "\">";
                    echo "<input type=\"submit\" name=\"validate\" value=\"Image Data\">";
                    echo "</form>";
                } else {
                    echo $row[$i];
                }
                echo "</td>";
            }
            echo "</tr>";
            $resultsFlag = 1;
        } else {
            echo "<tr>";
            for ($i = 0; $i < $column_num; $i++) {
                echo "<td align=\"center\">";
                if ($i == $column_num - 1) {
                    echo "<form name =\"pacs_image\" method =\"get\" action =\"searchImage.php\">";
                    echo "<input type=\"hidden\" name=\"record_id\" value=\"" . $i . "\">";
                    echo "<input type=\"submit\" name=\"validate\" value=\"Image Data\">";
                    echo "</form>";
                } else {
                    echo $row[$i];
                }
                echo "</td>";
            }
            echo "</tr>";
        }
    }
    if ($resultsFlag == 0) {
        echo "No results found!<br/>";
    } else {
        echo "</table>";
    }
}
?>
