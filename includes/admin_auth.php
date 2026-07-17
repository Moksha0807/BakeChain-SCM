<?php
/**
 * BakeChain SCM — Admin Authentication Guard
 * Include this at the very top of every admin sub-page.
 * It starts the session, verifies the user is logged in as admin,
 * and redirects to login if not.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cookie_scm/auth/login.php");
    exit();
}
