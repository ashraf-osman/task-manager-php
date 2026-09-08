<?php
class Task {
    private $id;
    private $title;
    private $description;
    private $done;
    private $createdAt;
    
    public function __construct($title, $description = '') {
        $this->title = $title;
        $this->description = $description;
        $this->done = false;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    // Getters و Setters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function isDone() { return $this->done; }
    public function getCreatedAt() { return $this->createdAt; }
    
    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setDone($done) { $this->done = $done; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'done' => $this->done,
            'created_at' => $this->createdAt
        ];
    }
}
?>