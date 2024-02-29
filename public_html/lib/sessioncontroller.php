<?php

if (!defined('PME_LOGIN_KEY')) {
    DEFINE('PME_LOGIN_KEY', 'pme_login_key');
}
if (!defined('PME_LOGIN_TIME')) {
    DEFINE('PME_LOGIN_TIME' , 900); // 15 minutes
}
if (!defined('PME_PATH')) {
    DEFINE('PME_PATH' , '/'); // 15 minutes
}
if (!defined('PME_HOST')) {
    $host = $_SERVER['HTTP_HOST'];
    if ($pos = strpos($host, ':')) {
        $host = substr($host, 0, $pos);
    }
    DEFINE('PME_HOST', $host);
}

// check if logged in, and redirect if not
function sessionCtl_requireLogin() {
    if (!sessionCtl_isLoggedIn()) {
        // redirect to login page
        sessionCtl_returnerror(401, "Login required");
    }
}

// check if logged in
function sessionCtl_isLoggedIn() {
    return isset($_COOKIE[PME_LOGIN_KEY]);
}

function sessionCtl_storelogin($token) {
    if (PHP_VERSION_ID < 70300) {
        setcookie(PME_LOGIN_KEY,
            $token,
            time() + PME_LOGIN_TIME,
            PME_PATH . "; SameSite=Strict",
            PME_HOST);
    } else {
        setcookie(PME_LOGIN_KEY, $token, [
            'expires' => time() + PME_LOGIN_TIME,
            'path' => PME_PATH,
            'domain' => PME_HOST,
            'secure' => false,
            'SameSite' => 'Strict']);
    }
}

// cancel login related storage
function sessionCtl_clearlogin() {
    // login cookie
    if (PHP_VERSION_ID < 70300) {
        setcookie(PME_LOGIN_KEY,
            $token,
            time() + LOGIN_TIME,
            PME_PATH . "; SameSite=Strict",
            PME_HOST);
    } else {
        setcookie(PME_LOGIN_KEY, "", [
            'expires' => time() - 3600,
            'path' => PME_PATH,
            'SameSite' => 'Strict']);
    }
}

// force redirect to logout
function sessionCtl_logout() {
    sessionCtl_clearlogin();

    // redirect to login page
    header('Location: ' . PME_PATH . '/');
    exit;
}

function sessionCtl_returnerror($en, $msg) {
    header("Content-Type: text/plain; charset=UTF-8");
    echo json_encode(array("message" => $msg));
    //header('Location: ' . PME_PATH . '/');
    exit;
}
