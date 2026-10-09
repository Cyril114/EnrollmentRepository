<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$classes = new ClassSection($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = (int) ($_POST['class_id'] ?? 0);

    if ($full_name === '') {
        $message = "Name is required.";
    } elseif ($class_id <= 0) {
        $message = "Please select a class.";
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email.";
    } elseif ($repo->recordStudent($full_name, $email, $phone, $class_id)) {
        $message = "Student recorded and enrolled successfully!";
    } else {
        $message = "No slots available.";
    }
}
$classList = $classes->allWithCourse(); 
?>
<link rel="stylesheet" href="../css/style.css">
<h2>Record Student</h2>
<p><a href="../index.php">Home</a></p>
<?php if (!empty($message)) echo "<p>" . htmlspecialchars($message) . "</p>"; ?>
<form method="post">
  Name: <input type="text" name="full_name" required><br>
  Email: <input type="email" name="email"><br>
  Phone: <input type="text" name="phone"><br>
  Class:
  <select name="class_id">
    <option value="0">-- select --</option>
    <?php foreach ($classList as $c): ?>
      <option value="<?= $c['class_id'] ?>">
        <?= htmlspecialchars($c['course_name'] . ' - ' . $c['class_code']) ?>
        (<?= $c['slots'] ?> slots)
      </option>
    <?php endforeach; ?>
  </select><br>
  <button type="submit">Record</button>
</form>

