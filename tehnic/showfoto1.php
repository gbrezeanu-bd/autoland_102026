<?php require_once('../Connections/autoland.php');
$query_assoc =  sprintf("SELECT * FROM fotor WHERE oferta = %s",$row_rent['id']);
$assoc = mysql_query($query_assoc, $autoland) or die(mysql_error());
$row_assoc = mysql_fetch_assoc($assoc);
$totalRows_assoc = mysql_num_rows($assoc);
$nrfoto=$totalRows_assoc;
do {
echo '<a href=../foto/'. $row_assoc['file'] .'><img data-no-retina src= "thumb.php?src=foto/'. $row_assoc['file'] .'&wmax=67&hmax=50&quality=90&bgcol=FFFFff" hspace="5" vspace="2" border="1"></a></img>';
} while ($row_assoc = mysql_fetch_assoc($assoc));
?>

<?php
mysql_free_result($assoc);
?>
