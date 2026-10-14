<?php
// Get config files
include_once './includes/config.php';
include_once './includes/json_ld_schemas.php';

// Set optimized page title (direct in file for easier maintenance)
$page_title = "Articole - Ghiduri și Sfaturi Auto | AUTOLAND România";
$meta_description = "Ghiduri utile despre mașini second hand, finanțare auto, servicii complete și import mașini. Sfaturi practice de la dealerul auto AUTOLAND România cu peste 18 ani experiență.";
$meta_keywords = "ghiduri auto, sfaturi mașini, second hand, finanțare auto, leasing, servicii auto, import mașini";

// Canonical URL
$canonical_url = BASE_PATH . 'articole/';

// Set base URL for links
$base_url = defined('BASE_PATH') ? BASE_PATH : './';

// Paginare - 12 articole per pagină
$articles_per_page = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $articles_per_page;

// Query pentru numărul total de articole publicate
$count_query = "SELECT COUNT(*) as total FROM articole WHERE status = 'published'";
$count_result = $connection->query($count_query);
$total_articles = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_articles / $articles_per_page);

// Query pentru articole publicate, ordonate după data publicării (cele mai recente primele)
$articles_query = "SELECT id, titlu, slug, excerpt, meta_description, imagine_featured, 
                          data_publicarii, timp_citire, numar_citiri, categorie, autor
                   FROM articole 
                   WHERE status = 'published' 
                   ORDER BY data_publicarii DESC, id DESC 
                   LIMIT $articles_per_page OFFSET $offset";
$articles_result = $connection->query($articles_query);

$articles = [];
if ($articles_result && $articles_result->num_rows > 0) {
    while ($row = $articles_result->fetch_assoc()) {
        // Procesează imaginea featured - adaugă BASE_PATH dacă e relativă
        $imagine_featured_full = '';
        if (!empty($row['imagine_featured'])) {
            if (strpos($row['imagine_featured'], 'http://') === 0 || strpos($row['imagine_featured'], 'https://') === 0) {
                $imagine_featured_full = $row['imagine_featured'];
            } else {
                $imagine_featured_full = BASE_PATH . ltrim($row['imagine_featured'], '/');
            }
        }
        $row['imagine_featured_full'] = $imagine_featured_full;
        $articles[] = $row;
    }
}

// Set header scripts pentru SEO
$header_scripts = '';
$header_scripts .= '<link rel="canonical" href="' . htmlspecialchars($canonical_url) . '" />' . "\n";

// Open Graph Meta Tags
$header_scripts .= '<meta property="og:type" content="website" />' . "\n";
$header_scripts .= '<meta property="og:url" content="' . htmlspecialchars($canonical_url) . '" />' . "\n";
$header_scripts .= '<meta property="og:title" content="' . htmlspecialchars($page_title) . '" />' . "\n";
$header_scripts .= '<meta property="og:description" content="' . htmlspecialchars($meta_description) . '" />' . "\n";

// Breadcrumb Schema
$breadcrumb_schema = [
    "@context" => "https://schema.org",
    "@type" => "BreadcrumbList",
    "itemListElement" => [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => "Acasă",
            "item" => JSONLD_BASE_URL
        ],
        [
            "@type" => "ListItem",
            "position" => 2,
            "name" => "Articole",
            "item" => JSONLD_BASE_URL . "articole/"
        ]
    ]
];
// CollectionPage Schema
$collection_schema = [
    "@context" => "https://schema.org",
    "@type" => "CollectionPage",
    "name" => "Articole - Ghiduri și Sfaturi Auto",
    "description" => $meta_description,
    "url" => $canonical_url,
    "mainEntity" => [
        "@type" => "ItemList",
        "numberOfItems" => $total_articles,
        "itemListElement" => []
    ]
];
// Adaugă primele articole în schema (cu image și author pentru fiecare)
$default_image = JSONLD_BASE_URL . 'themes/automotive/images/logo_autoland_social.png';
foreach (array_slice($articles, 0, 10) as $index => $article) {
    // Determină imaginea pentru articol
    $article_image = !empty($article['imagine_featured_full']) 
        ? $article['imagine_featured_full'] 
        : $default_image;
    
    // Determină autorul
    $author_name = !empty($article['autor']) && trim($article['autor']) !== '' 
        ? trim($article['autor']) 
        : JSONLD_COMPANY_NAME;
    
    $article_item = [
        "@type" => "Article",
        "headline" => $article['titlu'],
        "url" => BASE_PATH . 'articole/' . $article['slug'],
        // Image - obligatoriu pentru Article schema
        "image" => [
            "@type" => "ImageObject",
            "url" => $article_image,
            "width" => 1200,
            "height" => 630
        ],
        // Author - obligatoriu pentru Article schema
        "author" => [
            "@type" => "Organization",
            "name" => $author_name,
            "url" => JSONLD_BASE_URL
        ]
    ];
    
    // Adaugă description dacă există
    if (!empty($article['meta_description'])) {
        $article_item["description"] = $article['meta_description'];
    } elseif (!empty($article['excerpt'])) {
        $article_item["description"] = $article['excerpt'];
    }
    
    // Adaugă data publicării dacă există
    if (!empty($article['data_publicarii'])) {
        $article_item["datePublished"] = date('c', strtotime($article['data_publicarii']));
    }
    
    $collection_schema["mainEntity"]["itemListElement"][] = [
        "@type" => "ListItem",
        "position" => $index + 1,
        "item" => $article_item
    ];
}

