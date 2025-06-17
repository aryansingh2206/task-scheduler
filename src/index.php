<?php
require_once 'functions.php';

// Task management
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        addTask($_POST['task-name']);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['delete-task'])) {
        deleteTask($_POST['delete-task']);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['complete-task'])) {
        toggleTaskCompletion($_POST['complete-task']);
        header("Location: index.php");
        exit;
    }

    // Subscription form
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = addPendingSubscription($email);
            if (sendVerificationEmail($email, $code)) {
                echo "<p style='color:green;'>Verification email sent to <strong>$email</strong></p>";
            } else {
                echo "<p style='color:red;'>Failed to send verification email to <strong>$email</strong></p>";
            }
        } else {
            echo "<p style='color:red;'>Invalid email address.</p>";
        }
    }
}


$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Planner</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 50px;
    }

    .container {
      background: #ffffffdd;
      backdrop-filter: blur(8px);
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
      max-width: 600px;
      width: 100%;
    }

    h1 {
      text-align: center;
      margin-bottom: 1rem;
      color: #333;
    }

    form {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    input[type="text"],
    input[type="email"] {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    button {
      padding: 10px 16px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    button:hover {
      background: linear-gradient(135deg, #5a67d8, #6b46c1);
    }

    ul.tasks-list {
      list-style: none;
      padding: 0;
    }

    .task-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #f0f4ff;
      border-radius: 10px;
      padding: 10px;
      margin-bottom: 10px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .task-item.completed {
      text-decoration: line-through;
      color: gray;
      background: #e0e0e0;
    }

    .task-status {
      margin-right: 1rem;
    }

    .delete-task {
      background: #ff6b6b;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      color: white;
      cursor: pointer;
    }

    .delete-task:hover {
      background: #ff4c4c;
    }

    .email-form {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }

  </style>
</head>
<body>
  <div class="container">
    <h1>Task Planner</h1>

    <!-- Task Form -->
    <form method="POST">
      <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
      <button type="submit" id="add-task">Add Task</button>
    </form>

    <!-- Task List -->
    <ul class="tasks-list">
      <?php foreach ($tasks as $task): ?>
        <li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
          <input type="checkbox" class="task-status" data-id="<?php echo $task['id']; ?>" <?php echo $task['completed'] ? 'checked' : ''; ?>>
          <?php echo htmlspecialchars($task['name']); ?>
          <form method="POST" style="margin:0;">
            <input type="hidden" name="delete-task-id" value="<?php echo $task['id']; ?>">
            <button class="delete-task" type="submit">Delete</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Email Subscription -->
    <form method="POST" class="email-form">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit" id="submit-email">Subscribe</button>
    </form>
  </div>
</body>
</html>
