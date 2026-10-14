<?php
/**
 * Script de diagnostic pentru verificarea căilor video
 * Accesează: https://domeniul-tau.ro/tehnic/check_video_paths.php
 */

require_once('../includes/config.php');

$headerTitle = 'Verificare Căi Video';
include 'header.php';
?>

<!-- InstanceBeginEditable name="main" -->
<td align="center" valign="middle" style="padding: 20px;">
    <h2>🔍 Verificare Căi Video</h2>
    
    <div style="text-align: left; max-width: 900px; margin: 0 auto; background: #f9f9f9; padding: 20px; border: 1px solid #ddd;">
        
        <h3>1. Informații Sistem</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background: #f0f0f0;">
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Parametru</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Valoare</th>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>__DIR__</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><code><?php echo __DIR__; ?></code></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>dirname(__DIR__)</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><code><?php echo dirname(__DIR__); ?></code></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>DOCUMENT_ROOT</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><code><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'N/A'; ?></code></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><strong>BASE_PATH</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><code><?php echo BASE_PATH; ?></code></td>
            </tr>
        </table>
        
        <h3>2. Detectare Project Root</h3>
        <?php
        // Logica de detectare (ca în upload_video2.php)
        $projectRoot = dirname(__DIR__);
        $detectionMethod = "dirname(__DIR__)";
        
        // Verificare suplimentară pentru Linux
        if (!file_exists($projectRoot . "/foto/") && isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            if (file_exists($docRoot . "/foto/")) {
                $projectRoot = $docRoot;
                $detectionMethod = "DOCUMENT_ROOT (foto/ found)";
            } elseif (file_exists(dirname($docRoot) . "/foto/")) {
                $projectRoot = dirname($docRoot);
                $detectionMethod = "dirname(DOCUMENT_ROOT) (foto/ found)";
            }
        }
        
        // Normalizează calea
        $projectRoot = rtrim(str_replace('\\', '/', $projectRoot), '/');
        
        $videoDir = $projectRoot . "/video/";
        $thumbDir = $projectRoot . "/video/thumbs/";
        ?>
        
        <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; border-radius: 4px;">
            <p><strong>Metodă de detectare:</strong> <?php echo htmlspecialchars($detectionMethod); ?></p>
            <p><strong>Project Root detectat:</strong> <code><?php echo htmlspecialchars($projectRoot); ?></code></p>
            <p><strong>Video Directory:</strong> <code><?php echo htmlspecialchars($videoDir); ?></code></p>
            <p><strong>Thumbnails Directory:</strong> <code><?php echo htmlspecialchars($thumbDir); ?></code></p>
        </div>
        
        <h3>3. Verificare Directoare</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background: #f0f0f0;">
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Director</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Există</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Scriabil</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Permisiuni</th>
            </tr>
            
            <?php
            $dirs = [
                'Project Root' => $projectRoot,
                'Video Directory' => $videoDir,
                'Thumbnails Directory' => $thumbDir,
                'Foto Directory (reference)' => $projectRoot . "/foto/",
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
        
        <h3>4. Fișiere Video Existente</h3>
        <?php
        if (file_exists($videoDir)) {
            $videoFiles = glob($videoDir . "*.*");
            if (count($videoFiles) > 0) {
                echo "<p><strong>Număr fișiere găsite:</strong> " . count($videoFiles) . "</p>";
                echo "<table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
                echo "<tr style='background: #f0f0f0;'><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Fișier</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Mărime</th></tr>";
                foreach (array_slice($videoFiles, 0, 10) as $file) {
                    $filename = basename($file);
                    $size = filesize($file);
                    $sizeFormatted = $size > 1024 * 1024 ? number_format($size / (1024 * 1024), 2) . ' MB' : number_format($size / 1024, 2) . ' KB';
                    echo "<tr>";
                    echo "<td style='padding: 8px; border: 1px solid #ddd;'><code>" . htmlspecialchars($filename) . "</code></td>";
                    echo "<td style='padding: 8px; border: 1px solid #ddd;'>$sizeFormatted</td>";
                    echo "</tr>";
                }
                if (count($videoFiles) > 10) {
                    echo "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; color: gray;'>... și încă " . (count($videoFiles) - 10) . " fișiere</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: orange;'>⚠ Nu s-au găsit fișiere video în director.</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Directorul video nu există!</p>";
        }
        ?>
        
        <h3>5. Thumbnail-uri Existente</h3>
        <?php
        if (file_exists($thumbDir)) {
            $thumbFiles = glob($thumbDir . "*.*");
            if (count($thumbFiles) > 0) {
                echo "<p><strong>Număr thumbnail-uri găsite:</strong> " . count($thumbFiles) . "</p>";
                echo "<table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
                echo "<tr style='background: #f0f0f0;'><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Fișier</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Mărime</th></tr>";
                foreach (array_slice($thumbFiles, 0, 10) as $file) {
                    $filename = basename($file);
                    $size = filesize($file);
                    $sizeFormatted = $size > 1024 ? number_format($size / 1024, 2) . ' KB' : $size . ' bytes';
                    echo "<tr>";
                    echo "<td style='padding: 8px; border: 1px solid #ddd;'><code>" . htmlspecialchars($filename) . "</code></td>";
                    echo "<td style='padding: 8px; border: 1px solid #ddd;'>$sizeFormatted</td>";
                    echo "</tr>";
                }
                if (count($thumbFiles) > 10) {
                    echo "<tr><td colspan='2' style='padding: 8px; border: 1px solid #ddd; color: gray;'>... și încă " . (count($thumbFiles) - 10) . " fișiere</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: orange;'>⚠ Nu s-au găsit thumbnail-uri în director.</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Directorul thumbnail-uri nu există!</p>";
        }
        ?>
        
        <h3>6. Verificare Baza de Date</h3>
        <?php
        $query = "SELECT COUNT(*) as total FROM fotov_video";
        $result = $connection->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $totalVideos = $row['total'];
            echo "<p><strong>Total videouri în baza de date:</strong> $totalVideos</p>";
            
            if ($totalVideos > 0) {
                $query_samples = "SELECT file, thumb FROM fotov_video LIMIT 5";
                $result_samples = $connection->query($query_samples);
                if ($result_samples) {
                    echo "<table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
                    echo "<tr style='background: #f0f0f0;'><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Video File</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Thumbnail Path</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Există Video</th><th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Există Thumb</th></tr>";
                    while ($row = $result_samples->fetch_assoc()) {
                        $videoFile = $row['file'];
                        $thumbFile = $row['thumb'];
                        
                        $videoExists = file_exists($videoDir . $videoFile);
                        $thumbExists = !empty($thumbFile) && file_exists($projectRoot . '/' . $thumbFile);
                        
                        echo "<tr>";
                        echo "<td style='padding: 8px; border: 1px solid #ddd;'><code>" . htmlspecialchars($videoFile) . "</code></td>";
                        echo "<td style='padding: 8px; border: 1px solid #ddd;'><code>" . htmlspecialchars($thumbFile ?: 'N/A') . "</code></td>";
                        echo "<td style='padding: 8px; border: 1px solid #ddd; color: " . ($videoExists ? 'green' : 'red') . ";'>" . ($videoExists ? '✓' : '✗') . "</td>";
                        echo "<td style='padding: 8px; border: 1px solid #ddd; color: " . ($thumbExists ? 'green' : 'red') . ";'>" . ($thumbExists ? '✓' : '✗') . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        }
        ?>
        
        <h3>7. Recomandări</h3>
        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; border-radius: 4px;">
            <?php
            if (!file_exists($videoDir)) {
                echo "<p style='color: red;'><strong>⚠️ Directorul video nu există!</strong></p>";
                echo "<p>Rulează următoarea comandă prin SSH sau cPanel Terminal:</p>";
                echo "<pre style='background: #fff; padding: 10px; border: 1px solid #ddd;'>mkdir -p " . htmlspecialchars($videoDir) . "\nchmod 755 " . htmlspecialchars($videoDir) . "</pre>";
            }
            
            if (!file_exists($thumbDir)) {
                echo "<p style='color: red;'><strong>⚠️ Directorul thumbnail-uri nu există!</strong></p>";
                echo "<p>Rulează următoarea comandă prin SSH sau cPanel Terminal:</p>";
                echo "<pre style='background: #fff; padding: 10px; border: 1px solid #ddd;'>mkdir -p " . htmlspecialchars($thumbDir) . "\nchmod 755 " . htmlspecialchars($thumbDir) . "</pre>";
            }
            
            if (file_exists($videoDir) && !is_writable($videoDir)) {
                echo "<p style='color: orange;'><strong>⚠️ Directorul video nu este scriabil!</strong></p>";
                echo "<p>Rulează următoarea comandă prin SSH sau cPanel Terminal:</p>";
                echo "<pre style='background: #fff; padding: 10px; border: 1px solid #ddd;'>chmod 755 " . htmlspecialchars($videoDir) . "</pre>";
            }
            
            if (file_exists($thumbDir) && !is_writable($thumbDir)) {
                echo "<p style='color: orange;'><strong>⚠️ Directorul thumbnail-uri nu este scriabil!</strong></p>";
                echo "<p>Rulează următoarea comandă prin SSH sau cPanel Terminal:</p>";
                echo "<pre style='background: #fff; padding: 10px; border: 1px solid #ddd;'>chmod 755 " . htmlspecialchars($thumbDir) . "</pre>";
            }
            
            if (file_exists($videoDir) && is_writable($videoDir) && file_exists($thumbDir) && is_writable($thumbDir)) {
                echo "<p style='color: green;'><strong>✓ Toate directoarele sunt configurate corect!</strong></p>";
            }
            ?>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 4px;">
            <p><a href="vezi_anunturi.php">← Înapoi la anunțuri</a> | <a href="check_ffmpeg.php">Verificare FFmpeg →</a></p>
        </div>
    </div>
</td>
<!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>

