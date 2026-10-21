<?php
// get config file;
include_once './includes/config.php';
include_once './includes/pagini_sectiuni.php';

// Aspectul cu meniu lateral (secțiunea Despre Autoland)
$sectiune = 'pagini';
$slug = 'recenzii';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT * FROM recenzii2 WHERE status = '1' ORDER BY `data_recenzie` DESC";

$result = $connection->query($query_text) or db_fail($connection);
$result_number=$result->num_rows;
//$row = mysqli_fetch_assoc($result);

$page_title = "Păreri clienți și recenzii | Autoland România";
$banner_title = "Păreri ale clienților Autoland România";
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
$header_scripts .= '<link rel="stylesheet" href="' . BASE_PATH . 'themes/automotive/css/pagini.css">' . "\n";
$header_scripts .= '<script type="application/ld+json">' . json_encode(getOrganizationSchema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
$header_scripts .= '<script type="application/ld+json">' . json_encode(getBreadcrumbSchema($breadcrumbs), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';


include './themes/automotive/template_parts/header.php';
$footer_scripts .= '';
?>

<?php
// Profilul Google Business al firmei (recenziile Google se citesc direct acolo)
$google_profil = defined('JSONLD_GOOGLE_BUSINESS') ? JSONLD_GOOGLE_BUSINESS : 'https://maps.app.goo.gl/cuxVc4u2BkxwVWXt6';
$ultimul_an = '';
$recenzii_site = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recenzii_site[] = $row;
}
if ($recenzii_site) {
    $ultimul_an = date('Y', strtotime($recenzii_site[0]['data_recenzie']));
}
?>

<section class="content">
    <div class="container">
        <div class="inner-page">
            <div class="ap-layout">
                <?php include './themes/automotive/template_parts/pagina_sidebar.php'; ?>
                <div class="ap-main">
                    <div class="ap">
                        <h2>Recenzii pe Google</h2>
                        <p><strong>Recenziile din profilul Autoland România sunt publice pe Google.</strong> Le poți citi acolo, în ordinea dorită, și poți lăsa propria recenzie după ce cumperi o mașină.</p>
                        <p><a href="<?php echo htmlspecialchars($google_profil); ?>" target="_blank" rel="noopener">Deschide profilul Autoland România pe Google</a></p>

                        <h2>Păreri trimise pe site</h2>
                        <?php if (!$recenzii_site): ?>
                            <p>Momentan nu avem păreri publicate pe site.</p>
                        <?php else: ?>
                            <div class="ap-reviews ap-reviews-wide">
                                <?php foreach ($recenzii_site as $r): ?>
                                    <?php
                                    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($r['recenzie'])));
                                    $autor = trim(trim($r['prenume']) . ' ' . mb_substr(trim($r['nume']), 0, 1, 'UTF-8') . '.');
                                    ?>
                                    <blockquote class="ap-review">
                                        <p><?php echo htmlspecialchars($text); ?></p>
                                        <span class="ap-review-by"><?php echo htmlspecialchars($autor); ?></span>
                                    </blockquote>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <p class="ap-related"><span>Vezi și:</span>
                            <a href="<?php echo BASE_PATH; ?>despre-noi/">Despre Autoland</a>
                            <a href="<?php echo BASE_PATH; ?>garantie.php">Garanție</a>
                            <a href="<?php echo BASE_PATH; ?>leasing.php">Leasing și rate</a>
                            <a href="<?php echo BASE_PATH; ?>contact.php">Contact</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</section>


<?php
include './themes/automotive/template_parts/footer.php';
