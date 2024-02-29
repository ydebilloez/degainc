<?php

require_once dirname(__FILE__) . "/../lib/sessioncontroller.php";
require_once dirname(__FILE__) . "/../lib/sessioncontroller.class.php";

$request = $_SERVER["REQUEST_METHOD"];

// reading data
$data = null;

if ($request == 'GET') {
    $data = new stdClass();
    foreach($_GET as $key => $value) { $data->{$key} = $value; }
}

if ($_SERVER["CONTENT_TYPE"] == 'application/json') {   
    if (in_array($request, array("POST", "PUT"))) {
        $data = json_decode(file_get_contents('php://input'), false);  
    }
} else {
    if (in_array($request, array("POST", "PUT"))) {
        // DEBUG
        // var_dump($_SERVER);
        var_dump($_REQUEST);
        // END DEBUG
        $data = new stdClass();
        foreach($_REQUEST as $key => $value) { $data->{$key} = $value; }
    }   
}

// processing data
// DEBUG
// $data->REQUEST_METHOD = $request;
// if (isset($_SERVER["CONTENT_TYPE"])) $data->CONTENT_TYPE = $_SERVER["CONTENT_TYPE"];
// END DEBUG

if (isset($data->pme_user) && isset($data->pme_password)) {
    $data->session_token = md5($data->pme_user + $data->pme_password);
    sessionCtl_storelogin($data->session_token);
} else if (isset($data->action) && $data->action == 'logout') {
    sessionCtl_clearlogin();
}

        /*
        // to log-in, we should call a post methed
        if (isset($_POST['pme_user']) && isset($_POST['pme_password'])) {
            $pme_user = $_POST['pme_user'];
            $pme_password = $_POST['pme_password'];
            $sessionController = new SessionController();
            $hash = $sessionController->login($pme_user, $pme_password);
            if ($hash) {
                sessionCtl_storelogin($hash);
                sessionCtl_returnerror(200, "Login successful!");
            } else {
                sessionCtl_returnerror(401, "Invalid username or password.");
            }
        } else {
            if (sessionCtl_isLoggedIn()) sessionCtl_logout();
        }
        */

unset($data->action);
unset($data->pme_user);
unset($data->pme_password);

// sending response
if ($_SERVER["CONTENT_TYPE"] == 'application/json') {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
} else {
    // DEBUG
    echo "\n\nData:\n" . json_encode($data) . "\n\n";
    echo "About to REDIRECT to : " . PME_PATH . "\n";
    // END DEBUG
    //header('Location: ' . PME_PATH);
}
