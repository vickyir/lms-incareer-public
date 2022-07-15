<?php
class AssignmentQuestion
{
    private $assignmentQuestionId;
    private $questionFileName;
    private $questionUploadDate;
    private $assignmentId;
    private $dbConn;
    public function __construct()
    {
        require_once("DbConnect.php");
        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }
    public function setAssignmentQuestionId($id)
    {
        $this->assignmentQuestionId = $id;
    }
    public function getAssignmentQuestionId()
    {
        return $this->assignmentQuestionId;
    }
    public function setQuestionFileName($Name)
    {
        $this->questionFileName = $Name;
    }
    public function getQuestionFileName()
    {
        return $this->questionFileName;
    }
    public function setQuestionUploadDate($date)
    {
        $this->questionUploadDate = $date;
    }
    public function getQuestionUploadDate()
    {
        return $this->questionUploadDate;
    }
    public function setAssignmentId($id)
    {
        $this->assignmentId = $id;
    }
    public function getAssignmentId()
    {
        return $this->assignmentId;
    }

    public function uploadFile($id)
    {
        $stmt = $this->dbConn->prepare("INSERT INTO assignment_questions VALUES (null, :filename, :upload_date, :assignment_id)");
        $stmt->bindParam(":filename", $this->questionFileName);
        $stmt->bindParam(":upload_date", $this->questionUploadDate);
        $stmt->bindParam(":assignment_id", $id);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllQuestions()
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM assignment_questions");

        try {
            if ($stmt->execute()) {
                $allQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $allQuestions;
    }

    public function getQuestionsByAssignmentId()
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM assignment_questions WHERE assignment_id = :aid");

        $stmt->bindParam(":aid", $this->assignmentId);

        try {
            if ($stmt->execute()) {
                $allQuestions = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $allQuestions;
    }
}
