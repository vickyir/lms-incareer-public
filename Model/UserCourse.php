<?php
class UserCourses
{
    private $userCourseId;
    private $userId;
    private $courseId;
    private $dbConn;

    public function __construct()
    {
        require_once("DbConnect.php");
        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }

    public function setUserCourseId($id)
    {
        $this->userCourseId = $id;
    }
    public function getUserCourseId()
    {
        return $this->userCourseId;
    }
    public function setUserId($id)
    {
        $this->userId = $id;
    }
    public function getUserId()
    {
        return $this->userId;
    }
    public function setCourseId($id)
    {
        $this->courseId = $id;
    }
    public function getCourseId()
    {
        return $this->courseId;
    }
}
