<?php


add_action( 'wp_ajax_nopriv_expose', function() {
    $params = empty($_POST['params'])? [] : $_POST['params'];
    $data = call_user_func_array($_POST['method'], $params);
    $response['success'] = true;
    $response['message'] = 'success';
    $response['data'] = $data;

    header('Content-type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}, 1);



