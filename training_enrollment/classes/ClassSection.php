<?php

class ClassSection {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }
    
    public function allWithCourse() {
    return $this->db->query(
        "SELECT c.*, co.course_name FROM classes c
         JOIN courses co ON c.course_id = co.course_id
         ORDER BY co.course_name, c.class_code")->fetchAll();
    }

    public function create($course_id, $code, $schedule, $instructor, $slots) {
        $stmt = $this->db->prepare(
        "INSERT INTO classes (course_id, class_code, schedule, instructor, slots)
         VALUES (:cid, :code, :sch, :ins, :slots)");
        return $stmt->execute([':cid' => $course_id, ':code' => $code,
            ':sch' => $schedule, ':ins' => $instructor, ':slots' => $slots]);
    }

    public function getSlots($class_id) {
        $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :id");
        $stmt->execute([':id' => $class_id]);
        return (int) $stmt->fetchColumn();
    }
}
