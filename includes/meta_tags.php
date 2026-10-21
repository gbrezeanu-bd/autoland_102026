<?php
/**
 * Meta Tags Helper Functions
 * Provides SEO-optimized meta descriptions and keywords for all pages
 * 
 * @copyright (c) 2025, AutoLand Romania
 */

/**
 * Set meta tags for homepage
 */
function setHomePageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Parc auto din Iași, din 2007: mașini verificate în service autorizat, garanție în scris, finanțare și livrare în toată România. Cere o ofertă.";
    $meta_keywords = "dealer auto Iași, mașini second hand Iași, autoturisme second hand, autoutilitare, garanție auto 12 luni, leasing auto, finanțare auto, credit auto, înmatriculări auto, dealer autorizat, mașini verificate tehnic";
    $og_type = "website";
}

/**
 * Set meta tags for contact page
 */
function setContactPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Autoland România, Iași: str. Luca Arbore nr. 3 (incinta KIA Iași). Livrare în toată țara. Telefon și WhatsApp 0742 377 001, luni–vineri 10:00–18:00.";
    $meta_keywords = "contact dealer auto Iași, AutoLand România contact, dealer auto Strada Luca Arbore, telefon dealer auto, program dealer auto Iași";
    $og_type = "website";
}

/**
 * Set meta tags for car listings page
 */
function setListingsPageMeta($marca = null) {
    global $meta_description, $meta_keywords, $og_type;
    
    if ($marca) {
        $meta_description = "Stoc complet {$marca} second hand la AutoLand România, Iași. Mașini {$marca} verificate tehnic cu garanție 12 luni. Prețuri avantajoase, finanțare disponibilă. Descoperă ofertele noastre de {$marca} second hand. Livrare în toată România, garanție valabilă în orice service RAR.";
        $meta_keywords = "{$marca} second hand Iași, {$marca} second hand, mașini {$marca}, {$marca} cu garanție, {$marca} rate, dealer {$marca} Iași";
    } else {
        $meta_description = "Stoc complet de mașini second hand și autoutilitare la AutoLand România, Iași. Toate mărcile disponibile cu garanție 12 luni. Autoturisme și autoutilitare verificate tehnic. Prețuri competitive, finanțare și rate disponibile. Livrare în toată România, garanție valabilă în orice service RAR.";
        $meta_keywords = "stoc auto second hand, mașini second hand Iași, autoturisme second hand, autoutilitare second hand, toate mărcile, parc auto Iași";
    }
    $og_type = "website";
}

/**
 * Set meta tags for car detail page
 */
function setCarDetailMeta($car_name, $year, $price, $image_url = null, $db_keywords = null, $km = null) {
    global $meta_description, $meta_keywords, $og_type, $og_image;
    
    $km_text = $km ? ", " . number_format($km, 0, ',', '.') . " km," : "";
    $meta_description = "{$car_name} din {$year}{$km_text} la {$price} EUR. Mașină second hand verificată tehnic cu garanție 12 luni în scris. Disponibil la AutoLand România, Iași. Finanțare și înmatriculări disponibile. Contactează-ne pentru mai multe detalii. Livrare în toată România, garanție valabilă în orice service RAR.";
    
    // Use keywords from database if available, otherwise generate default ones
    if (!empty($db_keywords)) {
        $meta_keywords = $db_keywords;
    } else {
        $meta_keywords = "{$car_name} second hand, {$year}, mașini second hand Iași, {$car_name} cu garanție, {$car_name} rate";
    }
    
    $og_type = "product";
    
    if ($image_url) {
        $og_image = $image_url;
    }
}

/**
 * Set meta tags for leasing page
 */
function setLeasingPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Finanțare auto second hand pentru persoane fizice și firme: rate sau leasing, avans de la 0% în funcție de profil, 1–5 ani. Cere oferta online.";
    $meta_keywords = "leasing auto Iași, finanțare auto, credit auto, rate auto, leasing operațional, leasing financiar, credit cu avans zero, rate cu dobanda fixa, finanțare mașini second hand";
    $og_type = "website";
}

/**
 * Set meta tags for warranty page
 */
function setWarrantyPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Garanție auto de la 12 luni / 10.000 km până la 3 ani / 40.000 km, valabilă în orice service autorizat RAR din România. Vezi ce acoperă, cât costă și cum o folosești.";
    $meta_keywords = "garanție auto 12 luni, garanție mașini second hand, garanție extinsă auto, garanție în scris, dealer cu garanție Iași, mașini cu garanție";
    $og_type = "website";
}

/**
 * Set meta tags for registration page
 */
function setRegistrationPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Servicii complete de înmatriculare auto la AutoLand România, Iași. Înmatriculări permanente, provizorii, transcriere, radiere, schimbare permis. Rezolvăm toate actele auto rapid și profesional. Contactează-ne pentru detalii. Livrare în toată România, garanție valabilă în orice service RAR.";
    $meta_keywords = "înmatriculări auto Iași, servicii înmatriculare, acte auto, transcriere auto, radiere auto, permis circulație, înmatriculare provizorie, înmatriculare permanentă";
    $og_type = "website";
}

/**
 * Set meta tags for roadside assistance page
 */
function setRoadsideAssistancePageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Asistență rutieră Autoland România: 3 pachete anuale cu tractare, depanare și repornire baterie, în România sau și în UE. Tarife de la 45 EUR + TVA.";
    $meta_keywords = "asistență rutieră Iași, tractare auto, depanare auto, ajutor rutier 24/7, service mobil, intervenție urgență auto";
    $og_type = "website";
}

/**
 * Set meta tags for special offers page
 */
function setSpecialOffersPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Oferte speciale la mașini second hand - AutoLand România, Iași. Prețuri reduse, promoții exclusive, reduceri la autoturisme și autoutilitare. Garanție 12 luni inclusă. Profită acum de ofertele noastre limitate! Livrare în toată România, garanție valabilă în orice service RAR.";
    $meta_keywords = "oferte speciale auto, promoții mașini second hand, reduceri auto, mașini ieftine, oferte limitate auto Iași, prețuri reduse autoturisme";
    $og_type = "website";
}

/**
 * Set meta tags for request offer page
 */
function setRequestOfferPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Cere oferta ta personalizată de la AutoLand România, Iași. Completează formularul și primești ofertă rapidă pentru mașina dorită. Consiliere gratuită, soluții de finanțare și garanție inclusă. Contactează-ne astăzi! Livrare în toată România, garanție valabilă în orice service RAR.";
    $meta_keywords = "cerere ofertă auto, ofertă personalizată mașină, consiliere auto, ofertă rapidă, dealer auto Iași, contact dealer";
    $og_type = "website";
}

/**
 * Set meta tags for customer reviews page
 */
function setReviewsPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Păreri și recenzii de la clienții AutoLand România, Iași. Vezi ce spun cei care au cumpărat deja mașini de la noi. Recenzii reale, feedback autentic. Încredere și transparență - citește experiențele clienților noștri. Livrare în toată România, garanție valabilă în orice service RAR.";
    $meta_keywords = "recenzii AutoLand România, păreri clienți, feedback dealer auto, testimoniale clienți, recenzii mașini second hand Iași, opinii AutoLand";
    $og_type = "website";
}

