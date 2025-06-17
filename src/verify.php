<?php
require_once 'functions.php';

if (!isset($_GET['email']) || !isset($_GET['code'])) {
    die("Invalid verification link.");
}

$email = base64_decode(urldecode($_GET['email']));
$code = $_GET['code'];

if (verifyEmail($email, $code)) {
    echo "<h2>Subscription verified successfully!</h2>";
} else {
    echo "<h2>Verification failed. Link may be invalid or expired.</h2>";
}
?>
