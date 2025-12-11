<?php

require_once "../../app/core/init.php";

$userId = $_POST["user_id"];

$result = $notificatieController->deleteAllNotification($userId);

if($result){
  // Redirect terug naar de pagina waar de gebruiker vandaan kwam
  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit;
}


