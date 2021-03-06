<?php

require_once("includes/bootstrap.php");

if ($login_ok === false) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

global $db, $db_tb_incomings;

$action = getVar('action');
if ($action === 'getIncomings') {

    getIncomings((int)getVar('timestamp'));

} elseif ($action === 'setSaved') {

    if (getVar('state') === 'true') {
        $saved = 1;
    } else {
        $saved = 0;
    }
    $coords = $db->escape(getVar('coords'));

    $result = $db->db_update($db_tb_incomings, array('saved' => $saved, 'savedUpdateTime' => CURRENT_UNIX_TIME ), "WHERE koords_to = '$coords'");

    if ($result === true) {
        include('ajax/getIncomingsTables.php');
        echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'tables' => getIncomingsTables()));
    }

} elseif ($action === 'setRecalled') {

    if (getVar('state') === 'true') {
        $recalled = 1;
    } else {
        $recalled = 0;
    }
    $coords = $db->escape(getVar('coords'));

    $result = $db->db_update($db_tb_incomings, array('recalled' => $recalled, 'recalledUpdateTime' => CURRENT_UNIX_TIME), "WHERE koords_to = '$coords'");

    if ($result === true) {
        include('ajax/getIncomingsTables.php');
        echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'tables' => getIncomingsTables()));
    }

} elseif ($action === 'getOnlineUsers') {

    include('ajax/getOnlineUsers.php');
    echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'data' => getOnlineUsers()));

}

function getIncomings($timestamp)
{
    global $db, $db_tb_incomings;

    $sql = "SELECT COUNT(*) AS newEntries FROM `{$db_tb_incomings}` WHERE (`listedtime`>{$timestamp} OR `savedUpdateTime`>{$timestamp} OR `recalledUpdateTime`>{$timestamp}) ORDER BY `arrivaltime` ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    if (empty($row['newEntries'])) {

        //es sind keine aktuelleren Einträge da
        header('HTTP/1.1 304 Not Modified');

    } else {

        include_once('ajax/getIncomingsTables.php');
        echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'tables' => getIncomingsTables()));

    }

}