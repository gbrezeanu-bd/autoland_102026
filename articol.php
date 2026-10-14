<?php
// Get config files
include_once './includes/config.php';
include_once './includes/json_ld_schemas.php';

// Preia slug-ul din URL (trimis de .htaccess ca parametru GET)
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

// Validare slug (doar litere, cifre, cratimă și underline)
if (empty($slug) || !preg_match('/^[a-zA-Z0-9\-_]+$/', $slug)) {
    header("HTTP/1.0 404 Not Found");
    include('404.php');
    exit;
}

// Protejare împotriva SQL injection
$slug_safe = $connection->real_escape_string($slug);

// Query pentru preluare articol după slug (cu toate câmpurile SEO)
$query_article = "SELECT * FROM articole WHERE slug = '$slug_safe' AND status = 'published' LIMIT 1";
$result = $connection->query($query_article) or die($connection->error);

// Verifică dacă articolul există
if ($result->num_rows == 0) {
    header("HTTP/1.0 404 Not Found");
    include('404.php');
    exit;
}

// Preia datele articolului
$article = $result->fetch_assoc();

// Incrementează numărul de citiri
$article_id = (int)$article['id'];
$update_views = "UPDATE articole SET numar_citiri = numar_citiri + 1 WHERE id = $article_id";
$connection->query($update_views);

// ========================================
// SETARE META TAGS pentru SEO
// ========================================

// Title - Logică inteligentă pentru adăugare brand
if (!empty($article['meta_title'])) {
    // Dacă e setat manual în DB, respectă decizia editorială
    $page_title = $article['meta_title'];
} else {
    // Calculează automat ce variantă de brand încape
    $max_title_length = 60;  // Lungime maximă pentru Google SERP
    $brand_full = " | AUTOLAND România";  // 19 caractere
    $brand_short = " | AUTOLAND";          // 11 caractere
    $title_length = mb_strlen($article['titlu'], 'UTF-8');
    
    if ($title_length + 19 <= $max_title_length) {
        // Încape cu brand complet
        $page_title = $article['titlu'] . $brand_full;
    } elseif ($title_length + 11 <= $max_title_length) {
        // Încape doar cu brand scurt
        $page_title = $article['titlu'] . $brand_short;
    } else {
        // Nu încape nimic, lasă doar titlul (Google va adăuga brandul automat)
        $page_title = $article['titlu'];
    }
}

// Excerpt - fallback la meta_description dacă nu e setat (simplificare)
// $page_subtitle = !empty($article['excerpt']) 
//     ? $article['excerpt'] 
//     : ($article['meta_description'] ?? 'Expert în mașini rulate verificate tehnic din 2007');

// Meta Description
$meta_description = $article['meta_description'];

// Meta Keywords - Generează automat din tags dacă nu există
if (!empty($article['meta_keywords'])) {
    $meta_keywords = $article['meta_keywords'];
} elseif (!empty($article['tags'])) {
    // Convertește tag-urile în keywords
    $meta_keywords = $article['tags'] . ", autoland iași, dealer auto românia";
} else {
    $meta_keywords = "autoland iași, mașini rulate, dealer auto iași, dealer auto românia";
}

// Canonical URL - GENERAT AUTOMAT din slug (nu mai depinde de DB)
// Format: /articole/{slug} pentru structură ierarhică clară și SEO optimizat
$canonical_url = BASE_PATH . 'articole/' . $article['slug'];

// Open Graph & Twitter Card - Imagini Default per Categorie
$default_images_by_slug = [
    'ghid-masini-second-hand' => BASE_PATH . 'images/articles/default-ghid-masini-sh.jpg',
    'finantare-leasing-auto' => BASE_PATH . 'images/articles/default-finantare.jpg',
    'servicii-complete-auto' => BASE_PATH . 'images/articles/default-servicii.jpg',
    'import-masini-la-comanda' => BASE_PATH . 'images/articles/default-import.jpg',
];

// Prioritate: imagine custom > imagine default per articol > logo
// Dacă imaginea din DB este relativă, adaugă BASE_PATH
$imagine_featured_full = '';
if (!empty($article['imagine_featured'])) {
    // Dacă imaginea începe cu http/https, este deja o cale absolută
    if (strpos($article['imagine_featured'], 'http://') === 0 || strpos($article['imagine_featured'], 'https://') === 0) {
        $imagine_featured_full = $article['imagine_featured'];
    } else {
        // Altfel, adaugă BASE_PATH pentru cale relativă
        $imagine_featured_full = BASE_PATH . ltrim($article['imagine_featured'], '/');
    }
}

$og_image = $imagine_featured_full 
    ?: ($default_images_by_slug[$article['slug']] ?? null)
    ?: BASE_PATH . 'themes/automotive/images/logo_autoland_social.png';  // Logo optimizat 1200x630

