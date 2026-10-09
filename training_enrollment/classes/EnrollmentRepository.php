<?php
    class EnrollmentRepository {
        private $db;
        public function __construct(PDO $db) { $this->db = $db; }

        public function recordStudent($full_name, $email, $phone, $class_id) {
        try {
            $this->db->beginTransaction();

        
            $stmt = $this->db->prepare(
                "SELECT slots FROM classes WHERE class_id = :id FOR UPDATE");
            $stmt->execute([':id' => $class_id]);
            $slots = $stmt->fetchColumn();
            if ($slots === false || $slots < 1) {
                $this->db->rollBack();
                return false;               
            }

        
            $stmt = $this->db->prepare(
                "INSERT INTO students (full_name, email, phone) VALUES (:n, :e, :p)");
            $stmt->execute([':n' => $full_name, ':e' => $email, ':p' => $phone]);
            $student_id = $this->db->lastInsertId();

        
            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id) VALUES (:s, :c)");
            $stmt->execute([':s' => $student_id, ':c' => $class_id]);

        
            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots - 1 WHERE class_id = :c");
            $stmt->execute([':c' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function enroll($student_id, $class_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "SELECT slots FROM classes WHERE class_id = :id FOR UPDATE");
            $stmt->execute([':id' => $class_id]);
            $slots = $stmt->fetchColumn();
            if ($slots === false || $slots < 1) {
                $this->db->rollBack();
                return false;
            }

        
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM enrollments
                WHERE student_id = :s AND class_id = :c AND status = 'active'");
            $stmt->execute([':s' => $student_id, ':c' => $class_id]);
            if ($stmt->fetchColumn() > 0) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id) VALUES (:s, :c)");
            $stmt->execute([':s' => $student_id, ':c' => $class_id]);

            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots - 1 WHERE class_id = :c");
            $stmt->execute([':c' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    public function cancel($enrollment_id) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "SELECT class_id FROM enrollments
                WHERE enrollment_id = :id AND status = 'active'");
            $stmt->execute([':id' => $enrollment_id]);
            $class_id = $stmt->fetchColumn();
            if ($class_id === false) {      
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare(
                "UPDATE enrollments SET status = 'cancelled' WHERE enrollment_id = :id");
            $stmt->execute([':id' => $enrollment_id]);

            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots + 1 WHERE class_id = :c");
            $stmt->execute([':c' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

public function allWithDetails() {
    return $this->db->query(
        "SELECT e.enrollment_id, e.enrollment_date, e.status,
                s.full_name, s.email,
                c.class_code, c.schedule, c.instructor,
                co.course_name
         FROM enrollments e
         JOIN students s  ON e.student_id = s.student_id
         JOIN classes c   ON e.class_id = c.class_id
         JOIN courses co  ON c.course_id = co.course_id
         ORDER BY e.enrollment_date DESC")->fetchAll();
    }
}