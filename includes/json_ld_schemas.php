<?php
/**
 * JSON-LD Schema Generator Functions
 * Provides reusable structured data schemas for SEO
 * 
 * @copyright (c) 2025, Autoland Romania
 * @version 2.0
 * @date 2025-11-17
 */

// ========================================
// CONSTANTE PENTRU STRUCTURED DATA
// Modifică aici o singură dată pentru a actualiza în toate schema-urile
// ========================================

// Informații Business
define('JSONLD_COMPANY_NAME', 'Autoland România');
define('JSONLD_COMPANY_NAME_ALT', 'Autoland Romania');
define('JSONLD_COMPANY_DESCRIPTION', 'Autoland România - dealer auto autorizat în Iași din 2007, cu peste 18 ani experiență. Specializat în vânzare autoturisme, autoutilitare, autospecializate rulate, second hand și noi, import direct din Europa. Oferim garanție comercială în scris, de la 1 an până la 3 ani, verificare tehnică 100% în service autorizat, finanțare auto cu avans 0% (leasing și rate), servicii complete de înmatriculare, RAR și omologare. Stoc permanent actualizat, transparență totală și asistență post-vânzare pentru persoane fizice și juridice.');

// URL-uri
define('JSONLD_BASE_URL', 'https://autolandromania.ro/');
define('JSONLD_LOGO_URL', 'https://autolandromania.ro/themes/automotive/images/logo_autoland_3.png');
define('JSONLD_LOGO_WIDTH', 250);
define('JSONLD_LOGO_HEIGHT', 60);

// Contact
define('JSONLD_PHONE', '+40 742 377 001');
define('JSONLD_EMAIL', 'office@autolandromania.ro');

// Date firmă (identice cu pagina de contact)
define('JSONLD_LEGAL_NAME', 'S.C. Autoland S.R.L.');
define('JSONLD_VAT_ID', 'RO21834696');
define('JSONLD_REG_COM', 'J22/1605/2007');

// Adresă (format identic cu Google Business Profile pentru NAP consistency)
define('JSONLD_STREET', 'Strada Luca Arbore nr. 3');  // Format profesional
define('JSONLD_CITY', 'Iași');  // Cu diacritice corecte
define('JSONLD_REGION', 'Iași');  // Județul Iași
define('JSONLD_POSTAL_CODE', '700440');
define('JSONLD_COUNTRY_CODE', 'RO');
define('JSONLD_COUNTRY_NAME', 'România');  // Cu diacritice corecte

// Coordonate GPS (pentru Google Maps și Local Business)
define('JSONLD_LATITUDE', '47.1707578');
define('JSONLD_LONGITUDE', '27.543852');

// Social Media
define('JSONLD_TIKTOK', 'https://www.tiktok.com/@autoland_romania');

// Google Business & Maps
define('JSONLD_GOOGLE_BUSINESS', 'https://share.google/k4WqcN1Et6679n5FY');  // Link scurt Google Business Profile
define('JSONLD_GOOGLE_MAPS', 'https://maps.app.goo.gl/cuxVc4u2BkxwVWXt6');  // Link Google Maps cu pin

// Program funcționare (returnat ca array)
function getOpeningHours() {
    return [
        [
            "@type" => "OpeningHoursSpecification",
            "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens" => "10:00",
            "closes" => "18:00"
        ]
    ];
}

// Social media profiles (returnat ca array)
function getSocialProfiles() {
    return [
        JSONLD_TIKTOK,
        JSONLD_GOOGLE_BUSINESS  // Include Google Business Profile pentru Knowledge Graph
    ];
}

// Adresă completă (returnat ca array pentru JSON-LD)
function getAddress() {
    return [
        "@type" => "PostalAddress",
        "streetAddress" => JSONLD_STREET,
        "addressLocality" => JSONLD_CITY,
        "addressRegion" => JSONLD_REGION,
        "postalCode" => JSONLD_POSTAL_CODE,
        "addressCountry" => JSONLD_COUNTRY_CODE
    ];
}

