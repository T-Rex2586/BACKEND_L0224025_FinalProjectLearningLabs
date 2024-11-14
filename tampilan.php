<?php
$host = 'localhost';
$dbname = 'todo_app';
$user = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

if (isset($_POST['addTask'])) {
    $taskText = trim($_POST['taskInput']);
    if ($taskText !== '' && strlen($taskText) <= 255) {
        $stmt = $pdo->prepare("INSERT INTO tasks (text) VALUES (:text)");
        $stmt->execute(['text' => $taskText]);
    }
}

if (isset($_POST['completeTask'])) {
    $id = $_POST['taskId'];
    $stmt = $pdo->prepare("UPDATE tasks SET completed = NOT completed WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

if (isset($_POST['editTask'])) {
    $id = $_POST['taskId'];
    $newText = trim($_POST['taskText']);
    if ($newText !== '' && strlen($newText) <= 255) {
        $stmt = $pdo->prepare("UPDATE tasks SET text = :text WHERE id = :id");
        $stmt->execute(['text' => $newText, 'id' => $id]);
    }
}

if (isset($_POST['deleteTask'])) {
    $id = $_POST['taskId'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

$stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>
        <form method="POST" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <input type="text" name="taskInput" placeholder="Add a new task..." maxlength="255" required>
            <button type="submit" name="addTask">Add Task</button>
        </form>

        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task['completed'] ? 'completed' : '' ?>">
                    <span><?= htmlspecialchars($task['text']) ?></span>

                    <div class="task-actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="taskId" value="<?= $task['id'] ?>">
                            <button type="submit" name="completeTask"><?= $task['completed'] ? 'Undo' : 'Complete' ?></button>
                        </form>
                        <button type="button" class="edit" onclick="enableEditForm(<?= $task['id'] ?>, '<?= htmlspecialchars($task['text'], ENT_QUOTES) ?>')">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="taskId" value="<?= $task['id'] ?>">
                            <button type="submit" name="deleteTask" class="delete">Delete</button>
                        </form>
                    </div>

                    <!-- Edit Form -->
                    <form method="POST" class="edit-form" id="editForm<?= $task['id'] ?>">
                        <input type="hidden" name="taskId" value="<?= $task['id'] ?>">
                        <input type="text" name="taskText" class="edit-task-input" id="taskText<?= $task['id'] ?>" maxlength="255" required>
                        <button type="submit" name="editTask">Save</button>
                        <button type="button" onclick="disableEditForm(<?= $task['id'] ?>)">Cancel</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="script.js"></script> 
</body>
</html>