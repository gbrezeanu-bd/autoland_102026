<?PHP
//
//require_once '../includes/config.php';
//
//$query_curs_valutar_curent = "SELECT * FROM curs_valutar ORDER BY date DESC LIMIT 1";
//$curs_valutar_curent = $connection->query($query_curs_valutar_curent) or die($connection->error);
//$row_curs_valutar_curent = mysqli_fetch_assoc($curs_valutar_curent);
//
//// Fetch the exchange fomr BNR
//$xml = file_get_contents("https://www.bnr.ro/nbrfxrates.xml"); // your file is in the string "$xml" now.
//$xmlprint = simplexml_load_string($xml) or die("Error: Cannot create object");
//
//$eur = $xmlprint->Body->Cube->Rate[10];
//$usd = $xmlprint->Body->Cube->Rate[27];
//$huf = $xmlprint->Body->Cube->Rate[13];
//$czk = $xmlprint->Body->Cube->Rate[7];
//$pln = $xmlprint->Body->Cube->Rate[21];
//$data = $xmlprint->Header->PublishingDate;
//
//if (!(strcmp(date('Y-m-d'), $row_curs_valutar_curent['date']) == 0)) {
//
//    $query_curs_valutar = "insert into curs_valutar (`USD`, `EUR`, `HUF`, `CZK`, `PLN`, `date`) values ('$usd', '$eur', '$huf', '$czk', '$pln', '$data')";
//    $connection->query($query_curs_valutar) or die($connection->eror);
//}
//

// $hostname_autoland = "localhost";
// $database_autoland = "autoland_autos1ww1a";
// $username_autoland = "autoland_autos1";
// $password_autoland = "p4zj2J48bG";
// $connection = @new mysqli($hostname_autoland, $username_autoland, $password_autoland, $database_autoland);

// $query_curs_valutar_curent	= "SELECT * FROM curs_valutar ORDER BY date DESC LIMIT 1";
// $curs_valutar_curent		= $connection->query($query_curs_valutar_curent) or die($connection->error);
// $row_curs_valutar_curent	= mysqli_fetch_assoc($curs_valutar_curent);

// if (!(strcmp(date('Y-m-d'),$row_curs_valutar_curent['date']) == 0))
// {

// $xml = file_get_contents("https://www.bnr.ro/nbrfxrates.xml"); // your file is in the string "$xml" now.
// $xmlprint=simplexml_load_string($xml) or die("Error: Cannot create object");

// $eur = $xmlprint->Body->Cube->Rate[10];
// $usd = $xmlprint->Body->Cube->Rate[27];
// $huf = $xmlprint->Body->Cube->Rate[12];
// $czk = $xmlprint->Body->Cube->Rate[7];
// $pln = $xmlprint->Body->Cube->Rate[20];
// $data = $xmlprint->Header->PublishingDate;

// $query_curs_valutar = "insert into curs_valutar (`USD`, `EUR`, `HUF`, `CZK`, `PLN`, `date`) values ('$usd', '$eur', '$huf', '$czk', '$pln', '$data')";
// $connection->query($query_curs_valutar) or die($connection->error);
// }

// gabi - 7 ian 2026

// Folosește config.php pentru conexiunea la baza de date
require_once '../includes/config.php';

// Verifică că avem conexiune validă
if (!isset($connection) || $connection->connect_error) {
    die("Eroare conexiune baza de date: " . ($connection->connect_error ?? 'Connection not initialized'));
}

$query_curs_valutar_curent	= "SELECT * FROM curs_valutar ORDER BY date DESC LIMIT 1";
$curs_valutar_curent		= $connection->query($query_curs_valutar_curent) or die($connection->error);
$row_curs_valutar_curent	= mysqli_fetch_assoc($curs_valutar_curent);

if (!(strcmp(date('Y-m-d'),$row_curs_valutar_curent['date']) == 0))
{
    // Preia XML-ul de la BNR
    $xml = file_get_contents("https://www.bnr.ro/nbrfxrates.xml");
    if ($xml === false) {
        die("Eroare: Nu se poate accesa XML-ul de la BNR");
    }
    
    $xmlprint = simplexml_load_string($xml);
    if ($xmlprint === false) {
        die("Eroare: Nu se poate parsa XML-ul de la BNR");
    }
    
    // Folosește atributul 'currency' pentru a găsi cursurile (mai sigur decât indexare)
    $eur = null;
    $usd = null;
    $huf = null;
    $czk = null;
    $pln = null;
    
    // Parcurge toate ratele și caută după atributul currency
    foreach ($xmlprint->Body->Cube->Rate as $rate) {
        $currency = (string)$rate['currency'];
        $value = (string)$rate;
        
        switch($currency) {
            case 'EUR':
                $eur = $value;
                break;
            case 'USD':
                $usd = $value;
                break;
            case 'HUF':
                $huf = $value;
                break;
            case 'CZK':
                $czk = $value;
                break;
            case 'PLN':
                $pln = $value;
                break;
        }
    }
    
    $data = (string)$xmlprint->Header->PublishingDate;
    
    // Verifică că am găsit toate cursurile necesare
    if ($eur && $usd && $huf && $czk && $pln && $data) {
        // Folosește prepared statements pentru securitate
        $stmt = $connection->prepare("INSERT INTO curs_valutar (USD, EUR, HUF, CZK, PLN, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $usd, $eur, $huf, $czk, $pln, $data);
        
        if ($stmt->execute()) {
            echo "✓ Curs valutar actualizat cu succes pentru data: " . $data . "<br>";
            echo "EUR: " . $eur . " | USD: " . $usd . " | HUF: " . $huf . " | CZK: " . $czk . " | PLN: " . $pln;
        } else {
            die("Eroare la inserare: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo "Eroare: Nu s-au putut prelua toate cursurile din XML<br>";
        echo "EUR: " . ($eur ?: 'MISSING') . "<br>";
        echo "USD: " . ($usd ?: 'MISSING') . "<br>";
        echo "HUF: " . ($huf ?: 'MISSING') . "<br>";
        echo "CZK: " . ($czk ?: 'MISSING') . "<br>";
        echo "PLN: " . ($pln ?: 'MISSING') . "<br>";
    }
} else {
    echo "ℹ Cursul pentru data " . $row_curs_valutar_curent['date'] . " este deja în baza de date.";
}

?>