// Coordonate geo (returnat ca array pentru JSON-LD)
function getGeoCoordinates() {
    return [
        "@type" => "GeoCoordinates",
        "latitude" => JSONLD_LATITUDE,
        "longitude" => JSONLD_LONGITUDE
    ];
}

// Logo (returnat ca array pentru JSON-LD)
function getLogo($includeSize = false) {
    $logo = [
        "@type" => "ImageObject",
        "url" => JSONLD_LOGO_URL
    ];
    
    if ($includeSize) {
        $logo["width"] = JSONLD_LOGO_WIDTH;
        $logo["height"] = JSONLD_LOGO_HEIGHT;
    }
    
    return $logo;
}

// ========================================
// FUNCȚII GENERARE SCHEMA-URI JSON-LD
// ========================================

/**
 * Generate Organization/AutoDealer schema
 * Use this for homepage and contact page
 * NOW WITH @id FOR ENTITY CONNECTIONS
 */
function getOrganizationSchema() {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "AutoDealer",
        "@id" => JSONLD_BASE_URL . "#organization",  // UNIQUE ID pentru conectare cu alte entități
        "name" => JSONLD_COMPANY_NAME,
        "alternateName" => JSONLD_COMPANY_NAME_ALT,
        "description" => JSONLD_COMPANY_DESCRIPTION,
        "url" => JSONLD_BASE_URL,
        "logo" => getLogo(true),  // Folosim funcția pentru consistență
        "image" => JSONLD_LOGO_URL,
        "telephone" => JSONLD_PHONE,
        "email" => JSONLD_EMAIL,
        "legalName" => JSONLD_LEGAL_NAME,
        "vatID" => JSONLD_VAT_ID,
        "taxID" => JSONLD_VAT_ID,
        "foundingDate" => "2007",
        "identifier" => [
            "@type" => "PropertyValue",
            "name" => "Număr de înregistrare la Registrul Comerțului",
            "value" => JSONLD_REG_COM
        ],
        "contactPoint" => [
            "@type" => "ContactPoint",
            "contactType" => "customer service",
            "telephone" => JSONLD_PHONE,
            "email" => JSONLD_EMAIL,
            "areaServed" => "RO",
            "availableLanguage" => "ro",
            "hoursAvailable" => getOpeningHours()
        ],
        "priceRange" => "€€",
        "address" => getAddress(),
        "geo" => getGeoCoordinates(),
        "openingHoursSpecification" => getOpeningHours(),
        "sameAs" => getSocialProfiles(),
        "hasMap" => JSONLD_GOOGLE_MAPS,  // Google Maps pentru Local SEO
        "hasOfferCatalog" => [
            "@type" => "OfferCatalog",
            "name" => "Autovehicule Second Hand",
            "itemListElement" => [
                [
                    "@type" => "Offer",
                    "itemOffered" => [
                        "@type" => "Car",
                        "name" => "Autoturisme second hand"
                    ]
                ],
                [
                    "@type" => "Offer",
                    "itemOffered" => [
                        "@type" => "Car",
                        "name" => "Autoutilitare second hand"
                    ]
                ],
                [
                    "@type" => "Offer",
                    "itemOffered" => [
                        "@type" => "Car",
                        "name" => "Autospecializate second hand"
                    ]
                ]
            ]
        ],
        "makesOffer" => [
            [
                "@type" => "Offer",
                "name" => "Garanție 12 luni",
                "description" => "Toate autovehiculele sunt vândute cu garanție de 12 luni în scris"
            ],
            [
                "@type" => "Offer",
                "name" => "Finanțare auto",
                "description" => "Oferim posibilități de finanțare: leasing, credit cu avans zero"
            ],
            [
                "@type" => "Offer",
                "name" => "Inmatriculări auto",
                "description" => "Servicii complete de înmatriculare auto"
            ]
        ]
    ];
    
    return $schema;
}

