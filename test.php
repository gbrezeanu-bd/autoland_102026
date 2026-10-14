<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once './includes/config.php';


$query_vanzare="SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=2006 AND pret<=14000000 AND combustibil LIKE '%' AND cat LIKE 'Autoturisme' AND marca LIKE '%' AND prov_enienta LIKE '%' AND subcateg LIKE '%' ORDER BY pret ASC";

//var_dump($query_vanzare);

$pager = paginateQuery($connection, $query_vanzare, 20, 'pagination', 2);

var_dump($pager);

echo $pager['pager_html'];