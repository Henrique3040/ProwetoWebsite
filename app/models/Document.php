<?php

/**
 * Class Document
 *
 * Dit model beheert documenten die gekoppeld zijn aan cursussen.
 * Het bevat functionaliteit om documenten toe te voegen, op te halen
 * en te verwijderen (zowel uit de database als fysiek van de server).
 */
class Document
{
    /** @var mysqli Databaseconnectie (MySQLi) */
    private $conn;

    /**
     * Constructor
     *
     * @param mysqli $db Actieve databaseconnectie
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Registreert een geüpload document in de database.
     *
     * @param string|int $courseId    ID van de cursus waaraan het document gekoppeld is
     * @param string     $naam        De naam/titel van het document
     * @param string     $bestandUrl  De serverlocatie van het bestand
     * @param string     $type        Het bestandstype/extensie
     *
     * @return false|string False bij fout, anders het ID van het nieuw aangemaakte document
     *
     * @note Vereist dat generateUUID() beschikbaar is in de app
     */
    public function addDocument($courseId, $naam, $bestandUrl, $type)
    {

        if (empty($naam) || empty($bestandUrl) || empty($type)) {
            error_log("addDocument mislukte: naam='$naam', bestand='$bestandUrl', type='$type'");
            return false;
        }

        $id = generateUUID();
        $sql = "INSERT INTO CursusDocumenten 
        (Id, cursus_id, Naam, BestandURL, Bestandstype, UploadedAt) 
        VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $id, $courseId, $naam, $bestandUrl, $type);        
        mysqli_stmt_execute($stmt);
        return $id;
    }

    /**
     * Haalt alle documenten op die horen bij een bepaalde cursus.
     *
     * @param string|int $courseId ID van de cursus
     * @return array Associatieve array met documentrecords
     */
    public function getDocumentsByCourse($courseId)
    {
        $sql = "SELECT * FROM CursusDocumenten WHERE cursus_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    /**
     * Verwijdert alle documenten van een cursus.
     * Dit omvat:
     *  - fysiek verwijderen van bestanden op de server
     *  - verwijderen van de database-records
     *
     * @param string|int $courseId ID van de cursus
     * @return bool True indien de database-verwijdering succesvol is
     */
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