/**
 * Generate LocalBusiness schema (alternative for contact page)
 * More focused on local search
 */
function getLocalBusinessSchema() {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "LocalBusiness",
        "@id" => JSONLD_BASE_URL . "#localbusiness",
        "name" => JSONLD_COMPANY_NAME,
        "description" => JSONLD_COMPANY_DESCRIPTION,
        "url" => JSONLD_BASE_URL,
        "telephone" => JSONLD_PHONE,
        "email" => JSONLD_EMAIL,
        "image" => JSONLD_LOGO_URL,
        "priceRange" => "€€",
        "address" => getAddress(),
        "geo" => getGeoCoordinates(),
        "openingHoursSpecification" => getOpeningHours(),
        "hasMap" => JSONLD_GOOGLE_MAPS  // Google Maps pentru Local Business SEO
    ];
    
    return $schema;
}

/**
 * Generate BreadcrumbList schema
 * 
 * @param array $breadcrumbs Array of breadcrumb items [['name' => 'Home', 'url' => 'https://...']]
 */
function getBreadcrumbSchema($breadcrumbs) {
    $items = [];
    $position = 1;
    
    foreach ($breadcrumbs as $crumb) {
        $items[] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => $crumb['name'],
            "item" => $crumb['url']
        ];
    }
    
    return [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $items
    ];
}

/**
 * Generate WebSite schema with search action
 * Good for homepage
 * NOW CONNECTED TO ORGANIZATION VIA publisher
 */
function getWebSiteSchema() {
    return [
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "@id" => JSONLD_BASE_URL . "#website",
        "url" => JSONLD_BASE_URL,
        "name" => JSONLD_COMPANY_NAME,
        "description" => JSONLD_COMPANY_DESCRIPTION,
        "publisher" => [
            "@id" => JSONLD_BASE_URL . "#organization"  // CONECTARE cu Organization
        ],
        "inLanguage" => "ro-RO",
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => [
                "@type" => "EntryPoint",
                "urlTemplate" => JSONLD_BASE_URL . "vanzari.php?marca={search_term_string}"
            ],
            "query-input" => "required name=search_term_string"
        ]
    ];
}

/**
 * Generate WebPage schema for any page
 * Connects page to WebSite and Organization
 * 
 * @param string $page_url Full URL of the page
 * @param string $page_title Page title
 * @param string $page_description Page description (optional)
 * @param string $breadcrumb_url Breadcrumb URL (optional, for breadcrumb connection)
 * @return array Schema array
 */
function getWebPageSchema($page_url, $page_title, $page_description = '', $breadcrumb_url = '') {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "WebPage",
        "@id" => $page_url . "#webpage",
        "url" => $page_url,
        "name" => $page_title,
        "isPartOf" => [
            "@id" => JSONLD_BASE_URL . "#website"  // CONECTARE cu WebSite
        ],
        "about" => [
            "@id" => JSONLD_BASE_URL . "#organization"  // CONECTARE cu Organization
        ],
        "inLanguage" => "ro-RO",
        "publisher" => [
            "@id" => JSONLD_BASE_URL . "#organization"  // CONECTARE cu Organization
        ]
    ];
    
    if (!empty($page_description)) {
        $schema["description"] = $page_description;
    }
    
    // Connect to breadcrumb if provided
    if (!empty($breadcrumb_url)) {
        $schema["breadcrumb"] = [
            "@id" => $breadcrumb_url . "#breadcrumb"
        ];
    }
    
    return $schema;
}

/**
 * Generate Service schema for service pages
 * 
 * @param string $service_type Type of service (e.g., "Asistență Rutieră", "Înmatriculări Auto")
 * @param string $service_name Name of the service
 * @param string $service_description Description of the service
 * @param string $area_served City name where service is provided (default: "Iași")
 * @param array $additional_properties Additional properties (e.g., "warranty", "offers", "hoursAvailable")
 * @return array Schema array
 */
