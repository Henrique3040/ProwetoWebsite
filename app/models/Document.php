<?php
class Document
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Upload document in database registreren
    public function addDocument($courseId, $naam, $bestandUrl, $type)
    {
        $id = generateUUID();
        $sql = "INSERT INTO CursusDocumenten (Id, cursus_id, Naam, BestandURL, Bestandstype) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $id, $courseId, $naam, $bestandUrl, $type);
        mysqli_stmt_execute($stmt);
        return $id;
    }

    public function getDocumentsByCourse($courseId)
    {
        $sql = "SELECT * FROM CursusDocumenten WHERE cursus_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    public function deleteDocumentsByCourse($courseId)
    {
        // bestanden fysiek verwijderen
        $docs = $this->getDocumentsByCourse($courseId);
        foreach ($docs as $doc) {
            if (file_exists($doc['BestandURL'])) {
                unlink($doc['BestandURL']);
            }
        }

        // uit database verwijderen
        $stmt = mysqli_prepare($this->conn, "DELETE FROM CursusDocumenten WHERE cursus_id = ?");
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        return mysqli_stmt_execute($stmt);
    }

}
