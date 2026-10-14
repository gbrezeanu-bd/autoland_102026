<?php
/**
 * Script de test pentru verificarea FFmpeg local (Windows și Linux)
 * Accesează: http://localhost/tehnic/test_ffmpeg.php
 */

// Detectează sistemul de operare
$isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
$ffmpegExecutable = $isWindows ? 'ffmpeg.exe' : 'ffmpeg';

echo "<h1>Test FFmpeg Local</h1>";
echo "<p><strong>Sistem de operare:</strong> " . PHP_OS . ($isWindows ? " (Windows)" : " (Linux/Unix)") . "</p>";
echo "<hr>";

echo "<h2>1. Verificare FFmpeg...</h2>";

$ffmpegFound = false;
$ffmpegPath = 'ffmpeg';
$ffmpegVersion = '';

// Verifică dacă există variabila de mediu ffmpeg
$ffmpegEnvPath = getenv('ffmpeg');
if ($ffmpegEnvPath) {
    echo "<p><strong>Info:</strong> Variabila de mediu <code>ffmpeg</code> este setată la: <code>" . htmlspecialchars($ffmpegEnvPath) . "</code></p>";
    
    // Verifică dacă fișierul există (folosește executabilul corect pentru OS)
    $ffmpegExe = $ffmpegEnvPath . DIRECTORY_SEPARATOR . $ffmpegExecutable;
    if (file_exists($ffmpegExe)) {
        echo "<p style='color: green;'>✓ Fișierul <code>" . htmlspecialchars($ffmpegExecutable) . "</code> există la calea specificată!</p>";
    } else {
        echo "<p style='color: red;'>✗ Fișierul <code>" . htmlspecialchars($ffmpegExecutable) . "</code> NU există la: <code>" . htmlspecialchars($ffmpegExe) . "</code></p>";
    }
}

// Testează dacă ffmpeg este disponibil în PATH
$output = [];
$return_var = 0;
@exec("ffmpeg -version 2>&1", $output, $return_var);