function getServiceSchema($service_type, $service_name, $service_description, $area_served = "Iași", $additional_properties = []) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Service",
        "serviceType" => $service_type,
        "name" => $service_name,
        "description" => $service_description,
        "provider" => [
            "@id" => JSONLD_BASE_URL . "#organization"  // Reference to Organization
        ],
        "areaServed" => [
            "@type" => "City",
            "name" => $area_served
        ]
    ];
    
    // Add additional properties if provided
    foreach ($additional_properties as $key => $value) {
        $schema[$key] = $value;
    }
    
    return $schema;
}

/**
 * Get Organization reference (for use in Product/Offer schemas)
 * Returns just the @id reference instead of full object
 * 
 * @return array Reference to Organization
 */
function getOrganizationReference() {
    return [
        "@id" => JSONLD_BASE_URL . "#organization"
    ];
}

/**
 * Get full Organization schema for Product/Offer (when needed inline)
 * Use this when you need full Organization data in Product schema
 * 
 * @return array Full Organization schema
 */
function getOrganizationForProduct() {
    return [
        "@type" => "AutoDealer",
        "@id" => JSONLD_BASE_URL . "#organization",
        "name" => JSONLD_COMPANY_NAME,
        "telephone" => JSONLD_PHONE,
        "email" => JSONLD_EMAIL,
        "url" => JSONLD_BASE_URL,
        "image" => JSONLD_LOGO_URL,
        "priceRange" => "€€",
        "address" => getAddress(),
        "openingHoursSpecification" => getOpeningHours()
    ];
}

/**
 * Generate Article/BlogPosting schema for articles
 * 
 * @param array $article Article data from database
 * @param string $canonical_url Full URL of the article
 * @param string $og_image Full URL of the featured image
 * @return array Schema array
 */
function getArticleSchema($article, $canonical_url, $og_image) {
    // Asigură că există întotdeauna o imagine (fallback la logo dacă nu există featured image)
    $default_image = JSONLD_BASE_URL . 'themes/automotive/images/logo_autoland_social.png';
    $article_image = !empty($og_image) ? $og_image : $default_image;
    
    // Determină autorul - dacă există un autor specific în articol, folosește-l, altfel folosește organizația
    $author_name = !empty($article['autor']) && trim($article['autor']) !== '' 
        ? trim($article['autor']) 
        : JSONLD_COMPANY_NAME;
    
    $schema = [
        "@context" => "https://schema.org",
        "@type" => $article['schema_type'] ?? 'Article',
        "headline" => $article['titlu'],
        "description" => $article['meta_description'],
        "url" => $canonical_url,
        // Author - îmbunătățit pentru a respecta recomandările Google
        "author" => [
            "@type" => "Organization",
            "name" => $author_name,
            "url" => JSONLD_BASE_URL,
            "logo" => getLogo()
        ],
        "publisher" => [
            "@id" => JSONLD_BASE_URL . "#organization"  // CONECTARE cu Organization prin @id
        ],
        "datePublished" => date('c', strtotime($article['data_publicarii'] ?? 'now')),
        "dateModified" => date('c', strtotime($article['data_actualizarii'] ?? 'now')),
        // Image - întotdeauna prezent (obligatoriu pentru Article schema)
        "image" => [
            "@type" => "ImageObject",
            "url" => $article_image,
            "width" => 1200,
            "height" => 630
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => $canonical_url
        ],
        "inLanguage" => "ro-RO"
    ];
    
    // Adaugă caption pentru imagine dacă există
    if (!empty($article['imagine_alt_text'])) {
        $schema["image"]["caption"] = $article['imagine_alt_text'];
    } else {
        // Fallback la titlul articolului ca caption
        $schema["image"]["caption"] = $article['titlu'];
    }
    
    // Adaugă timpul de citire
    if (!empty($article['timp_citire'])) {
        $schema["timeRequired"] = "PT" . $article['timp_citire'] . "M";
    }
    
    // Adaugă categoria
    if (!empty($article['categorie'])) {
        $schema["articleSection"] = $article['categorie'];
    }
    
    // Adaugă keywords
    if (!empty($article['meta_keywords'])) {
        $schema["keywords"] = $article['meta_keywords'];
    } elseif (!empty($article['tags'])) {
        $schema["keywords"] = $article['tags'];
    }
    
    // Adaugă word count și preview
    if (!empty($article['text'])) {
        $plain_text = strip_tags($article['text']);
        $schema["wordCount"] = str_word_count($plain_text);
        $schema["articleBody"] = mb_substr($plain_text, 0, 500, 'UTF-8') . '...';
    }
    
    // Adaugă autor bio dacă există
    if (!empty($article['autor_bio'])) {
        $schema["author"]["description"] = $article['autor_bio'];
    }
    
    // Adaugă statistici de citire
    if (!empty($article['numar_citiri'])) {
        $schema["interactionStatistic"] = [
            "@type" => "InteractionCounter",
            "interactionType" => "https://schema.org/ReadAction",
            "userInteractionCount" => (int)$article['numar_citiri']
        ];
    }
    
    return $schema;
}

