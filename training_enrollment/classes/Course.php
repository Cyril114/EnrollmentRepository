<?php

class Course {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function all() {
    return $this->db->query("SELECT * FROM courses ORDER BY course_name")->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE course_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($code, $name, $description) {
        $stmt = $this->db->prepare(
            "INSERT INTO courses (course_code, course_name, description)
            VALUES (:code, :name, :d)");
        return $stmt->execute([':code' => $code, ':name' => $name, ':d' => $description]);
    }

    public function update($id, $code, $name, $description) {
        $stmt = $this->db->prepare(
            "UPDATE courses SET course_code = :code, course_name = :name,
            description = :d WHERE course_id = :id");
        return $stmt->execute([':code' => $code, ':name' => $name,
                           ':d' => $description, ':id' => $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE course_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}