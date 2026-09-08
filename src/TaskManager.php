<?php
require_once 'Storage.php';

class TaskManager {
    private $storage;
    private $tasks;
    
    public function __construct() {
        $this->storage = new Storage();
        $this->tasks = $this->storage->load();
    }
    
    public function addTask($title, $description = '') {
        $task = [
            'id' => count($this->tasks) + 1,
            'title' => $title,
            'description' => $description,
            'done' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->tasks[] = $task;
        $this->storage->save($this->tasks);
        return $task;
    }
    
    public function listTasks($showDone = false) {
        if ($showDone) {
            return $this->tasks;
        }
        return array_filter($this->tasks, function($task) {
            return !$task['done'];
        });
    }
    
    public function markDone($id) {
        foreach ($this->tasks as &$task) {
            if ($task['id'] == $id) {
                $task['done'] = true;
                $this->storage->save($this->tasks);
                return true;
            }
        }
        return false;
    }
    
    public function deleteTask($id) {
        $this->tasks = array_filter($this->tasks, function($task) use ($id) {
            return $task['id'] != $id;
        });
        $this->tasks = array_values($this->tasks); // إعادة ترقيم
        $this->storage->save($this->tasks);
        return true;
    }
    
    public function getStatistics() {
        $total = count($this->tasks);
        $done = count(array_filter($this->tasks, function($t) { return $t['done']; }));
        $pending = $total - $done;
        return [
            'total' => $total,
            'done' => $done,
            'pending' => $pending
        ];
    }
}
?>