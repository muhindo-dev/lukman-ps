<?php
/**
 * Admin Logout
 */
require_once 'config/auth.php';

logoutAdmin();

header('Location: login.php');
exit;
