<?php
require_once('../includes/config.php');

$query_assoc = sprintf("SELECT * FROM fotov WHERE oferta = %s ORDER BY nota ASC", $row_vehicule['id']);
$assoc = $connection->query($query_assoc) or die($connection->error);
$row_assoc = mysqli_fetch_assoc($assoc);
$totalRows_assoc = mysqli_num_rows($assoc);
$nrfoto = $totalRows_assoc;
//$updateSQL 			= 	sprintf("UPDATE fotov SET nota=%s WHERE id=%s", $nota, $row_assoc['id']);
//mysql_query($updateSQL, $autoland) or die(mysql_error());


echo '<div id="foto_' . $row_assoc['id'] . '" name="foto_' . $row_assoc['id'] . '" style="display: block; float: left;">';
echo '  <center>
			<input type="submit" name="nota_' . $row_assoc['id'] . '_minus" value="-" onclick="saveScrollPosition();">' . $row_assoc['nota'] . '<input type="submit" name="nota_' . $row_assoc['id'] . '_plus" value="+" onclick="saveScrollPosition();">
		</center>
			';

//echo '<a href='.BASE_PATH.'foto/'. $row_assoc['file'] .'><img data-no-retina src= "thumb.php?src='. $row_assoc['file'] .'&wmax=60&hmax=45&quality=90&bgcol=FFFFff" hspace="5" vspace="2" border="1"></img></a>';
echo '<a href=' . BASE_PATH . 'foto/' . $row_assoc['file'] . '><img data-no-retina src= "' . BASE_PATH . 'foto/' . $row_assoc['file'] . '" width=60px height=45px hspace="5" vspace="2" border="1"></img></a>';
echo '<br /><center><a href="javascript:delImage(\'' . $row_assoc['id'] . '\')">Delete</a></center>';
echo '</div>';

while ($row_assoc = mysqli_fetch_assoc($assoc)) {
    
        echo '<div id="foto_' . $row_assoc['id'] . '" name="foto_' . $row_assoc['id'] . '" style="display: block; float: left;">';
        echo '	<center>
			<input type="submit" name="nota_' . $row_assoc['id'] . '_minus" value="-">' . $row_assoc['nota'] . '<input type="submit" name="nota_' . $row_assoc['id'] . '_plus" value="+">
		</center>
			';
        echo '<a href='.BASE_PATH.'foto/' . $row_assoc['file'] . '><img data-no-retina src= "'.BASE_PATH . 'foto/' . $row_assoc['file'] . '" width=60px height=45px space="5" vspace="2" border="1"></img></a>';
        echo '<br /><center><a href="javascript:delImage(\'' . $row_assoc['id'] . '\')">Delete</a></center><br />';
        echo '</div>';
    }

    mysqli_free_result($assoc);
    ?>