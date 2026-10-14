<?php
/**
 * Script de test pentru generarea thumbnail-urilor video
 * Accesează: https://domeniul-tau.ro/tehnic/test_thumbnail_generation.php
 */

require_once('../includes/config.php');

$headerTitle = 'Test Generare Thumbnail Video';
include 'header.php';
?>

<!-- InstanceBeginEditable name="main" -->
<td align="center" valign="middle" style="padding: 20px;">
    <h2>🎬 Test Generare Thumbnail Video</h2>
    
    <div style="text-align: left; max-width: 900px; margin: 0 auto; background: #f9f9f9; padding: 20px; border: 1px solid #ddd;">
        
        <h3>1. Verificare FFmpeg</h3>
        <?php
        // Detectează sistemul de operare
        $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        $ffmpegExecutable = $isWindows ? 'ffmpeg.exe' : 'ffmpeg';
        
        // Logica de detectare FFmpeg (ca în upload_video2.php)
        $ffmpegCmd = 'ffmpeg';
        $ffmpegPath = null;
        
        // Verifică dacă există variabila de mediu ffmpeg
        $ffmpegEnvPath = getenv('ffmpeg');
        if ($ffmpegEnvPath) {
            $ffmpegExe = $ffmpegEnvPath . DIRECTORY_SEPARATOR . $ffmpegExecutable;
            if (file_exists($ffmpegExe)) {
                $ffmpegCmd = $isWindows ? '"' . $ffmpegExe . '"' : escapeshellarg($ffmpegExe);
                $ffmpegPath = $ffmpegExe;
            }
        }
        
        // Verifică instalare manuală în ~/bin (pentru Linux/shared hosting)
        if (!$isWindows) {
            // Încearcă mai multe metode de a găsi home directory
            $homeDir = getenv('HOME');
            
            // Dacă HOME nu e setat, încearcă din DOCUMENT_ROOT
            if (!$homeDir && isset($_SERVER['DOCUMENT_ROOT'])) {
                $docRoot = $_SERVER['DOCUMENT_ROOT'];
                // Pentru /home/username/public_html -> /home/username
                if (preg_match('#^/home/([^/]+)/public_html#', $docRoot, $matches)) {
                    $homeDir = '/home/' . $matches[1];
                } elseif (preg_match('#^/home/([^/]+)#', $docRoot, $matches)) {
                    $homeDir = '/home/' . $matches[1];
                }
            }
            
            // Încearcă și din $_SERVER['HOME']
            if (!$homeDir && isset($_SERVER['HOME'])) {
                $homeDir = $_SERVER['HOME'];
            }
            
            // posix_getpwuid (dacă e disponibil)
            if (!$homeDir && function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
                $userInfo = @posix_getpwuid(posix_geteuid());
                if ($userInfo && isset($userInfo['dir'])) {
                    $homeDir = $userInfo['dir'];
                }
            }
            
            if ($homeDir) {
                $manualPath = $homeDir . '/bin/ffmpeg';
                if (file_exists($manualPath) && is_executable($manualPath)) {
                    $ffmpegCmd = escapeshellarg($manualPath);
                    $ffmpegPath = $manualPath;
                }
            }
            
            // Verifică și în locații comune
            $commonPaths = [
                '/usr/bin/ffmpeg',
                '/usr/local/bin/ffmpeg',
                '/opt/ffmpeg/bin/ffmpeg',
            ];
            
            foreach ($commonPaths as $path) {
                if (file_exists($path) && is_executable($path)) {
                    $ffmpegCmd = escapeshellarg($path);
                    $ffmpegPath = $path;
                    break;
                }
            }
        }
        
        // Testează FFmpeg
        $output = [];
        $return_var = 0;
        @exec($ffmpegCmd . ' -version 2>&1', $output, $return_var);
        
        if ($return_var === 0 && !empty($output)) {
            echo '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;">';
            echo '<strong style="color: green;">✓ FFmpeg este disponibil!</strong><br>';
            echo '<strong>Cale:</strong> <code>' . htmlspecialchars($ffmpegPath ?: $ffmpegCmd) . '</code><br>';
            echo '<strong>Versiune:</strong> <code>' . htmlspecialchars($output[0]) . '</code>';
            echo '</div>';
        } else {
            echo '<div style="background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;">';
            echo '<strong style="color: red;">✗ FFmpeg NU este disponibil sau nu funcționează!</strong><br>';
            if (!empty($output)) {
                echo '<pre style="background: #fff; padding: 10px; margin-top: 10px; font-size: 0.9em;">' . htmlspecialchars(implode("\n", $output)) . '</pre>';
            }
            echo '</div>';
        }
        ?>
        
        <h3>2. Verificare PHP GD (pentru placeholder)</h3>
        <?php
        $gdAvailable = extension_loaded('gd');
        if ($gdAvailable) {
            $gdInfo = gd_info();
            echo '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;">';
            echo '<strong style="color: green;">✓ PHP GD este disponibil!</strong><br>';
            echo '<strong>Versiune:</strong> ' . htmlspecialchars($gdInfo['GD Version'] ?? 'N/A');
            echo '</div>';
        } else {
            echo '<div style="background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;">';
            echo '<strong style="color: orange;">⚠ PHP GD NU este disponibil!</strong><br>';
            echo 'Thumbnail-urile placeholder nu pot fi generate.';
            echo '</div>';
        }
        ?>
        
        <h3>3. Test Generare Thumbnail</h3>
        <?php
        // Calculează root-ul proiectului
        $projectRoot = dirname(__DIR__);
        
        // Verificare suplimentară pentru Linux
        if (!file_exists($projectRoot . "/foto/") && isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            if (file_exists($docRoot . "/foto/")) {
                $projectRoot = $docRoot;
            } elseif (file_exists(dirname($docRoot) . "/foto/")) {
                $projectRoot = dirname($docRoot);
            }
        }
        
        $projectRoot = rtrim(str_replace('\\', '/', $projectRoot), '/');
        $videoDir = $projectRoot . "/video/";
        $thumbDir = $projectRoot . "/video/thumbs/";
        
        // Găsește primul video disponibil
        $testVideo = null;
        if (file_exists($videoDir)) {
            $videoFiles = glob($videoDir . "*.{mp4,webm,mov,avi,MP4,WEBM,MOV,AVI}", GLOB_BRACE);
            if (!empty($videoFiles)) {
                $testVideo = $videoFiles[0];
            }
        }
        
        if ($testVideo) {
            echo '<p><strong>Video de test:</strong> <code>' . htmlspecialchars(basename($testVideo)) . '</code></p>';
            echo '<p><strong>Cale completă:</strong> <code>' . htmlspecialchars($testVideo) . '</code></p>';
            echo '<p><strong>Mărime:</strong> ' . number_format(filesize($testVideo) / (1024 * 1024), 2) . ' MB</p>';
            
            // Generează thumbnail de test
            $testThumbName = 'test_thumb_' . time() . '.jpg';
            $testThumbPath = $thumbDir . $testThumbName;
            
            echo '<h4>Test 1: Generare cu FFmpeg (încercăm multiple variante)</h4>';
            if ($ffmpegPath || $return_var === 0) {
                // Testăm toate variantele ca în upload_video2.php
                $commands = [
                    ['cmd' => $ffmpegCmd . " -nostdin -ss 00:00:01 -i " . escapeshellarg($testVideo) . " -frames:v 1 -an -q:v 2 " . escapeshellarg($testThumbPath) . " 2>&1", 'name' => 'Varianta 1 (nostdin, frames:v)'],
                    ['cmd' => $ffmpegCmd . " -nostdin -threads 1 -ss 00:00:01 -i " . escapeshellarg($testVideo) . " -frames:v 1 -an -q:v 2 " . escapeshellarg($testThumbPath) . " 2>&1", 'name' => 'Varianta 2 (nostdin, threads 1)'],
                    ['cmd' => $ffmpegCmd . " -nostdin -i " . escapeshellarg($testVideo) . " -ss 00:00:01 -frames:v 1 -an -q:v 2 " . escapeshellarg($testThumbPath) . " 2>&1", 'name' => 'Varianta 3 (nostdin, ss după input)'],
                    ['cmd' => $ffmpegCmd . " -i " . escapeshellarg($testVideo) . " -ss 00:00:01 -vframes 1 -an -q:v 2 " . escapeshellarg($testThumbPath) . " 2>&1", 'name' => 'Varianta 4 (complet simplă)'],
                ];
                
                $success = false;
                foreach ($commands as $cmdInfo) {
                    $command = $cmdInfo['cmd'];
                    echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 4px;">';
                    echo '<p><strong>Încercare:</strong> ' . htmlspecialchars($cmdInfo['name']) . '</p>';
                    echo '<p><strong>Comandă:</strong> <code style="font-size: 0.9em;">' . htmlspecialchars($command) . '</code></p>';
                    
                    // Șterge thumbnail-ul anterior dacă există
                    if (file_exists($testThumbPath)) {
                        @unlink($testThumbPath);
                    }
                    
                    $output = [];
                    $return_var = 0;
                    @exec($command, $output, $return_var);
                
                    if (file_exists($testThumbPath) && filesize($testThumbPath) > 0) {
                        echo '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;">';
                        echo '<strong style="color: green;">✓ Thumbnail generat cu succes cu ' . htmlspecialchars($cmdInfo['name']) . '!</strong><br>';
                        echo '<strong>Mărime:</strong> ' . number_format(filesize($testThumbPath) / 1024, 2) . ' KB<br>';
                        echo '<img src="' . BASE_PATH . 'video/thumbs/' . $testThumbName . '" style="max-width: 320px; border: 1px solid #ddd; margin-top: 10px;" alt="Test thumbnail">';
                        echo '</div>';
                        $success = true;
                        break; // Oprește la prima variantă care funcționează
                    } else {
                        echo '<p><strong>Return code:</strong> ' . $return_var . '</p>';
                        if (!empty($output)) {
                            echo '<p><strong>Output FFmpeg (ultimele 10 linii):</strong></p>';
                            $relevantOutput = array_slice($output, -10);
                            echo '<pre style="background: #fff; padding: 10px; margin-top: 10px; font-size: 0.9em; max-height: 150px; overflow: auto; border: 1px solid #ddd;">' . htmlspecialchars(implode("\n", $relevantOutput)) . '</pre>';
                        }
                        echo '</div>';
                    }
                }
                
                if (!$success) {
                    echo '<div style="background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;">';
                    echo '<strong style="color: red;">✗ Toate variantele FFmpeg au eșuat!</strong><br>';
                    echo '<p>FFmpeg nu poate genera thumbnail-uri. Sistemul va folosi placeholder-uri PHP GD.</p>';
                    
                    // Verificări suplimentare
                    echo '<h5 style="margin-top: 15px;">Verificări suplimentare:</h5>';
                    echo '<ul style="margin-left: 20px;">';
                    echo '<li>Video file exists: ' . (file_exists($testVideo) ? '✓ YES' : '✗ NO') . '</li>';
                    echo '<li>Video file readable: ' . (is_readable($testVideo) ? '✓ YES' : '✗ NO') . '</li>';
                    echo '<li>Thumbnail directory exists: ' . (file_exists($thumbDir) ? '✓ YES' : '✗ NO') . '</li>';
                    echo '<li>Thumbnail directory writable: ' . (is_writable($thumbDir) ? '✓ YES' : '✗ NO') . '</li>';
                    echo '<li>FFmpeg executable: <code>' . htmlspecialchars($ffmpegPath ?: $ffmpegCmd) . '</code></li>';
                    echo '<li>FFmpeg file exists: ' . (file_exists($ffmpegPath ?: $ffmpegCmd) ? '✓ YES' : '✗ NO') . '</li>';
                    if ($ffmpegPath) {
                        echo '<li>FFmpeg executable: ' . (is_executable($ffmpegPath) ? '✓ YES' : '✗ NO') . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
            } else {
                echo '<p style="color: orange;">⚠ FFmpeg nu este disponibil pentru test.</p>';
            }
            
            echo '<h4>Test 2: Generare Placeholder cu PHP GD</h4>';
            if ($gdAvailable) {
                $testPlaceholderName = 'test_placeholder_' . time() . '.jpg';
                $testPlaceholderPath = $thumbDir . $testPlaceholderName;
                
                $width = 320;
                $height = 240;
                $image = imagecreatetruecolor($width, $height);
                $bgColor = imagecolorallocate($image, 40, 40, 40);
                imagefill($image, 0, 0, $bgColor);
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $centerX = $width / 2;
                $centerY = $height / 2;
                $circleRadius = 40;
                $circleColor = imagecolorallocate($image, 200, 200, 200);
                imagefilledellipse($image, $centerX, $centerY, $circleRadius * 2, $circleRadius * 2, $circleColor);
                $triangleSize = 20;
                $points = [
                    $centerX - $triangleSize, $centerY - $triangleSize,
                    $centerX - $triangleSize, $centerY + $triangleSize,
                    $centerX + $triangleSize, $centerY
                ];
                imagefilledpolygon($image, $points, 3, $textColor);
                
                if (imagejpeg($image, $testPlaceholderPath, 85)) {
                    echo '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;">';
                    echo '<strong style="color: green;">✓ Placeholder generat cu succes cu PHP GD!</strong><br>';
                    echo '<strong>Mărime:</strong> ' . number_format(filesize($testPlaceholderPath) / 1024, 2) . ' KB<br>';
                    echo '<img src="' . BASE_PATH . 'video/thumbs/' . $testPlaceholderName . '" style="max-width: 320px; border: 1px solid #ddd; margin-top: 10px;" alt="Test placeholder">';
                    echo '</div>';
                } else {
                    echo '<div style="background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;">';
                    echo '<strong style="color: red;">✗ Placeholder NU a fost generat!</strong>';
                    echo '</div>';
                }
                imagedestroy($image);
            } else {
                echo '<p style="color: orange;">⚠ PHP GD nu este disponibil pentru test.</p>';
            }
            
        } else {
            echo '<div style="background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;">';
            echo '<strong style="color: orange;">⚠ Nu s-a găsit niciun video pentru test!</strong><br>';
            echo 'Încarcă un video mai întâi, apoi rulează din nou acest test.';
            echo '</div>';
        }
        ?>
        
        <h3>4. Verificare Permisiuni Directoare</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background: #f0f0f0;">
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Director</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Există</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Scriabil</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Permisiuni</th>
            </tr>
            <?php
            $dirs = [
                'Video Directory' => $videoDir,
                'Thumbnails Directory' => $thumbDir,
            ];
            
            foreach ($dirs as $label => $dir) {
                $exists = file_exists($dir);
                $writable = $exists && is_writable($dir);
                $perms = $exists ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A';
                
                $existsColor = $exists ? 'green' : 'red';
                $writableColor = $writable ? 'green' : 'orange';
                
                echo "<tr>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'><strong>$label</strong><br><code style='font-size: 0.9em;'>" . htmlspecialchars($dir) . "</code></td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; color: $existsColor;'>" . ($exists ? '✓ DA' : '✗ NU') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; color: $writableColor;'>" . ($writable ? '✓ DA' : '✗ NU') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'><code>$perms</code></td>";
                echo "</tr>";
            }
            ?>
        </table>
        
        <h3>5. Log-uri Recente (Ultimele 20 linii)</h3>
        <?php
        // Încearcă să găsească log-urile PHP
        $logPaths = [
            '/home/autoland/logs/error_log',
            '/home/autoland/public_html/logs/error_log',
            ini_get('error_log'),
            sys_get_temp_dir() . '/php_errors.log',
        ];
        
        $logFound = false;
        foreach ($logPaths as $logPath) {
            if ($logPath && file_exists($logPath) && is_readable($logPath)) {
                $logFound = true;
                echo '<p><strong>Log file:</strong> <code>' . htmlspecialchars($logPath) . '</code></p>';
                
                // Citește ultimele 20 linii
                $lines = file($logPath);
                $recentLines = array_slice($lines, -20);
                
                // Filtrează doar liniile relevante pentru video/thumbnail
                $relevantLines = array_filter($recentLines, function($line) {
                    return stripos($line, 'video') !== false || 
                           stripos($line, 'thumbnail') !== false || 
                           stripos($line, 'ffmpeg') !== false ||
                           stripos($line, 'thumb') !== false;
                });
                
                if (!empty($relevantLines)) {
                    echo '<pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; font-size: 0.9em; max-height: 400px; overflow: auto;">';
                    echo htmlspecialchars(implode('', $relevantLines));
                    echo '</pre>';
                } else {
                    echo '<p style="color: gray;">Nu s-au găsit linii relevante în log-uri.</p>';
                }
                break;
            }
        }
        
        if (!$logFound) {
            echo '<p style="color: orange;">⚠ Nu s-a putut găsi fișierul de log.</p>';
            echo '<p>Verifică manual log-urile PHP prin cPanel → Errors sau prin SSH.</p>';
        }
        ?>
        
        <div style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 4px;">
            <p><a href="vezi_anunturi.php">← Înapoi la anunțuri</a> | <a href="check_video_paths.php">Verificare Căi →</a></p>
        </div>
    </div>
</td>
<!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>

