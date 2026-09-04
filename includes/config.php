<?php
/**
 * Gram Panchayat Tirri - Core config
 * Handles session start + bilingual (Marathi/English) language switching.
 */
session_start();

// Change language when ?lang=mr or ?lang=en is passed, then remember it in session
if (isset($_GET['lang']) && in_array($_GET['lang'], ['mr', 'en'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Default language = Marathi
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'mr';
}

$lang = $_SESSION['lang'];

/**
 * tr($marathi, $english)
 * Returns the correct string for the currently active language.
 * Usage: <?php echo tr('मराठी मजकूर', 'English text'); ?>
 */
function tr($mr, $en) {
    global $lang;
    return $lang === 'en' ? $en : $mr;
}

// Site-wide constants - edit these once, they update everywhere
define('SITE_NAME_MR', 'ग्रामपंचायत तिर्री');
define('SITE_NAME_EN', 'Gram Panchayat Tirri');
define('SITE_ADDRESS_MR', 'तिर्री, पोस्ट - मिन्शी, तालुका - पवनी, जिल्हा - भंडारा, महाराष्ट्र');
define('SITE_ADDRESS_EN', 'Tirri, Post Minshi, Taluka Pawani, District Bhandara, Maharashtra');
define('SITE_PHONE', '+91 XXXXX XXXXX');
define('SITE_EMAIL', 'grampanchayat.tirri@example.com');
