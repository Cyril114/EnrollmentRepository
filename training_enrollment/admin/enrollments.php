<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);

$message = '';
if (isset($_GET['cancel'])) {
    $message = $repo->cancel((int) $_GET['cancel'])
        ? "Enrollment cancelled." : "Could not cancel.";
}
$rows = $repo->allWithDetails();
?>
<link rel="stylesheet" href="../css/style.css">
<h1>Enrollments</h1>
<p><a href="../index.php">Home</a></p>
<?php if ($message) echo "<p>" . htmlspecialchars($message) . "</p>"; ?>
<table border="1" cellpadding="5">
  <tr><th>ID</th><th>Student</th><th>Course</th><th>Class</th>
      <th>Schedule</th><th>Status</th><th>Action</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= $r['enrollment_id'] ?></td>
    <td><?= htmlspecialchars($r['full_name']) ?></td>
    <td><?= htmlspecialchars($r['course_name']) ?></td>
    <td><?= htmlspecialchars($r['class_code']) ?></td>
    <td><?= htmlspecialchars($r['schedule']) ?></td>
    <td><?= $r['status'] ?></td>
    <td>
      <?php if ($r['status'] === 'active'): ?>
        <a href="?cancel=<?= $r['enrollment_id'] ?>"
           onclick="return confirm('Cancel this enrollment?')">Cancel</a>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>