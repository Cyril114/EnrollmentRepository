<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../classes/Student.php';

$repo = new EnrollmentRepository($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int) ($_POST['student_id'] ?? 0);
    $class_id = (int) ($_POST['class_id'] ?? 0);

    if ($student_id <= 0 || $class_id <= 0) {
        $message = "Please choose a student and a class.";
    } elseif ($repo->enroll($student_id, $class_id)) {
        $message = "Enrolled successfully!";
    } else {
        $message = "No slots available (or already enrolled).";
    }
}

$students = (new Student($db))->all();
$classList = (new ClassSection($db))->allWithCourse();
?>
<link rel="stylesheet" href="../css/style.css">
<h2>Enroll Existing Student</h2>
<p><a href="../index.php">Home</a></p>
<?php if ($message) echo "<p>" . htmlspecialchars($message) . "</p>"; ?>
<form method="post">
  Student:
  <select name="student_id">
    <?php foreach ($students as $s): ?>
      <option value="<?= $s['student_id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
    <?php endforeach; ?>
  </select><br>
  Class:
  <select name="class_id">
    <?php foreach ($classList as $c): ?>
      <option value="<?= $c['class_id'] ?>">
        <?= htmlspecialchars($c['course_name'] . ' - ' . $c['class_code']) ?> (<?= $c['slots'] ?> slots)
      </option>
    <?php endforeach; ?>
  </select><br>
  <button type="submit">Enroll</button>
</form>
