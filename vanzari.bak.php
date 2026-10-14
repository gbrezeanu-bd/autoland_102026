<?php
// get config file;
include_once './includes/config.php';

//$page_title='Auto dupa provenienţă';

include './themes/automotive/template_parts/header.php';
$footer_scripts .= '';


// Mizerierie START Here


$colname_selector_marca = "-1";
if (isset($_GET['marcat'])&&!empty($_GET['marcat'])) {
    $colname_selector_marca = (get_magic_quotes_gpc()) ? $_GET['marcat'] : addslashes($_GET['marcat']);
}

$query_selector_marca = sprintf("SELECT * FROM `search` WHERE id = %s", $colname_selector_marca);
$selector_marca = $connection->query($query_selector_marca) or die($connection->error);
$row_selector_marca = mysqli_fetch_assoc($selector_marca);
$totalRows_selector_marca = mysqli_num_rows($selector_marca);

if (isset($_GET['marcat'])) {
    $categorie = trim($row_selector_marca['categorie']);
    if ($row_selector_marca['marca']{0} == '&') {
        $marca_auto = substr($row_selector_marca['marca'], 18);
    } else {
        $marca_auto = '%';
    }
}

$query_marci = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$marci = $connection->query($query_marci) or die($connection->error);
$row_marci = mysqli_fetch_assoc($marci);
$totalRows_marci = mysqli_num_rows($marci);

$colname_selector_subcateg = "-1";
if (isset($_GET['subcateg']) && !empty($_GET['subcateg'])) {
    $colname_selector_subcateg = (get_magic_quotes_gpc()) ? $_GET['subcateg'] : addslashes($_GET['subcateg']);
}
$query_selector_subcateg = sprintf("SELECT * FROM `search_subcateg` WHERE id = %s", $colname_selector_subcateg);
$selector_subcateg = $connection->query($query_selector_subcateg) or die($connection->error);
$row_selector_subcateg = mysqli_fetch_assoc($selector_subcateg);
$totalRows_selector_subcateg = mysqli_num_rows($selector_subcateg);

if (isset($_GET['subcateg']) && !empty($_GET['subcateg'])) {
    $categorie = trim($row_selector_subcateg['categorie']);
    if ($row_selector_subcateg['subcateg']{0} == '&') {
        $subcateg_auto = substr($row_selector_subcateg['subcateg'], 18);
    } else {
        $subcateg_auto = '%';
    }
} else {
    $subcateg_auto = '%';
}

$query_subcateg = "SELECT * FROM `search_subcateg` ORDER BY categorie, subcateg ASC";
$subcateg = $connection->query($query_subcateg) or die($connection->error);
$row_subcateg = mysqli_fetch_assoc($subcateg);
$totalRows_subcateg = mysqli_num_rows($marci);

//
//$maxRows_vanzare = 10;
//$pageNum_vanzare = (isset($_GET['pageNum_vanzare'])) ? $_GET['pageNum_vanzare'] : 0;
//$startRow_vanzare = $pageNum_vanzare * $maxRows_vanzare;


$col_cat_vanzare = "%";
if (isset($categorie)) {
    $col_cat_vanzare = (get_magic_quotes_gpc()) ? $categorie : addslashes($categorie);
}

if(!isset($categorie)){
    $categorie='';
}

// we need distinct values of 'an' from 'masini' table
$query_an = "SELECT DISTINCT(an) FROM `masini` ORDER BY an ASC";
$query_an_result = $connection->query($query_an) or die($connection->error);
$query_an_result_arr=array();

while ($row_an = mysqli_fetch_assoc($query_an_result)) {
    // create an array of years from the database
    // do it here because we need it and to avoid another query
    $query_an_result_arr[]=$row_an;
}

$col_an_vanzare = $query_an_result_arr[0]['an'];

if (isset($_GET['an'])) {
    $col_an_vanzare = (get_magic_quotes_gpc()) ? $_GET['an'] : addslashes($_GET['an']);
} else {
    $_GET['an']='';
}
$col_pret_vanzare = "14000000";
if (isset($_GET['pret'])) {
    $col_pret_vanzare = (get_magic_quotes_gpc()) ? $_GET['pret'] : addslashes($_GET['pret']);
}
$col_marca_vanzare = "%";
if (isset($marca_auto)) {
    $col_marca_vanzare = (get_magic_quotes_gpc()) ? $marca_auto : addslashes($marca_auto);
}
$col_subcateg_vanzare = "%";
if (isset($subcateg_auto)) {
    $col_subcateg_vanzare = (get_magic_quotes_gpc()) ? $subcateg_auto : addslashes($subcateg_auto);
}
$col_combustibil_vanzare = "%";
if (isset($_GET['combustibil'])) {
    $col_combustibil_vanzare = (get_magic_quotes_gpc()) ? $_GET['combustibil'] : addslashes($_GET['combustibil']);
}
$col_prov_enienta_vanzare = "%";
if (isset($_GET['prov_enienta'])) {
    $col_prov_enienta_vanzare = (get_magic_quotes_gpc()) ? $_GET['prov_enienta'] : addslashes($_GET['prov_enienta']);
}