/**
 * Generate BreadcrumbList schema for articles
 * 
 * @param string $categorie Article category
 * @param string $titlu Article title
 * @param string $canonical_url Article URL
 * @return array Schema array
 */
function getArticleBreadcrumbSchema($categorie, $titlu, $canonical_url) {
    return [
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
            ],
            [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $categorie ?? "Ghiduri",
                "item" => JSONLD_BASE_URL . "articole/#" . strtolower($categorie ?? 'ghiduri')
            ],
            [
                "@type" => "ListItem",
                "position" => 4,
                "name" => $titlu,
                "item" => $canonical_url
            ]
        ]
    ];
}

/**
 * Generate Review schema for a single review
 * 
 * @param string $author_name Name of the reviewer
 * @param string $review_text Review content
 * @param string $review_date Date of review (ISO 8601 format or date string)
 * @param int|null $rating_value Rating (1-5), null if no rating
 * @param string|null $item_reviewed What is being reviewed (default: Organization)
 * @return array Review schema
 */
function getReviewSchema($author_name, $review_text, $review_date, $rating_value = null, $item_reviewed = null) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Review",
        "author" => [
            "@type" => "Person",
            "name" => $author_name
        ],
        "reviewBody" => $review_text,
        "datePublished" => is_string($review_date) ? date('c', strtotime($review_date)) : $review_date,
        "inLanguage" => "ro-RO"
    ];
    
    // Add rating if provided
    if ($rating_value !== null && $rating_value >= 1 && $rating_value <= 5) {
        $schema["reviewRating"] = [
            "@type" => "Rating",
            "ratingValue" => (string)$rating_value,
            "bestRating" => "5",
            "worstRating" => "1"
        ];
    }
    
    // Add itemReviewed (what is being reviewed)
    if ($item_reviewed !== null) {
        $schema["itemReviewed"] = $item_reviewed;
    } else {
        // Default: review is about the Organization
        $schema["itemReviewed"] = [
            "@id" => JSONLD_BASE_URL . "#organization"
        ];
    }
    
    return $schema;
}

/**
 * Generate AggregateRating schema for Organization
 * Use this to show overall rating from Google Reviews
 * 
 * @param float $rating_value Average rating (e.g., 4.3)
 * @param int $review_count Total number of reviews
 * @param int $best_rating Best possible rating (default: 5)
 * @param int $worst_rating Worst possible rating (default: 1)
 * @return array AggregateRating schema
 */
