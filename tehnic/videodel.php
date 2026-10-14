<?php

require_once('../includes/config.php');
$video2del = (isset($_GET['videoId'])) ? $_GET['videoId'] : 0;

if (!isset($_SESSION))
    session_start();

if (($video2del > 0) && (isset($_SESSION['MM_Username']))) {
    // Obține informațiile despre video
    $query_video = "SELECT * FROM fotov_video WHERE id=" . intval($video2del);
    $res = $connection->query($query_video);
    
    if (!$res || $res->num_rows == 0) {
        echo 'Video-ul nu a fost găsit.';
        die();
    }
    
    $video = mysqli_fetch_assoc($res);
    
    // Calculează root-ul proiectului (un nivel mai sus decât tehnic/)
    $projectRoot = dirname(__DIR__);
    
    // Verificare suplimentară pentru Linux: dacă dirname(__DIR__) nu funcționează corect
    // Încearcă să detecteze din DOCUMENT_ROOT
    if (!file_exists($projectRoot . "/foto/") && isset($_SERVER['DOCUMENT_ROOT'])) {
        // Dacă nu găsește folder-ul foto în projectRoot, încercă din DOCUMENT_ROOT
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        // Pentru /home/username/public_html -> /home/username/public_html
        if (file_exists($docRoot . "/foto/")) {
            $projectRoot = $docRoot;
        } elseif (file_exists(dirname($docRoot) . "/foto/")) {
            // Încearcă un nivel mai sus
            $projectRoot = dirname($docRoot);
        }
    }
    
    // Normalizează calea (elimină slash-uri duble și trailing slash)
    $projectRoot = rtrim(str_replace('\\', '/', $projectRoot), '/');
    
    // Șterge fișierul video de pe server
    $videoPath = $projectRoot . '/video/' . $video['file'];
    if (file_exists($videoPath)) {
        if (!@unlink($videoPath)) {
            echo 'Eroare la ștergerea fișierului video.';
            die();
        }
    }
    
    // Șterge thumbnail-ul dacă există
    if (!empty($video['thumb'])) {
        $thumbPath = $projectRoot . '/' . $video['thumb'];
        if (file_exists($thumbPath)) {
            @unlink($thumbPath);
        }
    }
    
    // Șterge din baza de date
    $query_delete = "DELETE FROM fotov_video WHERE id=" . intval($video2del);
    $res = $connection->query($query_delete);
    
    if (!$res) {
        echo 'Eroare la ștergerea din baza de date.';
        die();
    }
    
    echo 'Video-ul a fost șters.';
} else {
    echo 'Eroare: Nu aveți permisiunea de a șterge acest video.';
    die();
}
?>