if (isset($_GET['txtsrc']) && (!empty($_GET['txtsrc']))) {
    $_GET['txtsrc']= mysqli_real_escape_string($connection, $_GET['txtsrc']);
    if (!strcmp("#S10", substr($_GET['txtsrc'], 0, 4))) {
        $textt = substr($_GET['txtsrc'], 4, 10);
        $query_vanzare = 'SELECT * FROM masini WHERE stare_anunt LIKE 1 AND id LIKE ' . $textt . ' ORDER BY pret';
    } else {
        $textt = $_GET['txtsrc'];
        $words = explode(' ', $textt);
        $words = array_slice($words, 0, 2);
        $fields = array('subcateg', 'marca', 'tip');
        $whereText = array();
        foreach ($words as $word) {
            $whereSubText = array();
            foreach ($fields as $field) {
                $whereSubText[] = ' (' . $field . ' LIKE "%' . $word . '%") ';
            }
            $whereText[] = '(' . join(' OR ', $whereSubText) . ')';
        }
        $whereText = '(' . join(' AND ', $whereText) . ')';
        if (!strcmp("fara", $col_prov_enienta_vanzare)) {
            $whereprov_enienta = "";
        } else {
            $whereprov_enienta = $col_prov_enienta_vanzare == '%' ? '' : ' (prov_enienta=\'' . $col_prov_enienta_vanzare . '\') AND ';
        }
        $query_vanzare = 'SELECT * FROM masini WHERE stare_anunt LIKE 1 AND ' . $whereprov_enienta . ' ' . $whereText . ' ORDER BY pret';
    }
} else {
    if ((!strcmp("fara", $col_combustibil_vanzare)) && (!strcmp("fara", $col_prov_enienta_vanzare))) {
        $query_vanzare = sprintf("SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=%s AND pret<=%s AND cat LIKE '%s' AND marca LIKE '%s' AND subcateg LIKE '%s' ORDER BY pret ASC", $col_an_vanzare, $col_pret_vanzare, $col_cat_vanzare, $col_marca_vanzare, $col_subcateg_vanzare);
    } elseif (!strcmp("fara", $col_combustibil_vanzare)) {
        $query_vanzare = sprintf("SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=%s AND pret<=%s AND cat LIKE '%s' AND marca LIKE '%s' AND prov_enienta LIKE '%s' AND subcateg LIKE '%s' ORDER BY pret ASC", $col_an_vanzare, $col_pret_vanzare, $col_cat_vanzare, $col_marca_vanzare, $col_prov_enienta_vanzare, $col_subcateg_vanzare);
    } elseif (!strcmp("fara", $col_prov_enienta_vanzare)) {
        $query_vanzare = sprintf("SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=%s AND pret<=%s AND combustibil LIKE '%s' AND cat LIKE '%s' AND marca LIKE '%s' AND subcateg LIKE '%s' ORDER BY pret ASC", $col_an_vanzare, $col_pret_vanzare, $col_combustibil_vanzare, $col_cat_vanzare, $col_marca_vanzare, $col_subcateg_vanzare);
    } else {
        $query_vanzare = sprintf("SELECT * FROM masini WHERE stare_anunt LIKE 1 AND an>=%s AND pret<=%s AND combustibil LIKE '%s' AND cat LIKE '%s' AND marca LIKE '%s' AND prov_enienta LIKE '%s' AND subcateg LIKE '%s' ORDER BY pret ASC", $col_an_vanzare, $col_pret_vanzare, $col_combustibil_vanzare, $col_cat_vanzare, $col_marca_vanzare, $col_prov_enienta_vanzare, $col_subcateg_vanzare);
    }
    $_GET['txtsrc']='';// set it, because we need it later on
}
// Mizerie END Here
//var_dump($query_vanzare);exit;
// paginate query
//$pager = paginateQuery($connection, $query_vanzare, 1, 'pagination', 2, FALSE, "vanzari.php?txtsrc=&users=Autoturisme&subcateg=3&marcat=10");
$pager = paginateQuery($connection, $query_vanzare, 10, 'pagination', 3, FALSE, 'vanzari.php');
//var_dump($query_vanzare);
$totalRows_vanzare=$pager['pager_resultset']->num_rows;
//echo 'Numar total pagini '. $totalRows_vanzare;
//var_dump($totalRows_vanzare);exit;
?>

<section class="content">
    <div class="container">
        <div class="inner-page row">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                require_once './themes/automotive/template_parts/sidebar_left.php';
                ?>
            </div>
            <!-- Sidebar END Here -->


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp">
                <?php
                // get car listing (all or just a part)
                require_once './themes/automotive/template_parts/inventory_listing.php';
                ?>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
