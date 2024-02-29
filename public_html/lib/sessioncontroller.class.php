<?php

require_once dirname(__FILE__) . "/sessioncontroller.php";
require_once dirname(__FILE__) . "/databasecontroller.class.php";

class SessionController
{
    public function login($pme_user, $pme_password)
    {
        if ($pme_user == 'admin') return $pme_password;
        return false;
    }

    // logout
    public function logout($pme_user, $pme_hash)
    {
        return false;
    }

    private function checkUser($pme_user, $pme_hash)
    {
        return true;
    }

    public function verifyToken($pme_hash)
    {
        return true;
    }
}