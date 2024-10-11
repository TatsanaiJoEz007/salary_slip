<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
    $_SESSION['lang'] = $lang; // Set the language in session
    setcookie('lang', $lang, time() + (86400 * 30), "/"); // Set the language in cookie
    echo json_encode(['success' => true]);
    exit;
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = 'th';
    $_SESSION['lang'] = $lang; // Set the default language in session
    setcookie('lang', $lang, time() + (86400 * 30), "/");
}

// Include translation file based on the language
require_once('th_eng.php');
?>