function getAggregateRatingSchema($rating_value, $review_count, $best_rating = 5, $worst_rating = 1) {
    return [
        "@context" => "https://schema.org",
        "@type" => "AggregateRating",
        "ratingValue" => (string)$rating_value,
        "bestRating" => (string)$best_rating,
        "worstRating" => (string)$worst_rating,
        "ratingCount" => (string)$review_count
    ];
}

/**
 * Generate ReviewPage schema (for pages that display multiple reviews)
 * 
 * NOTE: For rich snippets, AggregateRating is usually enough.
 * You don't need to include all reviews in JSON-LD - keep them in HTML for users.
 * 
 * @param string $page_url Full URL of the reviews page
 * @param string $page_title Page title
 * @param array $reviews Array of Review schemas (optional, usually empty - AggregateRating is enough)
 * @param array|null $aggregate_rating AggregateRating schema (REQUIRED for rich snippets)
 * @return array ReviewPage schema
 */
function getReviewPageSchema($page_url, $page_title, $reviews = [], $aggregate_rating = null) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "WebPage",
        "@id" => $page_url . "#webpage",
        "url" => $page_url,
        "name" => $page_title,
        "isPartOf" => [
            "@id" => JSONLD_BASE_URL . "#website"
        ],
        "about" => [
            "@id" => JSONLD_BASE_URL . "#organization"
        ],
        "inLanguage" => "ro-RO",
        "publisher" => [
            "@id" => JSONLD_BASE_URL . "#organization"
        ]
    ];
    
    // Add aggregate rating if provided (THIS IS WHAT ENABLES RICH SNIPPETS)
    if ($aggregate_rating !== null) {
        $schema["aggregateRating"] = $aggregate_rating;
    }
    
    // Note: We don't include individual reviews here to keep JSON-LD small
    // All reviews are in HTML for users, but AggregateRating is enough for Google
    
    return $schema;
}

/**
 * Generate Google Review schema (for reviews imported from Google)
 * 
 * @param string $author_name Name of the reviewer
 * @param string $review_text Review content
 * @param string $review_date Date of review
 * @param int $rating_value Rating (1-5)
 * @param string|null $google_review_url URL to the Google review (optional)
 * @return array Review schema with Google-specific data
 */
function getGoogleReviewSchema($author_name, $review_text, $review_date, $rating_value, $google_review_url = null) {
    $schema = getReviewSchema($author_name, $review_text, $review_date, $rating_value);
    
    // Mark as Google review
    $schema["publisher"] = [
        "@type" => "Organization",
        "name" => "Google",
        "url" => "https://www.google.com"
    ];
    
    // Add source URL if provided
    if ($google_review_url !== null) {
        $schema["url"] = $google_review_url;
    }
    
    return $schema;
}

/**
 * Output JSON-LD script tag
 * 
 * @param array|array[] $schemas Single schema or array of schemas
 */
function outputJsonLd($schemas) {
    // If single schema, wrap in array
    if (isset($schemas['@context'])) {
        $schemas = [$schemas];
    }
    
    foreach ($schemas as $schema) {
        $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        echo '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }
}

/**
 * Output JSON-LD as string (for $header_scripts variable)
 * 
 * OPTIMIZED: Combines all schemas into a single script tag for better performance
 * Google accepts both multiple tags and a single tag with array - single is more efficient
 * 
 * @param array|array[] $schemas Single schema or array of schemas
 * @param bool $combine If true, combines all schemas into one script tag (default: true, recommended)
 * @return string HTML script tags
 */
function getJsonLdString($schemas, $combine = true) {
    // If single schema, wrap in array
    if (isset($schemas['@context'])) {
        $schemas = [$schemas];
    }
    
    // OPTIMIZATION: Combine all schemas into one script tag (recommended)
    if ($combine && count($schemas) > 1) {
        // Combine all schemas into a single JSON-LD script tag
        $json = json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }
    
    // Fallback: Separate script tags (if combine = false or only one schema)
    $output = '';
    foreach ($schemas as $schema) {
        $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $output .= '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }
    
    return $output;
}

