<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';

$course = new Course($db);
$message = '';
$edit = null;

if (isset($_GET['edit'])) $edit = $course->find((int) $_GET['edit']);

if (isset($_GET['delete'])) {
    try {
        $course->delete((int) $_GET['delete']);
        $message = "Course deleted.";
    } catch (PDOException $e) {
        $message = "Cannot delete: this course still has classes.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int) ($_POST['course_id'] ?? 0);
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($code === '' || $name === '') {
        $message = "Code and name are required.";
    } else {
        try {
            if ($id > 0) {
                $course->update($id, $code, $name, $desc);
                $message = "Course updated.";
            } else {
                $course->create($code, $name, $desc);
                $message = "Course added.";
            }
            $edit = null;
        } catch (PDOException $e) {
            $message = "Course code already exists.";
        }
    }
}
$courses = $course->all();
?>
<link rel="stylesheet" href="../css/style.css">
<h1>Manage Courses</h1>
<p><a href="../index.php">Home</a></p>
<?php if ($message) echo "<p>" . htmlspecialchars($message) . "</p>"; ?>

<form method="post">
  <input type="hidden" name="course_id" value="<?= $edit['course_id'] ?? 0 ?>">
  Code: <input name="course_code" value="<?= htmlspecialchars($edit['course_code'] ?? '') ?>" required><br>
  Name: <input name="course_name" value="<?= htmlspecialchars($edit['course_name'] ?? '') ?>" required><br>
  Description: <textarea name="description"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea><br>
  <button type="submit"><?= $edit ? 'Update' : 'Add' ?> Course</button>
</form>

<table border="1" cellpadding="5">
  <tr><th>Code</th><th>Name</th><th>Description</th><th>Actions</th></tr>
  <?php foreach ($courses as $c): ?>
  <tr>
    <td><?= htmlspecialchars($c['course_code']) ?></td>
    <td><?= htmlspecialchars($c['course_name']) ?></td>
    <td><?= htmlspecialchars($c['description']) ?></td>
    <td>
      <a href="?edit=<?= $c['course_id'] ?>">Edit</a> |
      <a href="?delete=<?= $c['course_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>