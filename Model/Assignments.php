<?php
class Assignments
{
    private $assignmentId;
    private $assignmentName;
    private $assignmentStartDate;
    private $assignmentEndDate;
    private $assignmentDesc;
    private $subjectId;
    private $assignmentType;
    private $mentorId;
    private $eventId;
    private $dbConn;

    public function __construct()
    {
        require_once("DbConnect.php");
        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }
    public function setAssignmentId($id)
    {
        $this->assignmentId = $id;
    }
    public function getAssignmentId()
    {
        return $this->assignmentId;
    }
    public function setAssignmentName($name)
    {
        $this->assignmentName = $name;
    }
    public function getAssignmentName()
    {
        return $this->assignmentName;
    }
    public function setAssignmentStartDate($date)
    {
        $this->assignmentStartDate = $date;
    }
    public function getAssignmentStartDate()
    {
        return $this->assignmentStartDate;
    }
    public function setAssignmentEndDate($date)
    {
        $this->assignmentEndDate = $date;
    }
    public function getAssignmentEndDate()
    {
        return $this->assignmentEndDate;
    }
    public function setAssignmentDesc($desc)
    {
        $this->assignmentDesc = $desc;
    }
    public function getAssignmentDesc()
    {
        return $this->assignmentDesc;
    }
    public function setSubjectId($id)
    {
        $this->subjectId = $id;
    }
    public function getSubjectId()
    {
        return $this->subjectId;
    }
    public function setAssignmentType($type)
    {
        $this->assignmentType = $type;
    }
    public function getAssignmentType()
    {
        return $this->assignmentType;
    }
    public function setMentorId($id)
    {
        $this->mentorId = $id;
    }
    public function getMentorId()
    {
        return $this->mentorId;
    }
    public function setEventId($id)
    {
        $this->eventId = $id;
    }
    public function getEventId()
    {
        return $this->eventId;
    }

