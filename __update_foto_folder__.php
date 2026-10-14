<?php
//echo rawurlencode('FORD TRANSIT 2.2.TDCI EURO4_01_15256946550.jpg');
//$foo=file_get_contents('https://autolandromania.ro/foto/'. rawurlencode('FORD TRANSIT 2.2.TDCI EURO4_01_15256946550.jpg'));
//if(!@file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fotonew/' . 'FORD TRANSIT 2.2.TDCI EURO4_01_15256946550.jpg', $foo)){
//                exit('Nu am putut salva imaginea '. 'FORD TRANSIT 2.2.TDCI EURO4_01_15256946550.jpg');
//            }
//            exit;

/**
 * Acest scrip ESTE necesar
 * 
 * Actualizeaza dosarul foto pe baza anunturilor care sunt active
 * 
 * si le pune intr-un dosar fotonew.
 */
//if(!is_dir($_SERVER['DOCUMENT_ROOT'] . '/fotonew')){
//    //mkdir($_SERVER['DOCUMENT_ROOT'] . '/fotonew');
//    echo $_SERVER['DOCUMENT_ROOT'] . '/fotonew';
//} else {
//    exit('Dosarul exista deja');
//}

$processed = [
    'done' => FALSE,
    'limit' => 10,
    'still_processing' => FALSE,
    'current_page' => 1
];
//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fotonew'.'/aaaa.txt', json_encode($processed));


if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/fotonew' . '/aaaa.txt')) {
    echo 'NU POT PROCESA' . PHP_EOL;
    echo 'NU am fisierul de configurare';
    echo 'creeaza setarea initiala in fisierul aaaa.txt';
}

// get saved settings
$settings = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/fotonew' . '/aaaa.txt'), TRUE);

// iesim daca inca procesam lotul anterior
if ($settings['still_processing'] == TRUE) {
    exit();
}

// iesim daca am procesat intregul lot
if ($settings['done'] == TRUE) {
    exit();
}

require_once './includes/config.php';

$sqlMasini_count = $connection->query("SELECT COUNT(id) FROM masini") or die($connection->error);
$sqlMasini_count=mysqli_fetch_assoc($sqlMasini_count)['COUNT(id)'];

//var_dump(mysqli_fetch_assoc($sqlMasini_count)['COUNT(id)']);exit;
// calculate the number of required pages to display the results
$pages = intval($sqlMasini_count / $settings['limit']);
if (($settings['limit'] % $sqlMasini_count) !== 0) {
    // we have orphan results, so increment te number of pages
    $pages += 1;
}

$currentPage = $settings['current_page'];
//var_dump($pages);exit;
if ($currentPage > $pages) {
    // we had processed all results
    $settings['done']=TRUE;
    $settings['still_processing']=FALSE;
    
    updateSettings($settings);
    echo 'Am terminat';
}


$sqlMASINI = "SELECT * FROM masini";

$paginatedQuery = rtrim($sqlMASINI) . " LIMIT {$settings['limit']} OFFSET " . (($currentPage - 1) * $settings['limit']);
$paginatedQueryResults=$connection->query($paginatedQuery) or die($connection->error);

// update settings and write to disk to avoid processing the same batch
$settings['still_processing']=TRUE;
updateSettings($settings); // nu uita sa decomentezi linia ca sa funtioneze

while ($asoc= mysqli_fetch_assoc($paginatedQueryResults)){
    $queryPictures= sprintf("SELECT file FROM fotov WHERE oferta=%s", $asoc['id']);
    $queryPicturesResult=$connection->query($queryPictures) or die($connection->error);

    while ($pictureassoc = mysqli_fetch_assoc($queryPicturesResult)) {
        $picture_file_name=$pictureassoc['file'];
        
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/fotonew/' . $picture_file_name)){
            $picture_file= file_get_contents('https://autolandromania.ro/foto/'. rawurlencode($picture_file_name));
            if(!@file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fotonew/' . $picture_file_name, $picture_file)){
                exit('Nu am putut salva imaginea '. $picture_file_name);
            }
            //echo $picture_file;exit;
        }
        //echo $picture_file_name . " " . PHP_EOL;
    }
}
echo 'Am procesat pagina '. $settings['current_page'];
$settings['current_page']=$currentPage + 1;
$settings['still_processing']=FALSE;
updateSettings($settings);


/**
 * Write settings array to disk
 * @param array $settings
 */
function updateSettings($settings){
    
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fotonew'.'/aaaa.txt', json_encode($settings));
}