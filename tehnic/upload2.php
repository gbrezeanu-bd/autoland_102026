<?php 
if (!isset($_SESSION)) {
    session_start();
}
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

function getUniqueFilename($conn, $filename) {
    $counter = 1;
    $baseFilename = $filename;
    $newFilename = $baseFilename . ".jpg";
    
    // Limit iterations to prevent infinite loop
    $maxIterations = 1000;
    $iterations = 0;

    while ($iterations < $maxIterations) {
        // SQL query to check if the file exists
        $sql = "SELECT * FROM fotov WHERE file = '" . $conn->real_escape_string($newFilename) . "'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            // File does not exist, return the unique filename
            return $newFilename;
        } else {
            // File exists, increment the filename properly
            $newFilename = $baseFilename . "_" . $counter . ".jpg";
            $counter++;
        }
        $iterations++;
    }
    
    // If we reach here, something is wrong - return a timestamp-based unique name
    return $baseFilename . "_" . time() . ".jpg";
}

// Check for duplicate filenames BEFORE uploading
$duplicates = array();
for($i=0; $i < 15; $i++) {
    if (!empty($_FILES['uploaded']['name'][$i])) {
        $nume_fisier = $_FILES['uploaded']['name'][$i];
        $stop = strlen($nume_fisier);
        $start = $stop - 4;
        $nume_fisier_base = substr($nume_fisier, 0, $start);
        $nume_fisier_check = $nume_fisier_base . ".jpg";
        
        // Check if file exists in database
        $sql = "SELECT file FROM fotov WHERE file = '" . $connection->real_escape_string($nume_fisier_check) . "'";
        $result = $connection->query($sql);
        
        if ($result->num_rows > 0) {
            // Get the new filename that will be used
            $new_name = getUniqueFilename($connection, $nume_fisier_base);
            $duplicates[] = array(
                'original' => $nume_fisier_check,
                'new' => $new_name
            );
        }
    }
}