    public function saveAssignment()
    {
        $stmt = $this->dbConn->prepare("INSERT INTO assignments VALUES(null, :name, :start_date, :end_date, :desc, :assign_type, :sid,:mid,:eid)");

        $stmt->bindParam(":name", $this->assignmentName);
        $stmt->bindParam(":start_date", $this->assignmentStartDate);
        $stmt->bindParam(":end_date", $this->assignmentEndDate);
        $stmt->bindParam(":desc", $this->assignmentDesc);
        $stmt->bindParam(":assign_type", $this->assignmentType);
        $stmt->bindParam(":sid", $this->subjectId);
        $stmt->bindParam(":mid", $this->mentorId);
        $stmt->bindParam(":eid", $this->eventId);

        $id = "";

        try {
            if ($stmt->execute()) {

                $id = $this->dbConn->lastInsertId();
                $is_ok = true;
                goto out;
            } else {
                $is_ok = false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        out: {
            return [
                "assignment_id" => $id,
                "is_ok" => $is_ok
            ];
        }
    }

    public function createAssignment($data, $file, $sid, $mid, $userData)
    {
        $is_ok = false;
        $msg = "";

        if (empty($data['title'])) {
            $msg = "Judul tidak boleh kosong";
            goto out;
        }

        if (!is_string($data['title'])) {
            $msg = "Judul tidak valid!";
            goto out;
        }

        if (!is_string($data['desc'])) {
            $msg = "Deskripsi tidak valid!";
            goto out;
        }

        if (empty($data['start-date'])) {
            $msg = "Tanggal mulai tidak boleh kosong!";
            goto out;
        }

        if (empty($data['end-date'])) {
            $msg = "Tanggal selesai tidak boleh kosong!";
            goto out;
        }

        if (empty($data['assign_type'])) {
            $msg = "Tipe assignment tidak boleh kosong!";
            goto out;
        }
        $tkn = uniqid();
        $fn = $tkn . '_' . $file['file']['name'];

        $this->setAssignmentName($data['title']);
        $this->setAssignmentStartDate($data['start-date']);
        $this->setAssignmentEndDate($data['end-date']);
        $this->setAssignmentDesc($data['desc']);
        $this->setAssignmentType($data['assign_type']);
        $this->setEventId($data['event_id']);
        $this->setSubjectId($sid);
        $this->setMentorId($mid);

        require_once("AssignmentQuestion.php");
        $objQuest = new AssignmentQuestion;

        $validTypeFile = [
            "image/png", // png
            "image/jpg", // jpg
            "image/jpeg", // jpeg
            "text/plain", // txt or html
            "application/pdf", // pdf
            "application/vnd.ms-powerpoint",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // docx
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // xlsx
            "application/vnd.openxmlformats-officedocument.presentationml.presentation", // pptx
            "application/vnd.ms-excel", // xls
            "application/msword", // doc
            "application/zip", // zip
            "application/x-rar",
            "application/x-gzip", // zip
            "application/x-zip-compressed", // rar
            "application/octet-stream", //zip
            "application/x-rar-compressed", //rar
        ];


        if (!in_array($file['file']['type'], $validTypeFile)) {
            $msg = "Format file tidak didukung!";
            goto out;
        }
        if ($file['file']['size'] > 2097152) {
            $msg = "Tidak boleh lebih dari 2 mb";
            goto out;
        }

        $objQuest->setQuestionFileName($fn);
        date_default_timezone_set('Asia/Jakarta');
        $objQuest->setQuestionUploadDate(date("Y-m-d H:i:s"));

        // $path = "../../Upload/Assignment/Questions/";
        $path = dirname(__DIR__) . '/Upload/Assignment/Questions/';


        move_uploaded_file($file['file']['tmp_name'], $path . $fn);

        $save = $this->saveAssignment();
        $upload = $objQuest->uploadFile($save['assignment_id']);

        for ($i = 0; $i < count($userData); $i++) {
            require_once "Scores.php";
            require_once "AssignmentSubmission.php";

            $objScore = new Scores;
            $objScore->setScoreValue(0);
            $objScore->setAssignmentId($save['assignment_id']);
            $objScore->setMentorId(0);
            $objScore->setStudentId($userData[$i]->{'user_id'});
            $objScore->insertScore();

            $objSubm = new AssignmentSubmission;
            $objSubm->setSubmissionFileName("N/A");
            $objSubm->setSubmissionUploadDate(date("Y-m-d H:i:s"));
            $objSubm->setSubmissionStatus(1);
            $objSubm->setSubmissionToken("");
            $objSubm->setIsFinished(0);
            $objSubm->setAssignmentId($save['assignment_id']);
            $objSubm->setStudentId($userData[$i]->{'user_id'});
            $objSubm->creatAssignmentSubmission();
        }

        if ($save['is_ok'] && $upload) {
            $msg = "Berhasil membuat tugas!";
            $is_ok = true;
            goto out;
        } else {
            $msg = "Gagal membuat tugas!";
            goto out;
        }

        out: {
            return [
                "is_ok" => $is_ok,
                "msg" => $msg,
            ];
        }
    }

    public function getAllAssigment()
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM assignments");

        try {
            if ($stmt->execute()) {
                $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $assignments;
    }

    public function updateAssignment()
    {
        $stmt = $this->dbConn->prepare(
            "UPDATE assignments SET assignment_name = :name, 
                                    assignment_start_date = :start_date,
                                    assignment_end_date = :end_date,
                                    assignment_desc = :desc,
                                    assignment_type = :assign_type
                                    WHERE assignment_id = :id"
        );

        $stmt->bindParam(":name", $this->assignmentName);
        $stmt->bindParam(":start_date", $this->assignmentStartDate);
        $stmt->bindParam(":end_date", $this->assignmentEndDate);
        $stmt->bindParam(":desc", $this->assignmentDesc);
        $stmt->bindParam(":id", $this->assignmentId);
        $stmt->bindParam(':assign_type', $this->assignmentType);

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

    public function editAssignment($data, $file)
    {
        $is_ok = false;
        $msg = "";

        if (!is_string($data['title'])) {
            $msg = "Judul tidak valid!";
            goto out;
        }

        if (!is_string($data['desc'])) {
            $msg = "Deskripsi tidak valid!";
            goto out;
        }

        if (empty($data['start-date'])) {
            $msg = "Tanggal mulai tidak boleh kosong!";
            goto out;
        }

        if (empty($data['end-date'])) {
            $msg = "Tanggal selesai tidak boleh kosong!";
            goto out;
        }

        $this->setAssignmentName($data['title']);
        $this->setAssignmentStartDate($data['start-date']);
        $this->setAssignmentEndDate($data['end-date']);
        $this->setAssignmentDesc($data['desc']);
        $this->setAssignmentId($data['id']);
        $this->setAssignmentType($data['assign_type']);

        $validTypeFile = [
            "image/png", // png
            "image/jpg", // jpg
            "image/jpeg", // jpeg
            "text/plain", // txt or html
            "application/pdf", // pdf
            "application/vnd.ms-powerpoint",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // docx
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // xlsx
            "application/vnd.openxmlformats-officedocument.presentationml.presentation", // pptx
            "application/vnd.ms-excel", // xls
            "application/msword", // doc
            "application/zip", // zip
            "application/x-rar" // rar
        ];


        if ($file['filename']['size'] > 0) {
            if (!in_array($file['filename']['type'], $validTypeFile)) {
                $msg = "Format file tidak didukung!";
                goto out;
            }

            require_once("AssignmentQuestion.php");
            $objQuest = new AssignmentQuestion;

            $objQuest->setQuestionFileName($file['filename']['name']);
            date_default_timezone_set('Asia/Jakarta');
            $objQuest->setQuestionUploadDate(date("Y-m-d H:i:s"));

            $path = dirname(__DIR__) . '/Upload/Assignment/Questions/';
            move_uploaded_file($file['filename']['tmp_name'], $path . $file['filename']['name']);

            $objQuest->uploadFile($data['id']);
        }


        $edit = $this->updateAssignment();

        if ($edit) {
            $msg = "Berhasil mengubah tugas!";
            $is_ok = true;
            goto out;
        } else {
            $msg = "Gagal mengubah tugas!";
            goto out;
        }

        out: {
            return [
                "is_ok" => $is_ok,
                "msg" => $msg,
            ];
        }
    }

    public function deleteAssignment()
    {
        $stmt = $this->dbConn->prepare("DELETE FROM assignments WHERE assignment_id = :id");
        $stmt->bindParam(":id", $this->assignmentId);

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

    public function getAssignmentById($id)
    {
        $stmt = $this->dbConn->prepare("SELECT * FROM assignments WHERE assignment_id = :id");
        $stmt->bindParam(":id", $id);

        try {
            if ($stmt->execute()) {
                $assigmentData = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $assigmentData;
    }

    public function getAssignmentBySubjectId($id)
    {
        $stmt = $this->dbConn->prepare(
            "SELECT * FROM assignments WHERE subject_id = :sid"
        );

        $stmt->bindParam(":sid", $id);

        try {
            if ($stmt->execute()) {
                $assigments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $assigments;
    }
    public function getAssignmentByAssignmentId($id)
    {
        $stmt = $this->dbConn->prepare(
            "SELECT * FROM assignments WHERE assignment_id = :sid"
        );

        $stmt->bindParam(":sid", $id);

        try {
            if ($stmt->execute()) {
                $assigments = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $assigments;
    }
    public function getAssignmentBySubjectIdForStudent($id)
    {
        $stmt = $this->dbConn->prepare(
            "SELECT * FROM assignments WHERE subject_id = :sid AND now() > assignment_start_date"
        );

        $stmt->bindParam(":sid", $id);

        try {
            if ($stmt->execute()) {
                $assigments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $assigments;
    }
}
