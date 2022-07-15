<?php
class UserAssignment
{
    private $userAssignmentId;
    private $userId;
    private $assignmentId;

    public function __construct()
    {
        require_once("DbConnect.php");
        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }
    public function getUserAssignmentId()
    {
        return $this->userAssignmenId;
    }

    public function setUserAssignmenId($id)
    {
        $this->userAssignmenId = $id;
    }
    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($id)
    {
        $this->userId = $id;
    }
    public function setAssignmentId($id)
    {
        $this->assignmentId = $id;
    }
    public function getAssignmentId()
    {
        return $this->assignmentId;
    }
}
