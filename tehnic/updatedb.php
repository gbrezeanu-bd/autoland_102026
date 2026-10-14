<?php
require_once('../includes/config.php');

$sql = 'DESCRIBE masini';
$result = $connection->query($sql) or die($connection->error);
$subcateg_found = false;
while ($row = mysqli_fetch_assoc($result)) {
	if ( $row['Field'] == 'subcateg' ) {
		$subcateg_found = true;
		break;
	}
}
if ( !$subcateg_found ) {
	echo 'Field "subcateg@masini" not found. Creating.<br/>"';
	$sql = "alter table masini add column subcateg varchar(50)";
	$result = $connection->query($sql);
	if ( !$result ) {
		echo 'Field couldn\'t be created!<br/>';
		exit;
	}
	$sql = "update masini set subcateg=''";
	echo 'Field created. Updating current records with blank<br/>';
	$result = $connection->query($sql);
	if ( !$result ) {
		echo 'Records couldn\'t be updated!<br/>';
		exit;
	}
	echo 'Records updated ok!<br/>';
	echo 'Everything finished<br/>';	
} else {
	echo 'Field "subcateg@masini" already exists!<br/>';
}

echo 'Creating table subcateg<br/>';
$sql = "CREATE TABLE `subcateg` (".
	"  `id` bigint(20) unsigned NOT NULL auto_increment, " .
  	" `den` varchar(50) default NULL, " .
  	" PRIMARY KEY  (`id`) ".
	" ) ";
$result = $connection->query($sql);
if ( !$result ) {
	echo 'Table couldn\'t be created!<br/>';
	exit;
} else {
	echo 'Table created.<br/>';
}

echo 'Creating table search_subcateg<br/>';
$sql = " CREATE TABLE `search_subcateg` ( " .
  	" `id` int(10) unsigned NOT NULL auto_increment, " . 
	" `categorie` varchar(50) default NULL, " .
  	" `subcateg` varchar(50) default NULL, " .
  	" PRIMARY KEY  (`id`) " . 
	" ) ";
$result = $connection->query($sql);
if ( !$result ) {
	echo 'Table couldn\'t be created!<br/>';
	exit;
} else {
	echo 'Table created.<br/>';
}
echo 'Done';

?>