if ($return_var === 0 && !empty($output)) {
    echo "<p>Testez: <code>ffmpeg</code> (din PATH) ... <strong style='color: green;'>✓ GĂSIT!</strong></p>";
    $ffmpegFound = true;
    $ffmpegVersion = $output[0];
    $ffmpegPath = 'ffmpeg';
} else {
    echo "<p>Testez: <code>ffmpeg</code> (din PATH) ... <span style='color: red;'>✗ Nu găsit</span></p>";
    
    // Încearcă cu calea din variabila de mediu ffmpeg
    if ($ffmpegEnvPath) {
        $testPath = $ffmpegEnvPath . DIRECTORY_SEPARATOR . $ffmpegExecutable;
        if (file_exists($testPath)) {
            echo "<p>Testez: <code>" . htmlspecialchars($testPath) . "</code> ... ";
            $output2 = [];
            $return_var2 = 0;
            // Pe Windows folosim ghilimele, pe Linux nu
            $command = $isWindows ? "\"$testPath\"" : escapeshellarg($testPath);
            @exec("$command -version 2>&1", $output2, $return_var2);
            
            if ($return_var2 === 0 && !empty($output2)) {
                echo "<strong style='color: green;'>✓ GĂSIT ȘI FUNCȚIONEAZĂ!</strong></p>";
                $ffmpegFound = true;
                $ffmpegVersion = $output2[0];
                $ffmpegPath = $testPath;
            } else {
                echo "<span style='color: red;'>✗ Nu funcționează (eroare la execuție)</span></p>";
                if (!empty($output2)) {
                    echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 11px;'>" . htmlspecialchars(implode("\n", $output2)) . "</pre>";
                }
            }
        } else {
            echo "<p style='color: red;'>✗ Fișierul nu există la: <code>" . htmlspecialchars($testPath) . "</code></p>";
        }
    }
    
    if (!$ffmpegFound) {
        echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>⚠ FFmpeg nu este în PATH</h3>";
        if ($ffmpegEnvPath) {
            echo "<p><strong>Bună veste:</strong> Scriptul va folosi variabila de mediu <code>ffmpeg</code> ca fallback!</p>";
            echo "<p>Variabila <code>ffmpeg</code> este setată la: <code>" . htmlspecialchars($ffmpegEnvPath) . "</code></p>";
            echo "<p>Dacă fișierul există acolo, sistemul va funcționa chiar dacă nu este în PATH.</p>";
        } else {
            if ($isWindows) {
                echo "<p>Pentru funcționare optimă pe <strong>Windows</strong>, adaugă calea în variabila <code>Path</code>:</p>";
                echo "<ol>";
                echo "<li>Click dreapta pe 'Acest computer' → Proprietăți → Setări avansate → Variabile de mediu</li>";
                echo "<li>Găsește variabila <code>Path</code> în 'System variables'</li>";
                echo "<li>Click <code>Path</code> → <strong>Edit...</strong></li>";
                echo "<li>Click <strong>New</strong> și adaugă: <code>C:\\ffmpeg\\bin</code> (sau calea ta)</li>";
                echo "<li>Click <strong>OK</strong> și restartează Apache</li>";
                echo "</ol>";
            } else {
                echo "<p>Pentru funcționare optimă pe <strong>Linux</strong>, adaugă calea în PATH:</p>";
                echo "<ol>";
                echo "<li><strong>Instalează FFmpeg:</strong><br>";
                echo "<code>sudo apt-get update && sudo apt-get install ffmpeg</code> (Debian/Ubuntu)<br>";
                echo "sau<br>";
                echo "<code>sudo yum install ffmpeg</code> (CentOS/RHEL)<br>";
                echo "sau<br>";
                echo "<code>sudo dnf install ffmpeg</code> (Fedora)</li>";
                echo "<li><strong>Verifică instalarea:</strong><br>";
                echo "<code>which ffmpeg</code> sau <code>ffmpeg -version</code></li>";
                echo "<li><strong>Dacă ai instalat manual:</strong> Adaugă în <code>~/.bashrc</code> sau <code>/etc/environment</code>:<br>";
                echo "<code>export PATH=\$PATH:/usr/local/ffmpeg/bin</code> (sau calea ta)</li>";
                echo "<li><strong>Restartează Apache:</strong><br>";
                echo "<code>sudo systemctl restart apache2</code> (sau <code>httpd</code> pe CentOS)</li>";
                echo "</ol>";
            }
        }
        echo "</div>";
    }
}

echo "<hr>";

