<?php
require_once __DIR__ . '/functions.php';


if (!isset($_GET['email'])) {
    echo "Invalid request.";
    exit;
}

$email = base64_decode($_GET['email']);

if (!$email) {
    echo "Invalid email.";
    exit;
}

// Load current subscribers
$subscribers = loadJson('subscribers.txt');

// If not already empty, remove and save
if (is_array($subscribers)) {
    $updatedSubscribers = array_values(array_filter($subscribers, function ($e) use ($email) {
        return $e !== $email;
    }));

    saveJson('subscribers.txt', $updatedSubscribers);
}

echo "You have been unsubscribed successfully.";
