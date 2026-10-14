<?php 
require_once('../includes/config.php');

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function getUniqueVideoFilename($conn, $filename, $extension) {
    // Adaugă timestamp scurt la final (format: ymdHi = 2312251430 = 10 caractere)
    // Mai scurt decât time() care are 10 cifre dar e mai clar
    $timestamp = date('ymdHi'); // Format: 2312251430 (an 2 cifre, luna, ziua, ora, minut)
    $newFilename = $filename . "_" . $timestamp . "." . $extension;

    // Verifică dacă există deja, dacă da, adaugă secunde pentru unicitate
    $counter = 0;
    while (true) {
        // SQL query to check if the file exists
        $sql = "SELECT * FROM fotov_video WHERE file = '$newFilename'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            // File does not exist, return the unique filename
            return $newFilename;
        } else {
            // File exists, adaugă secunde pentru unicitate
            $counter++;
            $timestampWithSeconds = date('ymdHis'); // Adaugă secunde dacă există conflict
            $newFilename = $filename . "_" . $timestampWithSeconds . "." . $extension;
        }
    }
}

/**
 * Procesează thumbnail încărcat manual pentru video
 * @param int $index Index-ul thumbnail-ului în array-ul $_FILES
 * @param string $thumbPath Calea unde să salveze thumbnail-ul
 * @return bool True dacă reușește, false altfel
 */
function processUploadedThumbnail($index, $thumbPath) {
    // Verifică dacă există thumbnail-ul pentru acest index
    if (!isset($_FILES['thumbnails']['name'][$index]) || 
        $_FILES['thumbnails']['name'][$index] == "" ||
        $_FILES['thumbnails']['error'][$index] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $tmpName = $_FILES['thumbnails']['tmp_name'][$index];
    $originalName = $_FILES['thumbnails']['name'][$index];
    
    // Verifică tipul fișierului (doar imagini)
    $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedImageTypes)) {
        error_log("Invalid thumbnail image type: " . $mimeType . " for file: " . $originalName);
        return false;
    }
    
    // Mută fișierul în locația dorită
    if (move_uploaded_file($tmpName, $thumbPath)) {
        error_log("✓ Thumbnail uploaded successfully: " . $thumbPath . " (" . filesize($thumbPath) . " bytes)");
        return true;
    } else {
        error_log("✗ Failed to move uploaded thumbnail: " . $originalName);
        return false;
    }
}

/**
 * Creează un thumbnail placeholder pentru video
 * @param string $thumbPath Calea unde să salveze thumbnail-ul
 * @return bool True dacă reușește
 */
function createVideoPlaceholder($thumbPath) {
    // Creează o imagine placeholder simplă cu iconiță video
    $width = 320;
    $height = 240;
    
    $image = imagecreatetruecolor($width, $height);
    
    // Fundal gri închis
    $bgColor = imagecolorallocate($image, 40, 40, 40);
    imagefill($image, 0, 0, $bgColor);
    
    // Text/iconiță play
    $textColor = imagecolorallocate($image, 255, 255, 255);
    
    // Desenează un cerc pentru butonul play
    $centerX = $width / 2;
    $centerY = $height / 2;
    $circleRadius = 40;
    $circleColor = imagecolorallocate($image, 200, 200, 200);
    imagefilledellipse($image, $centerX, $centerY, $circleRadius * 2, $circleRadius * 2, $circleColor);
    
    // Desenează triunghiul play (simplificat)
    $triangleSize = 20;
    $points = [
        $centerX - $triangleSize, $centerY - $triangleSize,
        $centerX - $triangleSize, $centerY + $triangleSize,
        $centerX + $triangleSize, $centerY
    ];
    imagefilledpolygon($image, $points, 3, $textColor);
    
    // Salvează imaginea
    return imagejpeg($image, $thumbPath, 85);
}

// Formate video acceptate
$allowedExtensions = ['mp4', 'webm', 'mov', 'avi', 'MP4', 'WEBM', 'MOV', 'AVI'];
$maxFileSize = 500 * 1024 * 1024; // 500MB (dar nu verificăm strict)

// Creează directoarele dacă nu există (folosim calea relativă la proiect, ca la foto)
// Calculăm root-ul proiectului (un nivel mai sus decât tehnic/)
$projectRoot = dirname(__DIR__);

