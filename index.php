<?php
/**
 * Main Index Page
 * File: index.php
 */

require_once __DIR__ . '/lib/auth.php';

// Redirect berdasarkan status login dan role
if (Auth::check()) {
    $role = Auth::role();
    header("Location: /coba_kost/$role/dashboard.php");
} else {
    header('Location: /coba_kost/auth/login.php');
}
exit;
