<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "todolist_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task']) && isset($_POST['status'])) {
    $task = $_POST['task'];
    $status = $_POST['status'];
    $sql = "INSERT INTO tasks (task, status) VALUES ('$task', '$status')";
    $conn->query($sql);
    header("Location: index.php");
}

// Delete Task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM tasks WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
}

// Mark Task as Completed
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $sql = "UPDATE tasks SET status='Completed' WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
}

// Fetch tasks
$result = $conn->query("SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        :root {
            --primary: #6c63ff;
            --primary-light: #a5a1ff;
            --secondary: #4dabf7;
            --success: #40c057;
            --danger: #fa5252;
            --warning: #fab005;
            --light: #f8f9fa;
            --lighter: #f1f3f5;
            --lightest: #ffffff;
            --text: #495057;
            --text-light: #868e96;
            --border: #e9ecef;
            
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px rgba(0,0,0,0.05);
            --rounded-sm: 6px;
            --rounded: 8px;
            --rounded-lg: 12px;
            
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.5;
            color: var(--text);
            background-color: var(--lighter);
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--lightest);
            border-radius: var(--rounded-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .header {
            padding: 1.5rem;
            background: var(--lightest);
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .header h2 {
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .content {
            padding: 2rem;
        }

        .add-task-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .form-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--rounded-sm);
            font-size: 1rem;
            background: var(--lightest);
            transition: var(--transition);
            color: var(--text);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
        }

        .form-select {
            min-width: 150px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--rounded-sm);
            font-size: 1rem;
            background-color: var(--lightest);
            color: var(--text);
            transition: var(--transition);
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--rounded-sm);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .tasks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .tasks-table th {
            background-color: var(--light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        .tasks-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .tasks-table tr:last-child td {
            border-bottom: none;
        }

        .tasks-table tr:hover td {
            background-color: rgba(108, 99, 255, 0.03);
        }

        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: rgba(250, 82, 82, 0.1);
            color: var(--danger);
        }

        .status-inprogress {
            background-color: rgba(250, 176, 5, 0.1);
            color: var(--warning);
        }

        .status-completed {
            background-color: rgba(64, 192, 87, 0.1);
            color: var(--success);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: var(--text-light);
        }

        .empty-state p {
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .add-task-form {
                flex-direction: column;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>My To-Do List</h2>
    </div>
    
    <div class="content">
        <form method="POST" class="add-task-form">
            <input type="text" name="task" required placeholder="What needs to be done?" class="form-input">
            <select name="status" required class="form-select">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>

        <table class="tasks-table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['task']) ?></td>
                            <td>
                                <span class="status-badge <?= strtolower(str_replace(' ', '', $row['status'])) ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?complete=<?= $row['id'] ?>" class="btn btn-success btn-sm">Complete</a>
                                    <a href="index.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty-state">
                            <p>No tasks yet. Add your first task above!</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>