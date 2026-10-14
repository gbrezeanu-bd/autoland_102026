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
    
    $meta_description = "AutoLand România - dealer auto second hand autorizat în Iași. Oferim mașini second hand și autoutilitare verificate tehnic, cu garanție 12 luni în scris. Servicii complete: finanțare auto (leasing, rate cu avans zero), înmatriculări, evaluare vehicule la schimb. Stoc permanent actualizat.";
    $meta_keywords = "dealer auto Iași, mașini second hand Iași, autoturisme second hand, autoutilitare, garanție auto 12 luni, leasing auto, finanțare auto, credit auto, înmatriculări auto, dealer autorizat, mașini verificate tehnic";
    $og_type = "website";
}

/**
 * Set meta tags for contact page
 */
function setContactPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Contactează AutoLand România - dealer auto second hand în Iași. Strada Luca Arbore nr. 3, Iași, 700440. Telefon: +40 742 377 001. Program: Luni-Vineri 09:30-17:30. Vino să vezi mașinile noastre sau sună pentru informații.";
    $meta_keywords = "contact dealer auto Iași, AutoLand România contact, dealer auto Strada Luca Arbore, telefon dealer auto, program dealer auto Iași";
    $og_type = "website";
}

/**
 * Set meta tags for car listings page
 */
function setListingsPageMeta($marca = null) {
    global $meta_description, $meta_keywords, $og_type;
    
    if ($marca) {
        $meta_description = "Stoc complet {$marca} second hand la AutoLand România, Iași. Mașini {$marca} verificate tehnic cu garanție 12 luni. Prețuri avantajoase, finanțare disponibilă. Descoperă ofertele noastre de {$marca} second hand.";
        $meta_keywords = "{$marca} second hand Iași, {$marca} second hand, mașini {$marca}, {$marca} cu garanție, {$marca} rate, dealer {$marca} Iași";
    } else {
        $meta_description = "Stoc complet de mașini second hand și autoutilitare la AutoLand România, Iași. Toate mărcile disponibile cu garanție 12 luni. Autoturisme și autoutilitare verificate tehnic. Prețuri competitive, finanțare și rate disponibile.";
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
    $meta_description = "{$car_name} din {$year}{$km_text} la {$price} EUR. Mașină second hand verificată tehnic cu garanție 12 luni în scris. Disponibil la AutoLand România, Iași. Finanțare și înmatriculări disponibile. Contactează-ne pentru mai multe detalii.";
    
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
    
    $meta_description = "Leasing și finanțare auto la AutoLand România, Iași. Soluții personalizate: leasing operațional, leasing financiar, credit auto cu avans zero, rate cu dobanda fixa. Finanțare rapidă pentru mașini second hand și autoutilitare. Contactează-ne pentru ofertă.";
    $meta_keywords = "leasing auto Iași, finanțare auto, credit auto, rate auto, leasing operațional, leasing financiar, credit cu avans zero, rate cu dobanda fixa, finanțare mașini second hand";
    $og_type = "website";
}

/**
 * Set meta tags for warranty page
 */
function setWarrantyPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Garanție auto extinsă de 12 luni în scris la toate mașinile second hand de la AutoLand România, Iași. Acoperire completă, transparență totală. Cumpără cu încredere - garanție reală și asistență post-vânzare.";
    $meta_keywords = "garanție auto 12 luni, garanție mașini second hand, garanție extinsă auto, garanție în scris, dealer cu garanție Iași, mașini cu garanție";
    $og_type = "website";
}

/**
 * Set meta tags for registration page
 */
function setRegistrationPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Servicii complete de înmatriculare auto la AutoLand România, Iași. Înmatriculări permanente, provizorii, transcriere, radiere, schimbare permis. Rezolvăm toate actele auto rapid și profesional. Contactează-ne pentru detalii.";
    $meta_keywords = "înmatriculări auto Iași, servicii înmatriculare, acte auto, transcriere auto, radiere auto, permis circulație, înmatriculare provizorie, înmatriculare permanentă";
    $og_type = "website";
}

/**
 * Set meta tags for roadside assistance page
 */
function setRoadsideAssistancePageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Asistență rutieră 24/7 de la AutoLand România, Iași. Tractare auto, depanare, ajutor tehnic de urgență. Intervenții rapide în caz de pană, accident sau problemă tehnică. Suntem aici pentru tine non-stop.";
    $meta_keywords = "asistență rutieră Iași, tractare auto, depanare auto, ajutor rutier 24/7, service mobil, intervenție urgență auto";
    $og_type = "website";
}

/**
 * Set meta tags for special offers page
 */
function setSpecialOffersPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Oferte speciale la mașini second hand - AutoLand România, Iași. Prețuri reduse, promoții exclusive, reduceri la autoturisme și autoutilitare. Garanție 12 luni inclusă. Profită acum de ofertele noastre limitate!";
    $meta_keywords = "oferte speciale auto, promoții mașini second hand, reduceri auto, mașini ieftine, oferte limitate auto Iași, prețuri reduse autoturisme";
    $og_type = "website";
}

/**
 * Set meta tags for request offer page
 */
function setRequestOfferPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Cere oferta ta personalizată de la AutoLand România, Iași. Completează formularul și primești ofertă rapidă pentru mașina dorită. Consiliere gratuită, soluții de finanțare și garanție inclusă. Contactează-ne astăzi!";
    $meta_keywords = "cerere ofertă auto, ofertă personalizată mașină, consiliere auto, ofertă rapidă, dealer auto Iași, contact dealer";
    $og_type = "website";
}

/**
 * Set meta tags for customer reviews page
 */
function setReviewsPageMeta() {
    global $meta_description, $meta_keywords, $og_type;
    
    $meta_description = "Păreri și recenzii de la clienții AutoLand România, Iași. Vezi ce spun cei care au cumpărat deja mașini de la noi. Recenzii reale, feedback autentic. Încredere și transparență - citește experiențele clienților noștri.";
    $meta_keywords = "recenzii AutoLand România, păreri clienți, feedback dealer auto, testimoniale clienți, recenzii mașini second hand Iași, opinii AutoLand";
    $og_type = "website";
}

