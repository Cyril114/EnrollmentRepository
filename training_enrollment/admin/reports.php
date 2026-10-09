<?php
require_once __DIR__ . '/../config/db.php';

$report = $db->getRows(
    "SELECT co.course_name, c.class_code, c.slots AS slots_left,
            COUNT(CASE WHEN e.status = 'active' THEN 1 END) AS active_enrollments,
            COUNT(CASE WHEN e.status = 'cancelled' THEN 1 END) AS cancelled
     FROM classes c
     JOIN courses co ON c.course_id = co.course_id
     LEFT JOIN enrollments e ON e.class_id = c.class_id
     GROUP BY c.class_id, co.course_name, c.class_code, c.slots
     ORDER BY co.course_name");
?>
<link rel="stylesheet" href="../css/style.css">
<h1>Enrollment Summary</h1>
<p><a href="../index.php">Home</a></p>
<table border="1" cellpadding="5">
  <tr><th>Course</th><th>Class</th><th>Active</th><th>Cancelled</th><th>Slots left</th></tr>
  <?php foreach ($report as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['course_name']) ?></td>
    <td><?= htmlspecialchars($r['class_code']) ?></td>
    <td><?= $r['active_enrollments'] ?></td>
    <td><?= $r['cancelled'] ?></td>
    <td><?= $r['slots_left'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>