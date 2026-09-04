<?php
/**
 * Auth helpers for two SEPARATE login systems:
 *  - Citizen login  -> $_SESSION['user_id'], $_SESSION['user_name']
 *  - Admin login    -> $_SESSION['admin_id'], $_SESSION['admin_name']
 * config.php (session_start) must be included before this file.
 */

function isLoggedInUser() {
    return isset($_SESSION['user_id']);
}

function isLoggedInAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireUserLogin() {
    if (!isLoggedInUser()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdminLogin() {
    if (!isLoggedInAdmin()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * generateApplicationNumber() -> e.g. TIRRI-2026-4F82A1
 */
function generateApplicationNumber() {
    return 'TIRRI-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

/**
 * generateCertificateNumber() -> issued only when an application is approved
 * e.g. GPT/2026/93F1C2  - this is what citizens use on the Verify page.
 */
function generateCertificateNumber() {
    return 'GPT/' . date('Y') . '/' . strtoupper(bin2hex(random_bytes(3)));
}
