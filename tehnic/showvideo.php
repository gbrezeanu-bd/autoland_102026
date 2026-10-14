<?php
require_once('../includes/config.php');

// Verifică dacă $row_vehicule['id'] este setat
if (!isset($row_vehicule['id'])) {
    echo '<p>ID vehicul neidentificat.</p>';
    return;
}

$query_assoc = sprintf("SELECT * FROM fotov_video WHERE oferta = %s ORDER BY nota ASC", $row_vehicule['id']);
$assoc = $connection->query($query_assoc) or die($connection->error);

if ($assoc->num_rows == 0) {
    echo '<p style="color: #999; font-style: italic;">Nu există videouri asociate.</p>';
    return;
}

$row_assoc = $assoc->fetch_assoc();
$totalRows_assoc = $assoc->num_rows;

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

// Funcție helper pentru a obține calea corectă către thumbnail
function getThumbnailPath($thumbFile, $basePath, $projectRoot) {
    if (!empty($thumbFile)) {
        $thumbPhysicalPath = $projectRoot . '/' . $thumbFile;
        if (file_exists($thumbPhysicalPath)) {
            return $basePath . $thumbFile;
        }
    }
    
    // Placeholder fallback
    $placeholderPath = $projectRoot . '/themes/automotive/images/video_placeholder.jpg';
    if (file_exists($placeholderPath)) {
        return $basePath . 'themes/automotive/images/video_placeholder.jpg';
    }
    
    // SVG placeholder inline dacă nu există niciun placeholder
    return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="320" height="240"><rect width="320" height="240" fill="#282828"/><circle cx="160" cy="120" r="40" fill="#c8c8c8"/><path d="M150 100 L150 140 L180 120 Z" fill="#fff"/></svg>');
}

// Funcție helper pentru a verifica dacă video-ul există
function getVideoPath($videoFile, $basePath, $projectRoot) {
    $videoPhysicalPath = $projectRoot . '/video/' . $videoFile;
    if (!file_exists($videoPhysicalPath)) {
        error_log("Video file not found: " . $videoPhysicalPath);
    }
    return $basePath . 'video/' . $videoFile;
}

// Primul video
echo '<div id="video_' . $row_assoc['id'] . '" name="video_' . $row_assoc['id'] . '" style="display: block; float: left; margin: 5px; padding: 5px; border: 1px solid #ddd; background: #f9f9f9;">';
echo '  <center>
			<input type="submit" name="nota_video_' . $row_assoc['id'] . '_minus" value="-" onclick="saveScrollPosition();">' . $row_assoc['nota'] . '<input type="submit" name="nota_video_' . $row_assoc['id'] . '_plus" value="+" onclick="saveScrollPosition();">
		</center>
		';

$thumbPath = getThumbnailPath($row_assoc['thumb'], BASE_PATH, $projectRoot);
$videoPath = getVideoPath($row_assoc['file'], BASE_PATH, $projectRoot);

echo '<a href="' . htmlspecialchars($videoPath) . '" target="_blank" title="Vezi video: ' . htmlspecialchars($row_assoc['file']) . '">';
echo '    <img data-no-retina src="' . htmlspecialchars($thumbPath) . '" width="120px" height="90px" style="border: 1px solid #ccc; display: block; margin: 5px auto;" alt="Video thumbnail" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMjAiIGhlaWdodD0iMjQwIj48cmVjdCB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgZmlsbD0iIzI4MjgyOCIvPjxjaXJjbGUgY3g9IjE2MCIgY3k9IjEyMCIgcj0iNDAiIGZpbGw9IiNjOGM4YzgiLz48cGF0aCBkPSJNMTUwIDEwMCBMMTUwIDE0MCBMMTgwIDEyMCBaIiBmaWxsPSIjZmZmIi8+PC9zdmc+\';" />';
echo '    <div style="position: relative; margin-top: -75px; text-align: center;">';
echo '        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">';
echo '            <circle cx="20" cy="20" r="20" fill="rgba(0,0,0,0.6)"/>';
echo '            <path d="M16 12L16 28L28 20L16 12Z" fill="white"/>';
echo '        </svg>';
echo '    </div>';
echo '</a>';

echo '<div style="font-size: 10px; color: #666; text-align: center; margin-top: 5px; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . htmlspecialchars($row_assoc['file']) . '</div>';
echo '<br /><center><a href="javascript:delVideo(\'' . $row_assoc['id'] . '\')" style="color: #c00;">Delete</a></center>';
echo '</div>';

// Restul videourilor
while ($row_assoc = $assoc->fetch_assoc()) {
    echo '<div id="video_' . $row_assoc['id'] . '" name="video_' . $row_assoc['id'] . '" style="display: block; float: left; margin: 5px; padding: 5px; border: 1px solid #ddd; background: #f9f9f9;">';
    echo '	<center>
			<input type="submit" name="nota_video_' . $row_assoc['id'] . '_minus" value="-" onclick="saveScrollPosition();">' . $row_assoc['nota'] . '<input type="submit" name="nota_video_' . $row_assoc['id'] . '_plus" value="+" onclick="saveScrollPosition();">
		</center>
		';
    
    $thumbPath = getThumbnailPath($row_assoc['thumb'], BASE_PATH, $projectRoot);
    $videoPath = getVideoPath($row_assoc['file'], BASE_PATH, $projectRoot);
    
    echo '<a href="' . htmlspecialchars($videoPath) . '" target="_blank" title="Vezi video: ' . htmlspecialchars($row_assoc['file']) . '">';
    echo '        <img data-no-retina src="' . htmlspecialchars($thumbPath) . '" width="120px" height="90px" style="border: 1px solid #ccc; display: block; margin: 5px auto;" alt="Video thumbnail" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMjAiIGhlaWdodD0iMjQwIj48cmVjdCB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgZmlsbD0iIzI4MjgyOCIvPjxjaXJjbGUgY3g9IjE2MCIgY3k9IjEyMCIgcj0iNDAiIGZpbGw9IiNjOGM4YzgiLz48cGF0aCBkPSJNMTUwIDEwMCBMMTUwIDE0MCBMMTgwIDEyMCBaIiBmaWxsPSIjZmZmIi8+PC9zdmc+\';" />';
    echo '        <div style="position: relative; margin-top: -75px; text-align: center;">';
    echo '            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">';
    echo '                <circle cx="20" cy="20" r="20" fill="rgba(0,0,0,0.6)"/>';
    echo '                <path d="M16 12L16 28L28 20L16 12Z" fill="white"/>';
    echo '            </svg>';
    echo '        </div>';
    echo '</a>';
    
    echo '<div style="font-size: 10px; color: #666; text-align: center; margin-top: 5px; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . htmlspecialchars($row_assoc['file']) . '</div>';
    echo '<br /><center><a href="javascript:delVideo(\'' . $row_assoc['id'] . '\')" style="color: #c00;">Delete</a></center><br />';
    echo '</div>';
}

mysqli_free_result($assoc);
?>