$og_image_alt = $article['imagine_alt_text'] ?? $article['titlu'];

// Set base URL for links
$base_url = defined('BASE_PATH') ? BASE_PATH : './';

// Conținutul articolului - preia HTML-ul din DB
$content = $article['text'];

// Verifică dacă conținutul este NULL sau gol
if ($content === null || $content === '') {
    $content = '';
} else {
    // Asigură encoding UTF-8 corect
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');
    }
    
    // Decodează entitățile HTML dacă HTML-ul este escapat în DB
    // Verifică dacă conține entități HTML escapat (ex: &lt;div&gt;) dar nu conține tag-uri HTML valide
    // Aceasta indică faptul că HTML-ul a fost salvat cu entități HTML în loc de tag-uri reale
    $has_escaped_entities = (
        (strpos($content, '&lt;') !== false || strpos($content, '&gt;') !== false) &&
        !preg_match('/<[a-z][\s\S]*>/i', $content)
    );
    
    if ($has_escaped_entities) {
        // Decodează toate entitățile HTML (inclusiv &amp;, &quot;, etc.)
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// ========================================
// SCRIPT-URI și META TAGS SUPLIMENTARE
// ========================================

$header_scripts = '';

// 1. Canonical URL
$header_scripts .= '<link rel="canonical" href="' . htmlspecialchars($canonical_url) . '" />' . "\n";

// 2. Additional Meta Tags
$header_scripts .= '<meta name="author" content="' . htmlspecialchars($article['autor'] ?? 'AUTOLAND România') . '" />' . "\n";
$header_scripts .= '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
if (!empty($article['categorie'])) {
    $header_scripts .= '<meta name="article:section" content="' . htmlspecialchars($article['categorie']) . '" />' . "\n";
}
if (!empty($article['tags'])) {
    $tags_array = explode(',', $article['tags']);
    foreach ($tags_array as $tag) {
        $header_scripts .= '<meta name="article:tag" content="' . htmlspecialchars(trim($tag)) . '" />' . "\n";
    }
}

// 3. Open Graph Meta Tags (Facebook)
$header_scripts .= '<meta property="og:locale" content="ro_RO" />' . "\n";
$header_scripts .= '<meta property="og:type" content="article" />' . "\n";
$header_scripts .= '<meta property="og:title" content="' . htmlspecialchars($article['titlu']) . '" />' . "\n";
$header_scripts .= '<meta property="og:description" content="' . htmlspecialchars($meta_description) . '" />' . "\n";
$header_scripts .= '<meta property="og:url" content="' . htmlspecialchars($canonical_url) . '" />' . "\n";
$header_scripts .= '<meta property="og:site_name" content="AUTOLAND România" />' . "\n";
$header_scripts .= '<meta property="og:image" content="' . htmlspecialchars($og_image) . '" />' . "\n";
$header_scripts .= '<meta property="og:image:secure_url" content="' . htmlspecialchars($og_image) . '" />' . "\n";
$header_scripts .= '<meta property="og:image:width" content="1200" />' . "\n";
$header_scripts .= '<meta property="og:image:height" content="630" />' . "\n";
$header_scripts .= '<meta property="og:image:alt" content="' . htmlspecialchars($og_image_alt) . '" />' . "\n";
if (!empty($article['data_publicarii'])) {
    $header_scripts .= '<meta property="article:published_time" content="' . date('c', strtotime($article['data_publicarii'])) . '" />' . "\n";
}
if (!empty($article['data_actualizarii'])) {
    $header_scripts .= '<meta property="article:modified_time" content="' . date('c', strtotime($article['data_actualizarii'])) . '" />' . "\n";
}
if (!empty($article['autor'])) {
    $header_scripts .= '<meta property="article:author" content="' . htmlspecialchars($article['autor']) . '" />' . "\n";
}

// 4. Twitter Card Meta Tags
$header_scripts .= '<meta name="twitter:card" content="summary_large_image" />' . "\n";
$header_scripts .= '<meta name="twitter:title" content="' . htmlspecialchars($article['titlu']) . '" />' . "\n";
$header_scripts .= '<meta name="twitter:description" content="' . htmlspecialchars($meta_description) . '" />' . "\n";
$header_scripts .= '<meta name="twitter:url" content="' . htmlspecialchars($canonical_url) . '" />' . "\n";
$header_scripts .= '<meta name="twitter:image" content="' . htmlspecialchars($og_image) . '" />' . "\n";
$header_scripts .= '<meta name="twitter:image:alt" content="' . htmlspecialchars($og_image_alt) . '" />' . "\n";
$header_scripts .= '<meta name="twitter:creator" content="@autolandromania" />' . "\n";
$header_scripts .= '<meta name="twitter:site" content="@autolandromania" />' . "\n";

// 4. Smooth Scroll pentru linkurile din cuprins
$header_scripts .= '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Smooth scroll pentru linkuri anchor
    document.querySelectorAll(\'a[href^="#"]\').forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const targetId = this.getAttribute("href");
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const headerOffset = 100;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });
});
</script>
';