// OPTIMIZED: Combine toate schemas într-un singur script tag
$article_list_schemas = [
    $breadcrumb_schema,
    $collection_schema
];
$header_scripts .= getJsonLdString($article_list_schemas);

include './themes/automotive/template_parts/header.php';
?>

<!-- ========================================
     SECȚIUNE LISTARE ARTICOLE
     ======================================== -->
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
            
            <!-- Conținut Principal - Lista Articole -->
            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp information_head welcome-wrap">
                <div class="content-wrap car_listings">
                    
                    <!-- Header Pagină -->
                    <div class="information_head" style="margin-bottom: 30px;">
                        <h1 style="color: #0891b2; margin: 0 0 10px 0; font-size: 28px; font-weight: 700;">
                            📚 Articole - Ghiduri și Sfaturi Auto
                        </h1>
                        <p style="font-size: 16px; color: #64748b; margin: 0 0 10px 0;">
                            Ghiduri utile, sfaturi practice și informații despre mașini second hand, finanțare auto, servicii complete și import mașini.
                        </p>
                        <?php if ($total_articles > 0): ?>
                            <p style="font-size: 14px; color: #94a3b8; margin: 0;">
                                <strong><?php echo number_format($total_articles, 0, ',', '.'); ?></strong> articol<?php echo $total_articles != 1 ? 'e' : ''; ?> publicat<?php echo $total_articles != 1 ? 'e' : ''; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($articles)): ?>
                        <!-- Mesaj dacă nu există articole -->
                        <div style="text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 48px; margin-bottom: 20px;">📝</div>
                            <h2 style="color: #475569; margin: 0 0 15px 0; font-size: 22px; font-weight: 600;">
                                Nu există articole publicate momentan
                            </h2>
                            <p style="color: #64748b; font-size: 15px; margin: 0;">
                                Articolele vor apărea aici odată ce sunt publicate.
                            </p>
                        </div>
                    <?php else: ?>
                        <!-- Grid Articole -->
                        <div class="articles-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin-bottom: 40px;">
                            <?php foreach ($articles as $article): ?>
                                <article class="article-card" style="background: white; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    
                                    <!-- Imagine Featured (dacă există) -->
                                    <?php if (!empty($article['imagine_featured_full'])): ?>
                                        <div class="article-image" style="width: 100%; height: 200px; overflow: hidden; background: #f1f5f9;">
                                            <a href="<?php echo BASE_PATH; ?>articole/<?php echo htmlspecialchars($article['slug']); ?>" style="display: block; width: 100%; height: 100%;">
                                                <img src="<?php echo htmlspecialchars($article['imagine_featured_full']); ?>" 
                                                     alt="<?php echo htmlspecialchars($article['titlu']); ?>"
                                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;"
                                                     onmouseover="this.style.transform='scale(1.05)'"
                                                     onmouseout="this.style.transform='scale(1)'" />
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <!-- Placeholder dacă nu există imagine -->
                                        <div class="article-image-placeholder" style="width: 100%; height: 200px; background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); display: flex; align-items: center; justify-content: center;">
                                            <div style="color: white; font-size: 48px; opacity: 0.7;">📄</div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Conținut Card -->
                                    <div class="article-card-content" style="padding: 20px;">
                                        
                                        <!-- Categorie -->
                                        <?php if (!empty($article['categorie'])): ?>
                                            <div style="margin-bottom: 10px;">
                                                <span style="display: inline-block; padding: 4px 12px; background: #f0f9ff; color: #0891b2; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                    <?php echo htmlspecialchars($article['categorie']); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Titlu -->
                                        <h2 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 700; line-height: 1.3;">
                                            <a href="<?php echo BASE_PATH; ?>articole/<?php echo htmlspecialchars($article['slug']); ?>" 
                                               style="color: #1e293b; text-decoration: none; transition: color 0.2s;"
                                               onmouseover="this.style.color='#0891b2'"
                                               onmouseout="this.style.color='#1e293b'">
                                                <?php echo htmlspecialchars($article['titlu']); ?>
                                            </a>
                                        </h2>
                                        
                                        <!-- Excerpt -->
                                        <?php if (!empty($article['excerpt'])): ?>
                                            <p style="font-size: 14px; line-height: 1.6; color: #64748b; margin: 0 0 15px 0;">
                                                <?php echo htmlspecialchars(mb_substr($article['excerpt'], 0, 120)); ?><?php echo mb_strlen($article['excerpt']) > 120 ? '...' : ''; ?>
                                            </p>
                                        <?php elseif (!empty($article['meta_description'])): ?>
                                            <p style="font-size: 14px; line-height: 1.6; color: #64748b; margin: 0 0 15px 0;">
                                                <?php echo htmlspecialchars(mb_substr($article['meta_description'], 0, 120)); ?><?php echo mb_strlen($article['meta_description']) > 120 ? '...' : ''; ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <!-- Meta Info -->
                                        <div class="article-meta" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; font-size: 12px; color: #94a3b8; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                                            <?php if (!empty($article['data_publicarii'])): ?>
                                                <span>
                                                    <i class="fa fa-calendar"></i> <?php echo date('d.m.Y', strtotime($article['data_publicarii'])); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($article['timp_citire'])): ?>
                                                <span>
                                                    <i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($article['timp_citire']); ?> min
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($article['numar_citiri'])): ?>
                                                <span>
                                                    <i class="fa fa-eye"></i> <?php echo number_format((int)$article['numar_citiri'], 0, ',', '.'); ?> citiri
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Link Citeste Mai Mult -->
                                        <div style="margin-top: 15px;">
                                            <a href="<?php echo BASE_PATH; ?>articole/<?php echo htmlspecialchars($article['slug']); ?>" 
                                               style="display: inline-block; padding: 8px 16px; background: #0891b2; color: white; border-radius: 5px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s;"
                                               onmouseover="this.style.background='#0e7490'"
                                               onmouseout="this.style.background='#0891b2'">
                                                Citește mai mult →
                                            </a>
                                        </div>
                                        
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Paginare -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination-wrapper" style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 2px solid #e2e8f0;">
                                <div class="pagination" style="display: inline-flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                                    <?php if ($page > 1): ?>
                                        <a href="<?php echo BASE_PATH; ?>articole/?page=<?php echo $page - 1; ?>" 
                                           style="display: inline-block; padding: 10px 16px; background: white; color: #0891b2; border: 1px solid #0891b2; border-radius: 5px; text-decoration: none; font-weight: 600; transition: all 0.2s;"
                                           onmouseover="this.style.background='#0891b2'; this.style.color='white'"
                                           onmouseout="this.style.background='white'; this.style.color='#0891b2'">
                                            ← Anterior
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php
                                    // Afișează numerele paginilor
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    if ($start_page > 1): ?>
                                        <a href="<?php echo BASE_PATH; ?>articole/" 
                                           style="display: inline-block; padding: 10px 16px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 5px; text-decoration: none; font-weight: 600;">
                                            1
                                        </a>
                                        <?php if ($start_page > 2): ?>
                                            <span style="padding: 10px 8px; color: #94a3b8;">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <?php if ($i == $page): ?>
                                            <span style="display: inline-block; padding: 10px 16px; background: #0891b2; color: white; border-radius: 5px; font-weight: 700;">
                                                <?php echo $i; ?>
                                            </span>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_PATH; ?>articole/?page=<?php echo $i; ?>" 
                                               style="display: inline-block; padding: 10px 16px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 5px; text-decoration: none; font-weight: 600; transition: all 0.2s;"
                                               onmouseover="this.style.background='#f0f9ff'; this.style.borderColor='#0891b2'"
                                               onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0'">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    
                                    <?php if ($end_page < $total_pages): ?>
                                        <?php if ($end_page < $total_pages - 1): ?>
                                            <span style="padding: 10px 8px; color: #94a3b8;">...</span>
                                        <?php endif; ?>
                                        <a href="<?php echo BASE_PATH; ?>articole/?page=<?php echo $total_pages; ?>" 
                                           style="display: inline-block; padding: 10px 16px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 5px; text-decoration: none; font-weight: 600;">
                                            <?php echo $total_pages; ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <a href="<?php echo BASE_PATH; ?>articole/?page=<?php echo $page + 1; ?>" 
                                           style="display: inline-block; padding: 10px 16px; background: white; color: #0891b2; border: 1px solid #0891b2; border-radius: 5px; text-decoration: none; font-weight: 600; transition: all 0.2s;"
                                           onmouseover="this.style.background='#0891b2'; this.style.color='white'"
                                           onmouseout="this.style.background='white'; this.style.color='#0891b2'">
                                            Următor →
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <p style="margin-top: 15px; font-size: 14px; color: #94a3b8;">
                                    Pagina <?php echo $page; ?> din <?php echo $total_pages; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php
include './themes/automotive/template_parts/footer.php';
?>