// Verificare suplimentară pentru Linux: dacă dirname(__DIR__) nu funcționează corect
// Încearcă să detecteze din DOCUMENT_ROOT
if (!file_exists($projectRoot . "/foto/") && isset($_SERVER['DOCUMENT_ROOT'])) {
    // Dacă nu găsește folder-ul foto în projectRoot, încercă din DOCUMENT_ROOT
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    // Pentru /home/username/public_html -> /home/username/public_html
    // Sau pentru /var/www/html -> /var/www/html
    if (file_exists($docRoot . "/foto/")) {
        $projectRoot = $docRoot;
    } elseif (file_exists(dirname($docRoot) . "/foto/")) {
        // Încearcă un nivel mai sus
        $projectRoot = dirname($docRoot);
    }
}

// Normalizează calea (elimină slash-uri duble și trailing slash)
$projectRoot = rtrim(str_replace('\\', '/', $projectRoot), '/');

$videoDir = $projectRoot . "/video/";
$thumbDir = $projectRoot . "/video/thumbs/";

// Creează directoarele cu permisiuni corecte pentru Linux
if (!file_exists($videoDir)) {
    $created = @mkdir($videoDir, 0755, true);
    if (!$created) {
        error_log("Failed to create video directory: " . $videoDir);
        // Încearcă cu permisiuni mai permisive
        @mkdir($videoDir, 0777, true);
    }
}

if (!file_exists($thumbDir)) {
    $created = @mkdir($thumbDir, 0755, true);
    if (!$created) {
        error_log("Failed to create thumbnails directory: " . $thumbDir);
        // Încearcă cu permisiuni mai permisive
        @mkdir($thumbDir, 0777, true);
    }
}