// If duplicates found and user hasn't confirmed, show popup
if (!empty($duplicates) && !isset($_POST['confirm_rename'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Imagini Duplicate Detectate</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .popup-container {
                background-color: white;
                border: 2px solid #ff9800;
                border-radius: 8px;
                padding: 30px;
                max-width: 700px;
                margin: 50px auto;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            h2 {
                color: #ff9800;
                margin-top: 0;
            }
            .info-box {
                background-color: #e3f2fd;
                border: 1px solid #2196f3;
                border-radius: 4px;
                padding: 15px;
                margin: 20px 0;
            }
            .duplicate-list {
                background-color: #fff3cd;
                border: 1px solid #ffc107;
                border-radius: 4px;
                padding: 15px;
                margin: 20px 0;
                max-height: 400px;
                overflow-y: auto;
            }
            .duplicate-item {
                padding: 10px 0;
                border-bottom: 1px solid #ffe082;
            }
            .duplicate-item:last-child {
                border-bottom: none;
            }
            .filename {
                font-weight: bold;
                color: #d32f2f;
            }
            .arrow {
                color: #1976d2;
                font-weight: bold;
                margin: 0 10px;
            }
            .new-filename {
                font-weight: bold;
                color: #388e3c;
            }
            .button-container {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 25px;
            }
            button {
                border: none;
                padding: 12px 30px;
                font-size: 16px;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.3s;
            }
            .btn-continue {
                background-color: #4caf50;
                color: white;
            }
            .btn-continue:hover {
                background-color: #45a049;
            }
            .btn-cancel {
                background-color: #f44336;
                color: white;
            }
            .btn-cancel:hover {
                background-color: #da190b;
            }
        </style>
    </head>
    <body>
        <div class="popup-container">
            <h2>⚠️ Imagini Duplicate Detectate!</h2>
            
            <div class="info-box">
                <strong>ℹ️ Informație:</strong> Următoarele imagini există deja în sistem. 
                Ele vor fi redenumite automat pentru a evita suprascrierile.
            </div>
            
            <p><strong>Redenumiri propuse:</strong></p>
            <div class="duplicate-list">
                <?php foreach ($duplicates as $duplicate): ?>
                    <div class="duplicate-item">
                        <span class="filename"><?php echo htmlspecialchars($duplicate['original']); ?></span>
                        <span class="arrow">→</span>
                        <span class="new-filename"><?php echo htmlspecialchars($duplicate['new']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <p><strong>Ce dorești să faci?</strong></p>
            <ul>
                <li><strong>Continuă:</strong> Imaginile vor fi încărcate cu numele noi</li>
                <li><strong>Anulează:</strong> Înapoi la formular pentru a redenumi manual</li>
            </ul>
            
            <form method="POST" action="">
                <input type="hidden" name="confirm_rename" value="1">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                <?php
                // Save uploaded files to temporary directory
                $upload_batch_id = uniqid('upload_', true);
                $temp_dir = sys_get_temp_dir() . '/autoland_upload_' . $upload_batch_id . '/';
                
                if (!file_exists($temp_dir)) {
                    mkdir($temp_dir, 0777, true);
                }
                
                $temp_files = array();
                for($i = 0; $i < 15; $i++) {
                    if (!empty($_FILES['uploaded']['name'][$i]) && isset($_FILES['uploaded']['tmp_name'][$i]) && $_FILES['uploaded']['error'][$i] === 0) {
                        $temp_file = $temp_dir . $i . '_' . basename($_FILES['uploaded']['name'][$i]);
                        if (move_uploaded_file($_FILES['uploaded']['tmp_name'][$i], $temp_file)) {
                            $temp_files[$i] = array(
                                'name' => $_FILES['uploaded']['name'][$i],
                                'temp_path' => $temp_file
                            );
                        }
                    }
                }
                
                $_SESSION['temp_upload_files'] = $temp_files;
                $_SESSION['temp_upload_id'] = $_GET['id'];
                $_SESSION['upload_batch_id'] = $upload_batch_id;
                ?>
                <div class="button-container">
                    <button type="submit" class="btn-continue">✓ Continuă cu Redenumire Automată</button>
                    <button type="button" class="btn-cancel" onclick="history.back()">✗ Anulează</button>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// If user confirmed, restore the files from session
if (isset($_POST['confirm_rename']) && isset($_SESSION['temp_upload_files'])) {
    $temp_files = $_SESSION['temp_upload_files'];
    $_GET['id'] = $_SESSION['temp_upload_id'];
    $foto_dir = dirname(__DIR__) . "/foto/";
    
    // Process files from temporary location
    for($i = 0; $i < 15; $i++) {
        if (isset($temp_files[$i]) && file_exists($temp_files[$i]['temp_path'])) {
            $nume_fisier = $temp_files[$i]['name'];
            $stop = strlen($nume_fisier);
            $start = $stop - 4;
            $nume_fisier_base = substr($nume_fisier, 0, $start);
            $nume_fisier_final = getUniqueFilename($connection, basename($nume_fisier_base));
            $target = $foto_dir . $nume_fisier_final;
            
            // Use copy() for files from temp directory (not direct uploads)
            if (copy($temp_files[$i]['temp_path'], $target)) {
                $insertSQL = sprintf("INSERT INTO fotov (file, oferta, nota) VALUES (%s, %s, %s)",
                    GetSQLValueString($nume_fisier_final, "text"),
                    GetSQLValueString($_GET['id'], "int"), 
                    $i);
                
                $connection->query($insertSQL) or die($connection->error);
                
                // Delete temp file after successful copy
                @unlink($temp_files[$i]['temp_path']);
            } else {
                echo "Eroare la copierea fișierului " . htmlspecialchars($nume_fisier);
                exit;
            }
        }
    }
    
    // Clean up temp directory
    if (isset($_SESSION['upload_batch_id'])) {
        $temp_dir = sys_get_temp_dir() . '/autoland_upload_' . $_SESSION['upload_batch_id'] . '/';
        if (file_exists($temp_dir)) {
            @rmdir($temp_dir);
        }
    }
    
    unset($_SESSION['temp_upload_files']);
    unset($_SESSION['temp_upload_id']);
    unset($_SESSION['upload_batch_id']);
    
    $edit_url = BASE_PATH . "tehnic/vezi_anunturi.php?id=" . $_GET['id'];
    header("Location: $edit_url");
    exit;
}

for($i=0; $i < 15; $i++)
{
    
 $target = dirname(__DIR__) . "/foto/"; 
 $nume_fisier=$_FILES['uploaded']['name'][$i];

 if ($nume_fisier != "")
 {
 $stop=strlen($nume_fisier);
 $start=$stop-"4";
 $nume_fisier=substr($_FILES['uploaded']['name'][$i],0,$start);
 $nume_fisier = getUniqueFilename($connection, basename( $nume_fisier ));
 $target = $target . $nume_fisier; 

 if(move_uploaded_file($_FILES['uploaded']['tmp_name'][$i], $target)) 
 {
	$insertSQL = sprintf("INSERT INTO fotov (file, oferta, nota) VALUES (%s, %s, %s)",
		       GetSQLValueString($nume_fisier, "text"),
                       GetSQLValueString($_GET['id'], "int"), $i);
                                                      
	$Result1 = $connection->query($insertSQL) or die($connection->error);
 }
 else {
 echo "Imi pare rau dar nu am putut uploada fisierul, s-a intamplat ceva, mai incearca odata si daca tot nu mere vorbeste cu Marius <webmaster@grupnet.ro>.";
 EXIT;
  }
 }
}
	$edit_url=BASE_PATH."tehnic/vezi_anunturi.php?id=".$_GET['id'];
	header ("Location: $edit_url");  
 ?>