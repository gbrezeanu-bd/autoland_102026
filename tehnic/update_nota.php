<?php
/**
 * Script AJAX pentru actualizarea ordinii (nota) pentru imagini și video-uri
 */

require_once('../includes/config.php');

if (!isset($_SESSION)) {
    session_start();
}

// Verifică autorizarea
if (!isset($_SESSION['MM_Username'])) {
    echo 'Eroare: Nu aveți permisiunea de a modifica ordinea.';
    exit;
}

$itemId = isset($_GET['itemId']) ? intval($_GET['itemId']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : ''; // 'image' sau 'video'
$action = isset($_GET['action']) ? $_GET['action'] : ''; // 'plus' sau 'minus'

if ($itemId <= 0 || !in_array($type, ['image', 'video']) || !in_array($action, ['plus', 'minus'])) {
    echo 'Eroare: Parametri invalizi.';
    exit;
}

// Obține nota curentă
$table = ($type === 'video') ? 'fotov_video' : 'fotov';
$query = "SELECT nota FROM $table WHERE id = $itemId";
$result = $connection->query($query);

if (!$result || $result->num_rows == 0) {
    echo 'Eroare: Item-ul nu a fost găsit.';
    exit;
}

$row = $result->fetch_assoc();
$currentNota = intval($row['nota']);

// Calculează noua nota
$newNota = ($action === 'plus') ? $currentNota + 1 : $currentNota - 1;

// Actualizează nota
$updateQuery = "UPDATE $table SET nota = $newNota WHERE id = $itemId";
$updateResult = $connection->query($updateQuery);

if ($updateResult) {
    echo 'OK';
    exit;
} else {
    echo 'Eroare: Nu s-a putut actualiza nota.';
    exit;
}