if ($ffmpegFound) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h2 style='color: green; margin-top: 0;'>✓ FFmpeg este instalat și funcțional!</h2>";
    echo "<p><strong>Cale:</strong> <code>" . htmlspecialchars($ffmpegPath) . "</code></p>";
    echo "<p><strong>Versiune:</strong> <code>" . htmlspecialchars($ffmpegVersion) . "</code></p>";
    echo "</div>";
    
    // Test generare thumbnail
    echo "<hr>";
    echo "<h2>2. Test Generare Thumbnail</h2>";
    
    // Verifică dacă există un video de test (folosim calea relativă la proiect)
    $projectRoot = dirname(__DIR__);
    $testVideo = $projectRoot . "/video/test.mp4";
    if (file_exists($testVideo)) {
        $testThumb = $projectRoot . "/video/thumbs/test_thumb.jpg";
        // Construiește comanda corectă pentru ambele sisteme
        $ffmpegCmd = $ffmpegPath;
        if ($isWindows && $ffmpegPath !== 'ffmpeg' && strpos($ffmpegPath, ' ') !== false) {
            // Pe Windows, dacă calea conține spații, folosește ghilimele
            $ffmpegCmd = "\"$ffmpegPath\"";
        }
        
        $command = $ffmpegCmd . " -i " . escapeshellarg($testVideo) . 
                   " -ss 00:00:01 -vframes 1 -q:v 2 " . escapeshellarg($testThumb) . " 2>&1";
        
        $output = [];
        $return_var = 0;
        @exec($command, $output, $return_var);
        
        if (file_exists($testThumb) && filesize($testThumb) > 0) {
            echo "<p style='color: green;'>✓ Thumbnail generat cu succes!</p>";
            echo "<p><img src='/video/thumbs/test_thumb.jpg' style='max-width: 320px; border: 1px solid #ddd;'></p>";
        } else {
            echo "<p style='color: orange;'>⚠ Thumbnail-ul nu a putut fi generat.</p>";
            if (!empty($output)) {
                echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
            }
        }
    } else {
        echo "<p style='color: #666;'>ℹ Nu există video de test. Încarcă un video prin interfața de administrare pentru a testa generarea thumbnail-ului.</p>";
    }
    
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h2 style='color: red; margin-top: 0;'>✗ FFmpeg NU a fost găsit</h2>";
    echo "<h3>Pași pentru rezolvare:</h3>";
    
    if ($isWindows) {
        echo "<ol>";
        echo "<li><strong>Verifică că ai extras FFmpeg</strong><br>";
        echo "Caută manual: <code>C:\\xampp\\ffmpeg\\bin\\ffmpeg.exe</code> sau <code>C:\\ffmpeg\\bin\\ffmpeg.exe</code></li>";
        echo "<li><strong>Adaugă în PATH</strong><br>";
        echo "Click dreapta pe 'Acest computer' → Proprietăți → Setări avansate → Variabile de mediu<br>";
        echo "Adaugă în Path: <code>C:\\xampp\\ffmpeg\\bin</code> (sau calea ta)</li>";
        echo "<li><strong>Restartează</strong><br>";
        echo "Restartează Command Prompt, XAMPP (Apache) și reîncarcă această pagină</li>";
        echo "<li><strong>Sau folosește calea completă</strong><br>";
        echo "Editează <code>tehnic/upload_video2.php</code> și adaugă calea exactă</li>";
        echo "</ol>";
    } else {
        echo "<ol>";
        echo "<li><strong>Instalează FFmpeg prin package manager:</strong><br>";
        echo "<strong>Debian/Ubuntu:</strong><br>";
        echo "<code>sudo apt-get update</code><br>";
        echo "<code>sudo apt-get install ffmpeg</code><br><br>";
        echo "<strong>CentOS/RHEL:</strong><br>";
        echo "<code>sudo yum install epel-release</code><br>";
        echo "<code>sudo yum install ffmpeg</code><br><br>";
        echo "<strong>Fedora:</strong><br>";
        echo "<code>sudo dnf install ffmpeg</code></li>";
        echo "<li><strong>Verifică instalarea:</strong><br>";
        echo "<code>which ffmpeg</code><br>";
        echo "<code>ffmpeg -version</code></li>";
        echo "<li><strong>Dacă ai instalat manual:</strong><br>";
        echo "Asigură-te că <code>" . htmlspecialchars($ffmpegExecutable) . "</code> este executabil:<br>";
        echo "<code>chmod +x /path/to/ffmpeg</code><br>";
        echo "Și adaugă în PATH în <code>~/.bashrc</code> sau <code>/etc/environment</code></li>";
        echo "<li><strong>Restartează Apache:</strong><br>";
        echo "<code>sudo systemctl restart apache2</code> (Debian/Ubuntu)<br>";
        echo "sau<br>";
        echo "<code>sudo systemctl restart httpd</code> (CentOS/RHEL)</li>";
        echo "</ol>";
    }
    echo "</div>";
}

echo "<hr>";
echo "<h2>3. Verificare PHP exec()</h2>";

if (function_exists('exec')) {
    echo "<p style='color: green;'>✓ Funcția exec() este disponibilă în PHP</p>";
} else {
    echo "<p style='color: red;'>✗ Funcția exec() NU este disponibilă</p>";
    echo "<p>Editează <code>php.ini</code> și asigură-te că <code>exec</code> nu este în <code>disable_functions</code></p>";
}

echo "<hr>";
echo "<h2>4. Informații Sistem</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>PHP_OS:</strong> " . PHP_OS . "</p>";
echo "<p><strong>php_uname():</strong> " . php_uname() . "</p>";