// Verificare finală - dacă directoarele nu există, loghează eroarea
if (!file_exists($videoDir) || !is_writable($videoDir)) {
    error_log("Video directory does not exist or is not writable: " . $videoDir);
    error_log("Project root: " . $projectRoot);
    error_log("DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A'));
    error_log("__DIR__: " . __DIR__);
    error_log("dirname(__DIR__): " . dirname(__DIR__));
}

if (!file_exists($thumbDir) || !is_writable($thumbDir)) {
    error_log("Thumbnails directory does not exist or is not writable: " . $thumbDir);
}

for($i = 0; $i < 4; $i++) {
    $target = $projectRoot . "/video/"; 
    $nume_fisier = $_FILES['uploaded']['name'][$i];
    
    if ($nume_fisier != "") {
        // Extrage extensia pentru a o păstra
        $fileExtension = strtolower(pathinfo($nume_fisier, PATHINFO_EXTENSION));
        
        // Verifică formatul
        if (!in_array($fileExtension, $allowedExtensions)) {
            continue;
        }
        
        // Generează nume unic (exact ca în upload2.php)
        $stop = strlen($nume_fisier);
        $start = $stop - strlen($fileExtension) - 1;
        $nume_fisier = substr($_FILES['uploaded']['name'][$i], 0, $start);
        $nume_fisier = getUniqueVideoFilename($connection, basename($nume_fisier), $fileExtension);
        $target = $target . $nume_fisier;
        
        // Verifică că directorul video există și este scriabil înainte de upload
        if (!file_exists($videoDir) || !is_writable($videoDir)) {
            error_log("Cannot upload video: directory not writable - " . $videoDir);
            echo "Eroare: Directorul video nu este scriabil. Contactează administratorul.";
            continue;
        }
        
        if (move_uploaded_file($_FILES['uploaded']['tmp_name'][$i], $target)) {
            // Verifică că fișierul a fost salvat corect
            if (!file_exists($target)) {
                error_log("Video file was not saved correctly: " . $target);
                error_log("Target directory: " . $videoDir);
                error_log("Target file: " . $target);
                continue;
            }
            
            // Log succes upload
            error_log("Video uploaded successfully: " . $nume_fisier . " -> " . $target);
            error_log("File size: " . filesize($target) . " bytes");
            
            // Procesează thumbnail încărcat manual (dacă există)
            $thumbDbName = NULL;
            
            // Verifică dacă există un thumbnail pentru acest video
            if (isset($_FILES['thumbnails']['name'][$i]) && $_FILES['thumbnails']['name'][$i] != "") {
                error_log("=== THUMBNAIL UPLOAD START ===");
                error_log("Thumbnail file: " . $_FILES['thumbnails']['name'][$i]);
                
                // Generează nume pentru thumbnail (folosind extensia originală sau .jpg)
                $thumbOriginalName = $_FILES['thumbnails']['name'][$i];
                $thumbExtension = strtolower(pathinfo($thumbOriginalName, PATHINFO_EXTENSION));
                if (!in_array($thumbExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $thumbExtension = 'jpg';
                }
                $thumbName = pathinfo($nume_fisier, PATHINFO_FILENAME) . "_thumb." . $thumbExtension;
                $targetThumb = $thumbDir . $thumbName;
                
                error_log("Target thumbnail: " . $targetThumb);
                error_log("Thumbnail directory exists: " . (file_exists($thumbDir) ? 'YES' : 'NO'));
                error_log("Thumbnail directory writable: " . (is_writable($thumbDir) ? 'YES' : 'NO'));
                
                $thumbUploaded = processUploadedThumbnail($i, $targetThumb);
                
                if ($thumbUploaded && file_exists($targetThumb)) {
                    $thumbDbName = 'video/thumbs/' . $thumbName;
                    error_log("✓ Thumbnail uploaded successfully: " . $thumbDbName);
                    error_log("Thumbnail size: " . filesize($targetThumb) . " bytes");
                } else {
                    error_log("✗ Thumbnail upload FAILED");
                    // Dacă nu reușește, creează placeholder cu PHP GD
                    if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
                        error_log("Trying PHP GD placeholder as fallback...");
                        $thumbName = pathinfo($nume_fisier, PATHINFO_FILENAME) . "_thumb.jpg";
                        $targetThumb = $thumbDir . $thumbName;
                        $placeholderCreated = createVideoPlaceholder($targetThumb);
                        if ($placeholderCreated && file_exists($targetThumb)) {
                            $thumbDbName = 'video/thumbs/' . $thumbName;
                            error_log("✓ Placeholder thumbnail created successfully: " . $thumbDbName);
                        } else {
                            error_log("✗ Failed to create placeholder thumbnail: " . $targetThumb);
                        }
                    } else {
                        error_log("✗ PHP GD not available for placeholder creation");
                    }
                }
                error_log("=== THUMBNAIL UPLOAD END ===");
            } else {
                // Dacă nu există thumbnail încărcat, creează placeholder
                error_log("No thumbnail uploaded for video: " . $nume_fisier);
                if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
                    $thumbName = pathinfo($nume_fisier, PATHINFO_FILENAME) . "_thumb.jpg";
                    $targetThumb = $thumbDir . $thumbName;
                    $placeholderCreated = createVideoPlaceholder($targetThumb);
                    if ($placeholderCreated && file_exists($targetThumb)) {
                        $thumbDbName = 'video/thumbs/' . $thumbName;
                        error_log("✓ Placeholder thumbnail created: " . $thumbDbName);
                    }
                }
            }
            
            // Log pentru debugging
            if ($thumbDbName) {
                error_log("Final thumbnail path in DB: " . $thumbDbName);
            } else {
                error_log("WARNING: No thumbnail created for: " . $nume_fisier);
            }
            
            $insertSQL = sprintf("INSERT INTO fotov_video (file, oferta, nota, thumb) VALUES (%s, %s, %s, %s)",
                GetSQLValueString($nume_fisier, "text"),
                GetSQLValueString($_GET['id'], "int"),
                $i,
                $thumbDbName ? GetSQLValueString($thumbDbName, "text") : "NULL"
            );
            
            error_log("Inserting video into database: " . $insertSQL);
            $Result1 = $connection->query($insertSQL);
            
            if (!$Result1) {
                error_log("Database insert failed: " . $connection->error);
                error_log("SQL: " . $insertSQL);
            } else {
                error_log("Video inserted successfully into database with ID: " . $connection->insert_id);
            }
        } else {
            error_log("Failed to move uploaded file: " . $_FILES['uploaded']['name'][$i]);
            error_log("Source: " . $_FILES['uploaded']['tmp_name'][$i]);
            error_log("Target: " . $target);
            error_log("Upload error code: " . ($_FILES['uploaded']['error'][$i] ?? 'N/A'));
            error_log("Video directory exists: " . (file_exists($videoDir) ? 'YES' : 'NO'));
            error_log("Video directory writable: " . (is_writable($videoDir) ? 'YES' : 'NO'));
            echo "Imi pare rau dar nu am putut uploada fisierul, s-a intamplat ceva, mai incearca odata si daca tot nu merge vorbeste cu Marius <webmaster@grupnet.ro>.";
            EXIT;
        }
    }
}

$edit_url = BASE_PATH . "tehnic/vezi_anunturi.php?id=" . $_GET['id'];
header("Location: $edit_url");
exit;
?>

