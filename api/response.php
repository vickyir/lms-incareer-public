<?php
function response($status, $msg, $data)
{
    $response = [
        'status' => $status,
        'msg' => $msg,
        'data' => $data
    ];
    header('Content-Type:application/json');
    echo json_encode($response);
}