// Detectare detaliată pentru Linux
if (!$isWindows) {
    echo "<h3>Detalii Linux:</h3>";
    
    // Detectare distributie din /etc/os-release
    if (file_exists('/etc/os-release')) {
        $osInfo = @parse_ini_file('/etc/os-release');
        if ($osInfo) {
            echo "<p><strong>Distribuție:</strong> " . ($osInfo['NAME'] ?? 'N/A') . "</p>";
            echo "<p><strong>Versiune:</strong> " . ($osInfo['VERSION'] ?? 'N/A') . "</p>";
            echo "<p><strong>ID:</strong> " . ($osInfo['ID'] ?? 'N/A') . "</p>";
        }
    }
    
    // Detectare CentOS/RHEL/CloudLinux
    if (file_exists('/etc/redhat-release')) {
        $redhatInfo = @file_get_contents('/etc/redhat-release');
        if ($redhatInfo) {
            echo "<p><strong>RedHat/CentOS/CloudLinux Release:</strong> " . htmlspecialchars(trim($redhatInfo)) . "</p>";
        }
    }
    
    // Detectare CloudLinux specific
    if (file_exists('/etc/cloudlinux-release')) {
        $cloudlinuxInfo = @file_get_contents('/etc/cloudlinux-release');
        if ($cloudlinuxInfo) {
            echo "<p><strong>CloudLinux Release:</strong> " . htmlspecialchars(trim($cloudlinuxInfo)) . "</p>";
            echo "<div style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; border-radius: 4px;'>";
            echo "<p><strong>⚠️ CloudLinux detectat:</strong> Verifică mai întâi dacă FFmpeg este deja instalat cu <code>which ffmpeg</code>. Dacă nu ai acces sudo, contactează suportul hosting.</p>";
            echo "</div>";
        }
    }
    
    // Verifică locații comune pentru FFmpeg
    echo "<h3>Locații comune FFmpeg:</h3>";
    $commonPaths = [
        '/usr/bin/ffmpeg',
        '/usr/local/bin/ffmpeg',
        '/opt/ffmpeg/bin/ffmpeg',
        '/usr/local/ffmpeg/bin/ffmpeg',
    ];
    
    // Încearcă mai multe metode de a găsi home directory
    $homeDir = null;
    
    // Metoda 1: Variabila de mediu HOME
    $homeDir = getenv('HOME');
    
    // Metoda 2: $_SERVER['HOME']
    if (!$homeDir && isset($_SERVER['HOME'])) {
        $homeDir = $_SERVER['HOME'];
    }
    
    // Metoda 3: Din DOCUMENT_ROOT (pentru shared hosting)
    // De obicei: /home/username/public_html -> /home/username
    if (!$homeDir && isset($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        // Dacă document root conține /home/username/public_html, extrage /home/username
        if (preg_match('#^/home/([^/]+)/public_html#', $docRoot, $matches)) {
            $homeDir = '/home/' . $matches[1];
        } elseif (preg_match('#^/home/([^/]+)#', $docRoot, $matches)) {
            // Dacă e direct /home/username, folosește-l
            $homeDir = '/home/' . $matches[1];
        }
    }
    
    // Metoda 4: posix_getpwuid (dacă e disponibil)
    if (!$homeDir && function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
        $userInfo = @posix_getpwuid(posix_geteuid());
        if ($userInfo && isset($userInfo['dir'])) {
            $homeDir = $userInfo['dir'];
        }
    }
    
    // Metoda 5: Din username-ul din cale (ultimă încercare)
    if (!$homeDir && isset($_SERVER['DOCUMENT_ROOT'])) {
        $parts = explode('/', trim($_SERVER['DOCUMENT_ROOT'], '/'));
        // Caută /home/username în cale
        $homeIndex = array_search('home', $parts);
        if ($homeIndex !== false && isset($parts[$homeIndex + 1])) {
            $homeDir = '/home/' . $parts[$homeIndex + 1];
        }
    }
    
    if ($homeDir) {
        $commonPaths[] = $homeDir . '/bin/ffmpeg';
        $commonPaths[] = $homeDir . '/ffmpeg/bin/ffmpeg';
        
        // Afișează home directory detectat
        echo "<div style='background: #e7f3ff; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0; border-radius: 4px;'>";
        echo "<p><strong>📁 Home Directory detectat:</strong> <code>" . htmlspecialchars($homeDir) . "</code></p>";
        echo "<p><strong>Cale așteptată pentru FFmpeg:</strong> <code>" . htmlspecialchars($homeDir . '/bin/ffmpeg') . "</code></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; border-radius: 4px;'>";
        echo "<p><strong>⚠️ Nu s-a putut detecta home directory automat.</strong></p>";
        echo "<p><strong>Document Root:</strong> <code>" . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</code></p>";
        echo "<p>Verifică manual că ai uploadat <code>ffmpeg</code> în <code>/home/username/bin/</code> (înlocuiește <code>username</code> cu username-ul tău de cPanel).</p>";
        echo "</div>";
    }
    
    echo "<ul>";
    foreach ($commonPaths as $path) {
        $exists = file_exists($path);
        $executable = $exists && is_executable($path);
        $color = $exists ? ($executable ? 'green' : 'orange') : 'red';
        $icon = $exists ? ($executable ? '✓' : '⚠') : '✗';
        echo "<li style='color: $color;'>$icon <code>" . htmlspecialchars($path) . "</code>";
        if ($exists) {
            $status = $executable ? "Există și este executabil" : "Există dar NU este executabil";
            // Verifică permisiuni
            $perms = fileperms($path);
            $permsOct = substr(sprintf('%o', $perms), -4);
            $status .= " (permisiuni: $permsOct)";
            echo " - " . $status;
            
            // Dacă există și este executabil, testează-l
            if ($executable) {
                $testOutput = [];
                $testReturn = 0;
                @exec(escapeshellarg($path) . ' -version 2>&1', $testOutput, $testReturn);
                if ($testReturn === 0 && !empty($testOutput)) {
                    echo " <strong style='color: green;'>✓ FUNCȚIONEAZĂ!</strong>";
                    if (!$ffmpegFound) {
                        $ffmpegFound = true;
                        $ffmpegPath = $path;
                        $ffmpegVersion = $testOutput[0];
                    }
                } else {
                    echo " <span style='color: red;'>✗ Nu funcționează la execuție</span>";
                }
            }
        } else {
            echo " - Nu există";
        }
        echo "</li>";
    }
    echo "</ul>";
    
    // Testează explicit ~/bin/ffmpeg dacă există
    // Dacă nu am detectat homeDir, încercăm să-l construim din DOCUMENT_ROOT
    if (!$homeDir && isset($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        // Pentru /home/username/public_html -> /home/username
        if (preg_match('#^/home/([^/]+)/public_html#', $docRoot, $matches)) {
            $homeDir = '/home/' . $matches[1];
        }
    }
    
    if ($homeDir) {
        $homeBinPath = $homeDir . '/bin/ffmpeg';
        if (file_exists($homeBinPath)) {
            echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; border-radius: 4px;'>";
            echo "<h3>🔍 Test Explicit: ~/bin/ffmpeg</h3>";
            echo "<p><strong>Cale completă:</strong> <code>" . htmlspecialchars($homeBinPath) . "</code></p>";
            
            $exists = file_exists($homeBinPath);
            $executable = $exists && is_executable($homeBinPath);
            $perms = $exists ? fileperms($homeBinPath) : null;
            $permsOct = $perms ? substr(sprintf('%o', $perms), -4) : 'N/A';
            
            echo "<p><strong>Există:</strong> " . ($exists ? "✓ Da" : "✗ Nu") . "</p>";
            echo "<p><strong>Executabil:</strong> " . ($executable ? "✓ Da" : "✗ Nu") . "</p>";
            echo "<p><strong>Permisiuni:</strong> <code>$permsOct</code> ";
            if ($permsOct === '0755' || $permsOct === '755') {
                echo "<span style='color: green;'>✓ Corect (755)</span>";
            } else {
                echo "<span style='color: red;'>✗ Greșit! Trebuie 755</span>";
            }
            echo "</p>";
            
            if ($executable) {
                echo "<p><strong>Testare execuție:</strong> ";
                $testOutput = [];
                $testReturn = 0;
                @exec(escapeshellarg($homeBinPath) . ' -version 2>&1', $testOutput, $testReturn);
                if ($testReturn === 0 && !empty($testOutput)) {
                    echo "<span style='color: green;'>✓ FUNCȚIONEAZĂ!</span></p>";
                    echo "<p><strong>Versiune:</strong> <code>" . htmlspecialchars($testOutput[0]) . "</code></p>";
                    if (!$ffmpegFound) {
                        $ffmpegFound = true;
                        $ffmpegPath = $homeBinPath;
                        $ffmpegVersion = $testOutput[0];
                    }
                } else {
                    echo "<span style='color: red;'>✗ Eroare la execuție</span></p>";
                    if (!empty($testOutput)) {
                        echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 11px; max-height: 200px; overflow: auto;'>" . htmlspecialchars(implode("\n", $testOutput)) . "</pre>";
                    }
                }
            } else {
                echo "<p style='color: orange;'><strong>⚠️ Fișierul nu este executabil!</strong> Setează permisiunile la 755.</p>";
            }
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; border-radius: 4px;'>";
            echo "<h3>⚠️ ~/bin/ffmpeg nu există</h3>";
            echo "<p><strong>Cale așteptată:</strong> <code>" . htmlspecialchars($homeBinPath) . "</code></p>";
            echo "<p>Verifică că ai uploadat fișierul <code>ffmpeg</code> în folder-ul <code>bin</code> din directorul home.</p>";
            echo "</div>";
        }
    }
    
    // Verifică which/whereis
    echo "<h3>Detectare automată:</h3>";
    $whichOutput = [];
    @exec('which ffmpeg 2>&1', $whichOutput);
    if (!empty($whichOutput) && !empty($whichOutput[0]) && strpos($whichOutput[0], 'not found') === false) {
        echo "<p style='color: green;'>✓ <code>which ffmpeg</code>: <code>" . htmlspecialchars($whichOutput[0]) . "</code></p>";
    } else {
        echo "<p style='color: red;'>✗ <code>which ffmpeg</code>: Nu găsit în PATH</p>";
    }
    
    $whereisOutput = [];
    @exec('whereis ffmpeg 2>&1', $whereisOutput);
    if (!empty($whereisOutput) && !empty($whereisOutput[0])) {
        echo "<p><code>whereis ffmpeg</code>: <code>" . htmlspecialchars($whereisOutput[0]) . "</code></p>";
    }
}

// Verifică variabile de mediu relevante
echo "<h3>Variabile de mediu:</h3>";
echo "<ul>";
$envVars = ['PATH', 'HOME', 'ffmpeg'];
foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value !== false) {
        echo "<li><strong>$var:</strong> <code>" . htmlspecialchars($value) . "</code></li>";
    } else {
        echo "<li><strong>$var:</strong> <span style='color: gray;'>Nu este setată</span></li>";
    }
}
echo "</ul>";

