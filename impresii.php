<?php
// get config file;
include_once './includes/config.php';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT * FROM recenzii2 WHERE status = '1' ORDER BY `data_recenzie` DESC";

$result = $connection->query($query_text) or db_fail($connection);
$result_number=$result->num_rows;
//$row = mysqli_fetch_assoc($result);

$page_title = "Recenzii Clienți Autoland România | Iași";
$page_subtitle = '';
//$content = is_null($row) ? "<h3>Nu avem recezii</h3>" : '';

// Set SEO meta tags
require_once './includes/meta_tags.php';
setReviewsPageMeta();

// Add JSON-LD structured data for SEO
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Păreri Clienți', 'url' => BASE_PATH . 'impresii.php']
];
$header_scripts .= '<script type="application/ld+json">' . json_encode(getOrganizationSchema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
$header_scripts .= '<script type="application/ld+json">' . json_encode(getBreadcrumbSchema($breadcrumbs), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';


include './themes/automotive/template_parts/header.php';
$footer_scripts .= '';
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


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp information_head welcome-wrap">
                <div class="content-wrap car_listings">
                    <?php
                                    
                                    
                    if($result_number==0){
                        ?><h3>Nu avem recenzii</h3><?php
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                        <div class="testimonial margin-bottom-100 scroll_effect fadeInUp" data-wow-delay="0.5s">
                            <blockquote class="style1">
                                <span><?php echo $row['recenzie']; ?></span>
                                <strong><?php echo $row['prenume'] . ' ' . substr($row['nume'], 0,1) . '.'; ?></strong>
                            </blockquote>
                            
                        </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
