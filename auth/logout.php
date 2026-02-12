<?php
/**
 * Logout Page
 * File: auth/logout.php
 */

require_once __DIR__ . '/../lib/auth.php';

Auth::logout();
header('Location: /coba_kost/auth/login.php');
exit;