// Rezumat final
echo "<hr>";
echo "<h2>📊 Rezumat Final</h2>";
if ($ffmpegFound) {
    echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #28a745; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ FFmpeg GĂSIT ȘI FUNCȚIONAL!</h3>";
    echo "<p><strong>Cale:</strong> <code>" . htmlspecialchars($ffmpegPath) . "</code></p>";
    echo "<p><strong>Versiune:</strong> <code>" . htmlspecialchars($ffmpegVersion) . "</code></p>";
    echo "<p style='color: #155724;'><strong>✓ Sistemul de generare thumbnail-uri va funcționa corect!</strong></p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ FFmpeg NU este găsit sau nu funcționează</h3>";
    echo "<p><strong>Pași pentru rezolvare:</strong></p>";
    echo "<ol>";
    echo "<li>Verifică că ai uploadat <code>ffmpeg</code> în <code>~/bin/</code></li>";
    echo "<li>Verifică că permisiunile sunt <code>755</code> (executabil)</li>";
    echo "<li>Verifică că fișierul nu este corupt</li>";
    if ($homeDir) {
        echo "<li>Calea așteptată: <code>" . htmlspecialchars($homeDir . '/bin/ffmpeg') . "</code></li>";
    }
    echo "</ol>";
    echo "</div>";
}

?>


