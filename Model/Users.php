<?php

class Users
{
    private $userId;
    private $userName;
    private $userPassword;
    private $userRole;
    private $dbConn;

    public function __construct()
    {
        require_once 'DbConnect.php';
        $db = new DbConnect();
        $this->dbConn = $db->connect();
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($id)
    {
        $this->userId = $id;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($name)
    {
        $this->userName = $name;
    }

    public function getUserPassword()
    {
        return $this->userPassword;
    }

    public function setUserPassword($pass)
    {
        $this->userPassword = $pass;
    }

    public function getUserRole()
    {
        return $this->userRole;
    }

    public function setUserRole($role)
    {
        $this->userRole = $role;
    }

    public function getUserByUsername()
    {
        $stmnt = $this->dbConn->prepare(
            "SELECT * FROM users WHERE username = BINARY :username"
        );
        $stmnt->bindParam(":username", $this->userName);

        try {
            if ($stmnt->execute()) {
                $userData = $stmnt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $userData;
    }

    public function loginUser($data)
    {
        $is_ok = false;
        $msg = '';

        if (!is_string($data['username'])) {
            $msg = 'Username tidak valid!';
            goto out;
        }

        if (!is_string($data['password'])) {
            $msg = 'Password tidak valid!';
            goto out;
        }

        $this->setUserName($data['username']);
        $this->setUserPassword($data['password']);

        $userData = $this->getUserByUsername();

        if (empty($userData)) {
            $msg = 'Data tidak ditemukan';
            goto out;
        }

        if ($data['password'] == $userData['password']) {
            $msg = 'Login berhasil!';
            $is_ok = true;
            goto out;
        }

        out:
        return [
            'is_ok' => $is_ok,
            'msg' => $msg,
            'data' => $userData,
        ];
    }
}