// ========================================
// JSON-LD SCHEMA.ORG (pentru Google Rich Results)
// COMPLET AUTOMATIZAT - nu necesită configurare manuală
// OPTIMIZED: Toate schemas combinate într-un singur script tag
// ========================================
$article_schemas = [
    getArticleSchema($article, $canonical_url, $og_image),
    getArticleBreadcrumbSchema($article['categorie'], $article['titlu'], $canonical_url),
    getOrganizationSchema(),
    getWebSiteSchema()
];
$header_scripts .= getJsonLdString($article_schemas); // Combine toate într-un singur tag

// ========================================
// ARTICOLE CONEXE (dacă sunt definite)
// ========================================

$articole_conexe = [];
if (!empty($article['articole_conexe'])) {
    $ids_conexe = $article['articole_conexe'];
    $query_conexe = "SELECT id, titlu, slug, excerpt, timp_citire FROM articole 
                     WHERE id IN ($ids_conexe) AND status = 'published' 
                     ORDER BY FIELD(id, $ids_conexe) 
                     LIMIT 3";
    $result_conexe = $connection->query($query_conexe);
    
    if ($result_conexe && $result_conexe->num_rows > 0) {
        while ($row = $result_conexe->fetch_assoc()) {
            $articole_conexe[] = $row;
        }
    }
}

include './themes/automotive/template_parts/header.php';
?>

<!-- ========================================
     SECȚIUNE ARTICOL - Afișare Completă
     ======================================== -->
