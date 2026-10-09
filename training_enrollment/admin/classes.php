<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$classes = new ClassSection($db);
$courses = (new Course($db))->all();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id  = (int) ($_POST['course_id'] ?? 0);
    $code       = trim($_POST['class_code'] ?? '');
    $schedule   = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots      = (int) ($_POST['slots'] ?? 0);

    if ($course_id <= 0 || $code === '' || $slots < 1) {
        $message = "Course, class code and at least 1 slot are required.";
    } else {
        $classes->create($course_id, $code, $schedule, $instructor, $slots);
        $message = "Class added.";
    }
}
$list = $classes->allWithCourse();
?>
<link rel="stylesheet" href="../css/style.css">
<h1>Manage Classes</h1>
<p><a href="../index.php">Home</a></p>
<?php if ($message) echo "<p>" . htmlspecialchars($message) . "</p>"; ?>

<form method="post">
  Course:
  <select name="course_id">
    <?php foreach ($courses as $c): ?>
      <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
    <?php endforeach; ?>
  </select><br>
  Class code: <input name="class_code" required><br>
  Schedule: <input name="schedule"><br>
  Instructor: <input name="instructor"><br>
  Slots: <input type="number" name="slots" min="1" required><br>
  <button type="submit">Add Class</button>
</form>

<table border="1" cellpadding="5">
  <tr><th>Course</th><th>Class</th><th>Schedule</th><th>Instructor</th><th>Slots left</th></tr>
  <?php foreach ($list as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['course_name']) ?></td>
    <td><?= htmlspecialchars($r['class_code']) ?></td>
    <td><?= htmlspecialchars($r['schedule']) ?></td>
    <td><?= htmlspecialchars($r['instructor']) ?></td>
    <td><?= $r['slots'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>