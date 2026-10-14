<?php
/**
 * Script simplu pentru detectarea sistemului de operare din cPanel
 * Accesează: https://domeniul-tau.ro/tehnic/detect_os.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detectare Sistem de Operare - cPanel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .info-box { background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; border-left-color: #28a745; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        ul { line-height: 1.8; }
        .command { background: #2d2d2d; color: #f8f8f2; padding: 10px; border-radius: 4px; font-family: 'Courier New', monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Detectare Sistem de Operare - cPanel</h1>
        
        <div class="info-box">
            <h2>Informații de Bază</h2>
            <ul>
                <li><strong>PHP_OS:</strong> <code><?php echo PHP_OS; ?></code></li>
                <li><strong>php_uname():</strong> <code><?php echo php_uname(); ?></code></li>
                <li><strong>PHP Version:</strong> <code><?php echo phpversion(); ?></code></li>
                <li><strong>Server Software:</strong> <code><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></code></li>
                <li><strong>Document Root:</strong> <code><?php echo $_SERVER['DOCUMENT_ROOT']; ?></code></li>
            </ul>
        </div>

        <?php
        $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        
        if ($isWindows) {
            echo '<div class="info-box warning">';
            echo '<h2>⚠️ Sistem: Windows</h2>';
            echo '<p>Acest script este optimizat pentru Linux/cPanel. Pentru Windows, folosește <code>test_ffmpeg.php</code>.</p>';
            echo '</div>';
        } else {
            echo '<div class="info-box success">';
            echo '<h2>✅ Sistem: Linux/Unix</h2>';
            echo '<p>Perfect pentru instalarea FFmpeg prin cPanel!</p>';
            echo '</div>';
            
            // Detectare distributie Linux
            echo '<h2>📦 Detalii Distribuție Linux</h2>';
            
            // /etc/os-release (standard modern)
            if (file_exists('/etc/os-release')) {
                $osInfo = @parse_ini_file('/etc/os-release');
                if ($osInfo) {
                    echo '<div class="info-box">';
                    echo '<h3>Din /etc/os-release:</h3>';
                    echo '<ul>';
                    echo '<li><strong>Nume:</strong> ' . ($osInfo['NAME'] ?? 'N/A') . '</li>';
                    echo '<li><strong>Versiune:</strong> ' . ($osInfo['VERSION'] ?? 'N/A') . '</li>';
                    echo '<li><strong>ID:</strong> <code>' . ($osInfo['ID'] ?? 'N/A') . '</code></li>';
                    echo '<li><strong>ID Like:</strong> <code>' . ($osInfo['ID_LIKE'] ?? 'N/A') . '</code></li>';
                    echo '<li><strong>Version ID:</strong> ' . ($osInfo['VERSION_ID'] ?? 'N/A') . '</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                    // Detectare CloudLinux specific
                    $id = strtolower($osInfo['ID'] ?? '');
                    if (strpos($id, 'cloudlinux') !== false || strpos(php_uname(), 'lve') !== false) {
                        echo '<div class="info-box warning">';
                        echo '<h3>☁️ CloudLinux Detectat</h3>';
                        echo '<p>Server-ul tău folosește <strong>CloudLinux</strong> cu LVE (Lightweight Virtual Environment).</p>';
                        echo '<ul>';
                        echo '<li>FFmpeg poate fi deja instalat pe server - verifică cu <code>which ffmpeg</code></li>';
                        echo '<li>Dacă nu ai acces <code>sudo</code>, contactează suportul hosting</li>';
                        echo '<li>CloudLinux folosește <code>yum</code> sau <code>dnf</code> ca package manager</li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                    
                    // Recomandări bazate pe ID
                    $id = strtolower($osInfo['ID'] ?? '');
                    $idLike = strtolower($osInfo['ID_LIKE'] ?? '');
                    
                    echo '<div class="info-box">';
                    echo '<h3>📋 Comandă Recomandată pentru Instalare:</h3>';
                    
                    if (strpos($id, 'centos') !== false || strpos($id, 'rhel') !== false || strpos($id, 'cloudlinux') !== false || strpos($idLike, 'rhel') !== false || strpos($idLike, 'fedora') !== false) {
                        echo '<p><strong>CentOS/RHEL/CloudLinux:</strong></p>';
                        echo '<div class="command"># Verifică mai întâi dacă este deja instalat:<br>which ffmpeg<br>ffmpeg -version<br><br># Dacă nu este instalat:<br>sudo yum install epel-release -y<br>sudo yum install ffmpeg -y<br><br># SAU pentru CentOS 8+:<br>sudo dnf install epel-release -y<br>sudo dnf install ffmpeg -y</div>';
                        echo '<div class="info-box warning">';
                        echo '<p><strong>⚠️ CloudLinux/Shared Hosting:</strong> Dacă nu ai acces sudo, contactează suportul hosting. Mulți provideri au deja FFmpeg instalat!</p>';
                        echo '</div>';
                    } elseif (strpos($id, 'ubuntu') !== false || strpos($id, 'debian') !== false || strpos($idLike, 'debian') !== false) {
                        echo '<p><strong>Ubuntu/Debian:</strong></p>';
                        echo '<div class="command">sudo apt-get update<br>sudo apt-get install ffmpeg -y</div>';
                    } else {
                        echo '<p><strong>Distribuție necunoscută. Încearcă:</strong></p>';
                        echo '<div class="command"># Pentru YUM (CentOS/RHEL):<br>sudo yum install ffmpeg -y<br><br># Pentru APT (Ubuntu/Debian):<br>sudo apt-get install ffmpeg -y</div>';
                    }
                    echo '</div>';
                }
            }
            
            // /etc/redhat-release (CentOS/RHEL/CloudLinux)
            if (file_exists('/etc/redhat-release')) {
                $redhatInfo = @file_get_contents('/etc/redhat-release');
                if ($redhatInfo) {
                    echo '<div class="info-box">';
                    echo '<h3>Din /etc/redhat-release:</h3>';
                    echo '<p><code>' . htmlspecialchars(trim($redhatInfo)) . '</code></p>';
                    echo '</div>';
                }
            }
            
            // /etc/cloudlinux-release (CloudLinux specific)
            if (file_exists('/etc/cloudlinux-release')) {
                $cloudlinuxInfo = @file_get_contents('/etc/cloudlinux-release');
                if ($cloudlinuxInfo) {
                    echo '<div class="info-box warning">';
                    echo '<h3>☁️ CloudLinux Release:</h3>';
                    echo '<p><code>' . htmlspecialchars(trim($cloudlinuxInfo)) . '</code></p>';
                    echo '<p><strong>⚠️ CloudLinux detectat:</strong> Verifică mai întâi dacă FFmpeg este deja instalat. Dacă nu ai acces sudo, contactează suportul hosting.</p>';
                    echo '</div>';
                }
            }
            
            // Detectare din kernel (lve = CloudLinux LVE)
            $kernel = php_uname('r');
            if (strpos($kernel, 'lve') !== false) {
                echo '<div class="info-box warning">';
                echo '<h3>☁️ CloudLinux LVE Detectat</h3>';
                echo '<p>Kernel-ul conține <code>lve</code> - server-ul folosește CloudLinux cu LVE.</p>';
                echo '<p><strong>Kernel Version:</strong> <code>' . htmlspecialchars($kernel) . '</code></p>';
                echo '<p><strong>Recomandare:</strong> Verifică mai întâi dacă FFmpeg este deja instalat pe server cu <code>which ffmpeg</code>.</p>';
                echo '</div>';
            }
            
            // Verifică FFmpeg
            echo '<h2>🎬 Verificare FFmpeg</h2>';
            
            $ffmpegFound = false;
            $ffmpegPath = '';
            
            // Testează which
            $whichOutput = [];
            @exec('which ffmpeg 2>&1', $whichOutput);
            if (!empty($whichOutput) && !empty($whichOutput[0]) && strpos($whichOutput[0], 'not found') === false && strpos($whichOutput[0], 'no ffmpeg') === false) {
                $ffmpegPath = trim($whichOutput[0]);
                $ffmpegFound = true;
                echo '<div class="info-box success">';
                echo '<h3>✅ FFmpeg găsit în PATH!</h3>';
                echo '<p><strong>Cale:</strong> <code>' . htmlspecialchars($ffmpegPath) . '</code></p>';
                
                // Verifică versiunea
                $versionOutput = [];
                @exec($ffmpegPath . ' -version 2>&1', $versionOutput);
                if (!empty($versionOutput)) {
                    echo '<p><strong>Versiune:</strong> <code>' . htmlspecialchars($versionOutput[0]) . '</code></p>';
                }
                echo '</div>';
            } else {
                echo '<div class="info-box warning">';
                echo '<h3>⚠️ FFmpeg NU este în PATH</h3>';
                echo '<p>Verifică locațiile comune mai jos sau instalează FFmpeg.</p>';
                echo '</div>';
            }
            
            // Verifică locații comune
            echo '<h3>🔍 Locații comune FFmpeg:</h3>';
            $commonPaths = [
                '/usr/bin/ffmpeg',
                '/usr/local/bin/ffmpeg',
                '/opt/ffmpeg/bin/ffmpeg',
                '/usr/local/ffmpeg/bin/ffmpeg',
            ];
            
            $homeDir = getenv('HOME');
            if ($homeDir) {
                $commonPaths[] = $homeDir . '/bin/ffmpeg';
                $commonPaths[] = $homeDir . '/ffmpeg/bin/ffmpeg';
            }
            
            echo '<ul>';
            $foundAny = false;
            foreach ($commonPaths as $path) {
                $exists = file_exists($path);
                $executable = $exists && is_executable($path);
                if ($exists) {
                    $foundAny = true;
                    $color = $executable ? 'green' : 'orange';
                    $icon = $executable ? '✅' : '⚠️';
                    echo "<li style='color: $color;'>$icon <code>" . htmlspecialchars($path) . "</code>";
                    echo " - " . ($executable ? "Există și este executabil" : "Există dar NU este executabil");
                    echo "</li>";
                }
            }
            if (!$foundAny) {
                echo '<li style="color: red;">❌ Nu s-a găsit FFmpeg în locațiile comune</li>';
            }
            echo '</ul>';
            
            // Verifică whereis
            $whereisOutput = [];
            @exec('whereis ffmpeg 2>&1', $whereisOutput);
            if (!empty($whereisOutput) && !empty($whereisOutput[0]) && strpos($whereisOutput[0], 'ffmpeg:') !== false) {
                echo '<div class="info-box">';
                echo '<h3>Rezultat whereis:</h3>';
                echo '<p><code>' . htmlspecialchars($whereisOutput[0]) . '</code></p>';
                echo '</div>';
            }
        }
        ?>
        
        <div class="info-box">
            <h2>📚 Pași Următori</h2>
            <ol>
                <li><strong>Dacă FFmpeg este deja instalat:</strong> Folosește <code>test_ffmpeg.php</code> pentru testare completă</li>
                <li><strong>Dacă FFmpeg NU este instalat:</strong> 
                    <ul>
                        <li>Dacă ai acces SSH/sudo: Folosește comenzile recomandate de mai sus</li>
                        <li>Dacă NU ai acces sudo: Contactează suportul hosting pentru instalare</li>
                    </ul>
                </li>
                <li><strong>După instalare:</strong> Testează cu <code>test_ffmpeg.php</code></li>
            </ol>
        </div>
        
        <div class="info-box">
            <h2>🔗 Link-uri Utile</h2>
            <ul>
                <li><a href="test_ffmpeg.php">Test FFmpeg Complet</a></li>
                <li><a href="GHID_INSTALARE_FFMPEG_CPANEL.md">Ghid Instalare FFmpeg cPanel</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

