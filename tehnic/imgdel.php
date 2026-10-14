<?php

require_once('../includes/config.php');
$img2del = (isset($_GET['imgId'])) ? $_GET['imgId'] : 0;

if (!isset($_SESSION))
    session_start();

if (($img2del > 0) && (isset($_SESSION['MM_Username']))) {
    // delete picture from server
    $query_webpics = "SELECT * FROM fotov WHERE id=" . $img2del;
    $res = $connection->query($query_webpics);
    $filename= mysqli_fetch_assoc($res);
    //var_dump(realpath($_SERVER["DOCUMENT_ROOT"].'/foto/'.$filename['file']));exit;
    if (!@unlink(realpath($_SERVER["DOCUMENT_ROOT"].'/foto/'.$filename['file']))) {
        echo 'Eroare la stergere.';
        die();
    }
    // delete from database
    $query_webpics = "DELETE FROM fotov WHERE id=" . $img2del;
    $res = $connection->query($query_webpics);

    
    if (!$res) {
        echo 'Eroare la stergere.';
        die();
    }
    echo 'Imaginea a fost stearsa.';
} else {
    echo 'Eroare la stergere.';
    die();
}
?>