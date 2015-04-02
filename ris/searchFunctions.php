<?php

/*
 * Contains functions to facilitate the search module.
 * 
 * Author: Costa
 * 
 * Rank Query SELECT statements adapted from:
 * http://stackoverflow.com/questions/8352796/ora-29908-missing-primary-invocation-for-ancillary-operator
 * Author: Paul Kar.
 * Date: 3/22/15
 */

function verifyInput($keywords, $ordering, $start_date, $end_date, $pattern) {
    if ($keywords == '' and $ordering == 'rank') {
        echo 'Cannot search by relevance without keywords.<br/>';
        echo '<a href="searchModule.php">Back</a>';
        return false;
    } else if ($keywords == '' and $start_date == '' and $end_date == '') {
        echo 'Please enter search keywords and/or time period to search.<br/>';
        echo '<a href="searchModule.php">Back</a>';
    } else if (($start_date == '') and ( $end_date != '')) {
        echo 'Please enter a start date.<br/>';
        echo '<a href="searchModule.php">Back</a>';
        return false;
    } else if (($start_date != '') and ( $end_date == '')) {
        echo 'Please enter an end date.<br/>';
        echo '<a href="searchModule.php">Back</a>';
        return false;
    } else if ($start_date != '' and ! preg_match($pattern, $start_date)) {
        echo 'Please enter a valid start date.<br/>';
        echo '<a href="searchModule.php">Back</a>';
        return false;
    } else if ($end_date != '' and ! preg_match($pattern, $end_date)) {
        echo 'Please enter a valid end date.<br/>';
        echo '<a href="searchModule.php">Back</a>';
        return false;
    } else {
        return true;
    }
}

function generateSearchQuery($userType, $person_id, $keywords, $ordering, $time_ref, $start_date, $end_date) {

    // Keywords used
    if ($keywords != '') {
        $sql = "SELECT rr.record_id, rs.patient_name, "
                . "pd.first_name || ' ' || pd.last_name AS doc_name, "
                . "pr.first_name || ' ' || pr.last_name AS rad_name, "
                . "rr.test_type, rr.prescribing_date, rr.test_date, rr.diagnosis, rr.description, rs.rank "
                . "FROM radiology_record rr, persons pd, persons pr, "
                . "(SELECT record_id, 6*SCORE(1) + 3*SCORE(2) + SCORE(3) AS rank, patient_name "
                . "FROM radiology_search "
                . "WHERE  CONTAINS(patient_name, '" . $keywords . "', 1)>0 "
                . "OR CONTAINS(diagnosis, '" . $keywords . "', 2)>0 "
                . "OR CONTAINS(description, '" . $keywords . "', 3)>0 "
                . ") rs "
                . "WHERE rr.record_id = rs.record_id "
                . "AND rr.doctor_id = pd.person_id "
                . "AND rr.radiologist_id = pr.person_id ";
        // Keywords and time period
        if ($start_date != '' and $time_ref == 't_date') {
            $sql .= "AND rr.test_date >= TO_DATE('" . $start_date . "','yyyy-mm-dd') "
                    . "AND rr.test_date <= TO_DATE('" . $end_date . "','yyyy-mm-dd') ";
        } else if ($start_date != '' and $time_ref == 'p_date') {
            $sql .= "AND rr.prescribing_date >= TO_DATE('" . $start_date . "','yyyy-mm-dd') "
                    . "AND rr.prescribing_date <= TO_DATE('" . $end_date . "','yyyy-mm-dd') ";
        }
    } 
    // Time period only
    else {
        $sql = "SELECT rr.record_id, "
                . "pp.first_name || ' ' || pp.last_name AS patient_name, "
                . "pd.first_name || ' ' || pd.last_name AS doc_name, "
                . "pr.first_name || ' ' || pr.last_name AS rad_name, "
                . "rr.test_type, rr.prescribing_date, rr.test_date, rr.diagnosis, rr.description "
                . "FROM   radiology_record rr, persons pd, persons pr, persons pp "
                . "WHERE rr.doctor_id = pd.person_id "
                . "AND rr.radiologist_id = pr.person_id "
                . "AND rr.patient_id = pp.person_id ";
        if ($start_date != '' and $time_ref == 't_date') {
            $sql .= "AND rr.test_date >= TO_DATE('" . $start_date . "','yyyy-mm-dd') "
                    . "AND rr.test_date <= TO_DATE('" . $end_date . "','yyyy-mm-dd') ";
        } else if ($start_date != '' and $time_ref == 'p_date') {
            $sql .= "AND rr.prescribing_date >= TO_DATE('" . $start_date . "','yyyy-mm-dd') "
                    . "AND rr.prescribing_date <= TO_DATE('" . $end_date . "','yyyy-mm-dd') ";
        }
    }
    
    // Security check
    if ($userType == 'p') {
        $sql .= "AND rr.patient_id = ".$person_id;
    } else if ($userType == 'r') {
        $sql .= "AND rr.radiologist_id = ".$person_id;
    } else if ($userType == 'd') {
        $sql .= "AND rr.patient_id IN (SELECT patient_id "
                . "FROM family_doctor "
                . "WHERE doctor_id = ".$person_id.")";
    }
    
    // Ordering
    if ($ordering == 'rank') {
        $sql .= " ORDER BY rs.rank";
    } else if ($ordering == 'time_new' and $time_ref == 't_date') {
        $sql .= " ORDER BY rr.test_date DESC";
    } else if ($ordering == 'time_new' and $time_ref == 'p_date') {
        $sql .= " ORDER BY rr.prescribing_date DESC";
    } else if ($ordering == 'time_old' and $time_ref == 't_date') {
        $sql .= " ORDER BY rr.test_date";
    } else if ($ordering == 'time_old' and $time_ref == 'p_date') {
        $sql .= " ORDER BY rr.prescribing_date";
    }
    //echo $sql;
    return $sql;
}

?>