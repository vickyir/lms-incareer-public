<?php
header('Content-Type:application/json');
require_once('../Model/Assignments.php');
$allasignment = new Assignments;
$as = $allasignment->getAllAssigment();
$method = $_SERVER['REQUEST_METHOD'];
$result = array();

if ($method == 'GET') {
    $result['status'] = [
        'code' => 200,
        'msg' => 'success'
    ];
    $result['data'] = $as;
} elseif (count($as) == 1) {
    $result['status'] = [
        'code' => 400,
        'msg' => 'Failed'
    ];
} elseif ($method != 'GET') {
    $result['status'] = [
        'code' => 400,
        'msg' => 'Method wrong'
    ];
}
echo json_encode($result);
