<?php
require_once 'functions.php';

// Load all pending tasks
$pendingTasks = getPendingTasks(); // This should return an array of uncompleted tasks

// Load all verified subscribers
$subscribers = loadSubscribers(); // This should return an array of emails

if (!$pendingTasks || !$subscribers) {
    exit; // Nothing to send
}

foreach ($subscribers as $email) {
    // Encode unsubscribe link
    $unsubscribeLink = "http://localhost:8000/unsubscribe.php?email=" . urlencode(base64_encode($email));

    // Construct email subject and headers
    $subject = "Task Planner - Pending Tasks Reminder";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    // Build the HTML body
    $body = "<h2>Pending Tasks Reminder</h2>";
    $body .= "<p>Here are the current pending tasks:</p>";
    $body .= "<ul>";
    foreach ($pendingTasks as $task) {
        $body .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }
    $body .= "</ul>";
    $body .= "<p><a id=\"unsubscribe-link\" href=\"$unsubscribeLink\">Unsubscribe from notifications</a></p>";

    // Send the email
    mail($email, $subject, $body, $headers);
}
