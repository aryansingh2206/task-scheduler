<?php

// ✅ Add a new task if it's not a duplicate
function addTask($task_name) {
    $file = __DIR__ . '/tasks.txt';

    // Load existing tasks or initialize empty array
    $tasks = json_decode(file_get_contents($file), true) ?? [];

    // Check for duplicates (case-insensitive)
    foreach ($tasks as $task) {
        if (strtolower($task['name']) === strtolower($task_name)) {
            return false; // Duplicate task
        }
    }

    // Create and add new task
    $tasks[] = [
        "id" => uniqid(),
        "name" => $task_name,
        "completed" => false
    ];

    // Save updated tasks to file
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
    return true;
}

// ✅ Get all tasks from tasks.txt
function getAllTasks() {
    $file = __DIR__ . '/tasks.txt';

    // Return empty array if file doesn't exist
    if (!file_exists($file)) return [];

    // Load and decode JSON
    $tasks = json_decode(file_get_contents($file), true);
    return is_array($tasks) ? $tasks : [];
}

// 🔜 Placeholders for next features (to be implemented step-by-step)

function markTaskAsCompleted($task_id, $is_completed) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = json_decode(file_get_contents($file), true) ?? [];

    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = (bool)$is_completed;
            break;
        }
    }

    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
}


function deleteTask($task_id) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = json_decode(file_get_contents($file), true) ?? [];

    // Filter out the task to be deleted
    $updatedTasks = array_filter($tasks, function($task) use ($task_id) {
        return $task['id'] !== $task_id;
    });

    // Save updated tasks
    file_put_contents($file, json_encode(array_values($updatedTasks), JSON_PRETTY_PRINT));
}


function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function subscribeEmail($email) {
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $pending = file_exists($pendingFile) ? json_decode(file_get_contents($pendingFile), true) : [];

    $code = generateVerificationCode();
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];

    file_put_contents($pendingFile, json_encode($pending, JSON_PRETTY_PRINT));

    // Send verification email
    $verification_link = "http://localhost:8000/verify.php?email=" . urlencode($email) . "&code=$code";
    $subject = "Verify subscription to Task Planner";
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html\r\n";
    $body = "
    <p>Click the link below to verify your subscription to Task Planner:</p>
    <p><a id=\"verification-link\" href=\"$verification_link\">Verify Subscription</a></p>";

    mail($email, $subject, $body, $headers);
}

function verifySubscription($email, $code) {
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $subscriberFile = __DIR__ . '/subscribers.txt';

    $pending = file_exists($pendingFile) ? json_decode(file_get_contents($pendingFile), true) : [];

    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) {
        return false;
    }

    // Add to subscribers
    $subscribers = file_exists($subscriberFile) ? json_decode(file_get_contents($subscriberFile), true) : [];
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
    }

    // Save updated list
    file_put_contents($subscriberFile, json_encode($subscribers, JSON_PRETTY_PRINT));

    // Remove from pending
    unset($pending[$email]);
    file_put_contents($pendingFile, json_encode($pending, JSON_PRETTY_PRINT));

    return true;
}

function unsubscribeEmail($email) {
    $subscriberFile = __DIR__ . '/subscribers.txt';
    $subscribers = file_exists($subscriberFile) ? json_decode(file_get_contents($subscriberFile), true) : [];

    $subscribers = array_filter($subscribers, function($sub) use ($email) {
        return $sub !== $email;
    });

    file_put_contents($subscriberFile, json_encode(array_values($subscribers), JSON_PRETTY_PRINT));
}

function sendTaskReminders() {
    $subscriberFile = __DIR__ . '/subscribers.txt';
    $subscribers = file_exists($subscriberFile) ? json_decode(file_get_contents($subscriberFile), true) : [];
    $tasks = getAllTasks();

    $pending_tasks = array_filter($tasks, fn($t) => !$t['completed']);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

function sendTaskEmail($email, $pending_tasks) {
    $subject = "Task Planner - Pending Tasks Reminder";
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html\r\n";

    $unsubscribe_link = "http://localhost:8000/unsubscribe.php?email=" . urlencode($email);

    $task_items = "";
    foreach ($pending_tasks as $task) {
        $task_items .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }

    $body = "
    <h2>Pending Tasks Reminder</h2>
    <p>Here are the current pending tasks:</p>
    <ul>$task_items</ul>
    <p><a id=\"unsubscribe-link\" href=\"$unsubscribe_link\">Unsubscribe from notifications</a></p>";

    mail($email, $subject, $body, $headers);
}


function addPendingSubscription($email) {
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $pending = [];

    if (file_exists($pendingFile)) {
        $pending = json_decode(file_get_contents($pendingFile), true);
    }

    $code = generateVerificationCode();
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];

    file_put_contents($pendingFile, json_encode($pending, JSON_PRETTY_PRINT));
    return $code;
}

function sendVerificationEmail($email, $code) {
    $encodedEmail = urlencode(base64_encode($email));
    $verificationLink = "http://localhost:8000/verify.php?email=$encodedEmail&code=$code";

    $subject = "Verify subscription to Task Planner";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    $message = '
    <p>Click the link below to verify your subscription to Task Planner:</p>
    <p><a id="verification-link" href="' . $verificationLink . '">Verify Subscription</a></p>
    ';

    return mail($email, $subject, $message, $headers);
}

function verifyEmail($email, $code) {
    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $subscribersFile = __DIR__ . '/subscribers.txt';

    if (!file_exists($pendingFile)) return false;

    $pending = json_decode(file_get_contents($pendingFile), true);

    if (!isset($pending[$email])) return false;
    if ($pending[$email]['code'] !== $code) return false;

    unset($pending[$email]);
    file_put_contents($pendingFile, json_encode($pending, JSON_PRETTY_PRINT));

    if (!file_exists('subscribers.txt')) {
    file_put_contents('subscribers.txt', json_encode([]));
}


    $subscribers = [];
    if (file_exists($subscribersFile)) {
        $subscribers = json_decode(@file_get_contents('subscribers.txt'), true) ?: [];
 }

    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        file_put_contents($subscribersFile, json_encode($subscribers, JSON_PRETTY_PRINT));
    }

    return true;
}
function getPendingTasks() {
    $tasks = loadTasks(); // Make sure loadTasks() is already defined
    if (!$tasks || !is_array($tasks)) return [];

    $pending = array_filter($tasks, function ($task) {
        return !$task['completed'];
    });

    return array_values($pending); // Re-index the array
}
function loadTasks() {
    $file = __DIR__ . '/tasks.txt';
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $data = file_get_contents($file);
    $tasks = json_decode($data, true);

    return is_array($tasks) ? $tasks : [];
}
function loadSubscribers() {
    $file = __DIR__ . '/subscribers.txt';

    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $data = file_get_contents($file);
    $subscribers = json_decode($data, true);

    return is_array($subscribers) ? $subscribers : [];
}
function loadJson($filename) {
    if (!file_exists($filename)) {
        file_put_contents($filename, json_encode([]));
    }

    $data = file_get_contents($filename);
    $decoded = json_decode($data, true);

    return is_array($decoded) ? $decoded : [];
}
function saveJson($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}