<section class="content">
    <div class="container">
        <div class="inner-page row">
            
            <!-- Sidebar Stânga -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none">
                <?php include './themes/automotive/template_parts/sidebar_left.php'; ?>
            </div>
            
            <!-- Conținut Principal Articol -->
            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none">
                <div class="content-wrap car_listings">
                    
                    <!-- Conținut Articol -->
                    <article class="article-content">
                        
                        <!-- Titlu H1 Articol -->
                        <header class="article-header">
                            <h2 class="article-title"><?php echo htmlspecialchars($article['titlu']); ?></h2>
                            
                            <!-- Meta Informații -->
                            <div class="article-meta-bar">
                                <?php if (!empty($article['autor'])): ?>
                                    <span class="article-author">
                                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($article['autor']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($article['data_publicarii'])): ?>
                                    <span class="article-date">
                                        <i class="fa fa-calendar"></i> <?php echo date('d.m.Y', strtotime($article['data_publicarii'])); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($article['timp_citire'])): ?>
                                    <span class="article-reading-time">
                                        <i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($article['timp_citire']); ?> min citire
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($article['numar_citiri'])): ?>
                                    <span class="article-views">
                                        <i class="fa fa-eye"></i> <?php echo number_format((int)$article['numar_citiri'] + 1, 0, ',', '.'); ?> citiri
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($article['categorie'])): ?>
                                    <span class="article-category">
                                        <i class="fa fa-folder"></i> <?php echo htmlspecialchars($article['categorie']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </header>
                        
                        <!-- Excerpt (dacă există) - sub meta info -->
                        <?php if (!empty($article['excerpt'])): ?>
                            <div class="article-excerpt">
                                <p><strong><?php echo htmlspecialchars($article['excerpt']); ?></strong></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Imagine Featured (dacă există) - sub excerpt -->
                        <?php if (!empty($imagine_featured_full)): ?>
                            <div class="article-featured-image">
                                <img 
                                    src="<?php echo htmlspecialchars($imagine_featured_full); ?>" 
                                    alt="<?php echo htmlspecialchars($article['imagine_alt_text'] ?? $article['titlu']); ?>"
                                    class="img-responsive"
                                />
                            </div>
                        <?php endif; ?>
                        
                        <!-- Conținut Principal -->
                        <div class="article-body">
                            <?php 
                            // Afișează HTML-ul direct, fără escape
                            echo $content; 
                            ?>
                        </div>
                        
                        <!-- Final CTA - apare după fiecare articol -->
                        <div class="article-cta-final" style="text-align: center; padding: 30px; background: #0891b2; border-radius: 6px; color: white; margin-top: 40px;">
                            <h2 style="color: white; margin: 0 0 15px 0; font-size: 24px; font-weight: 700;">
                                Gata Să Găsiți Mașina Perfectă? 🚗
                            </h2>
                            <p style="font-size: 16px; margin: 0 0 20px 0; line-height: 1.6;">
                                18 ani experiență | Verificare 100% în service autorizat | Garanție 12 luni | Finanțare 0% avans
                            </p>

                            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                                <a href="<?php echo $base_url; ?>contact.php" style="display: inline-block; padding: 12px 24px; background: white; color: #0891b2; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 15px;">
                                    📞 Contactează-ne Acum
                                </a>
                                <a href="<?php echo $base_url; ?>vanzari.php" style="display: inline-block; padding: 12px 24px; background: transparent; color: white; border: 2px solid white; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 15px;">
                                    🔍 Vezi Ofertele Noastre
                                </a>
                            </div>

                            <p style="margin: 20px 0 0 0; font-size: 14px;">
                                📍 <strong>AUTOLAND Iași</strong> - Comercializăm în toată România!
                            </p>
                        </div>

                        <!-- Info Autor (dacă există bio) -->
                        <?php if (!empty($article['autor']) && !empty($article['autor_bio'])): ?>
                            <div class="article-author-bio">
                                <h4><i class="fa fa-user-circle"></i> Despre autor</h4>
                                <p><strong><?php echo htmlspecialchars($article['autor']); ?></strong></p>
                                <p><?php echo htmlspecialchars($article['autor_bio']); ?></p>
                                <p style="margin-top: 15px;">
                                    <a href="<?php echo BASE_PATH; ?>index.php" 
                                       style="display: inline-block; padding: 8px 16px; background: #0891b2; color: white; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s;"
                                       onmouseover="this.style.background='#0e7490'"
                                       onmouseout="this.style.background='#0891b2'">
                                        <i class="fa fa-info-circle"></i> Află mai multe despre noi
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Link către toate articolele -->
                        <div class="article-more-articles" style="margin: 30px 0; padding: 20px; background: #f0f9ff; border-left: 4px solid #0891b2; border-radius: 5px;">
                            <h4 style="margin: 0 0 10px 0; color: #0891b2; font-size: 18px;">
                                <i class="fa fa-book"></i> Vrei să citești mai multe articole?
                            </h4>
                            <p style="margin: 0 0 15px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                                Descoperă toate ghidurile noastre utile despre mașini second hand, finanțare auto, servicii complete și import mașini.
                            </p>
                            <a href="<?php echo BASE_PATH; ?>articole/" 
                               style="display: inline-block; padding: 12px 24px; background: #0891b2; color: white; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 15px; transition: background 0.2s;"
                               onmouseover="this.style.background='#0e7490'"
                               onmouseout="this.style.background='#0891b2'">
                                <i class="fa fa-arrow-right"></i> Vezi toate articolele
                            </a>
                        </div>
                        
                        <!-- Tags (dacă există) - SEO optimizat -->
                        <?php if (!empty($article['tags'])): ?>
                            <div class="article-tags">
                                <strong><i class="fa fa-tags"></i> Cuvinte cheie:</strong>
                                <div class="tags-container">
                                    <?php 
                                    $tags_array = explode(',', $article['tags']);
                                    foreach ($tags_array as $tag): 
                                        $tag = trim($tag);
                                    ?>
                                        <span class="tag-badge">
                                            <?php echo htmlspecialchars($tag); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- CTA Button (dacă există) -->
                        <?php if (!empty($article['cta_text']) && !empty($article['cta_link'])): ?>
                            <div class="article-cta">
                                <a href="<?php echo htmlspecialchars($article['cta_link']); ?>" 
                                   class="btn btn-primary btn-lg">
                                    <?php echo htmlspecialchars($article['cta_text']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    </article>
                    
                    <!-- Articole Conexe (dacă există) -->
                    <?php if (!empty($articole_conexe)): ?>
                        <div class="related-articles">
                            <h3><i class="fa fa-newspaper-o"></i> Articole Conexe</h3>
                            <div class="row">
                                <?php foreach ($articole_conexe as $related): ?>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="related-article-card">
                                            <h4 style="font-size: 16px; margin-top: 0;">
                                                <a href="<?php echo BASE_PATH . 'articole/' . htmlspecialchars($related['slug']); ?>">
                                                    <?php echo htmlspecialchars($related['titlu']); ?>
                                                </a>
                                            </h4>
                                            <?php if (!empty($related['excerpt'])): ?>
                                                <p style="font-size: 14px; color: #666;">
                                                    <?php echo htmlspecialchars(substr($related['excerpt'], 0, 100)) . '...'; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($related['timp_citire'])): ?>
                                                <p style="font-size: 12px; color: #999;">
                                                    <i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($related['timp_citire']); ?> min citire
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php
// ========================================
// INCLUDE FOOTER
// ========================================
    include './themes/automotive/template_parts/footer.php';
?>