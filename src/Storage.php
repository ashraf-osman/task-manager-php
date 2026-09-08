<?php
class Storage {
    private $filePath;
    
    public function __construct($filePath = __DIR__ . '/../data/tasks.json') {
        $this->filePath = $filePath;
        $this->ensureDirectoryExists();
    }
    
    private function ensureDirectoryExists() {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new Exception('لا يمكن إنشاء مجلد البيانات: ' . $dir);
            }
        }
        
        // التأكد من صلاحيات الكتابة
        if (!is_writable($dir)) {
            throw new Exception('مجلد البيانات غير قابل للكتابة: ' . $dir);
        }
    }
    
    public function load() {
        if (!file_exists($this->filePath)) {
            // إنشاء ملف فارغ إذا لم يكن موجوداً
            $this->save([]);
            return [];
        }
        
        $content = file_get_contents($this->filePath);
        if ($content === false) {
            throw new Exception('لا يمكن قراءة ملف البيانات');
        }
        
        $data = json_decode($content, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // إذا كان الملف تالفاً، نبدأ من جديد
            $this->save([]);
            return [];
        }
        
        return is_array($data) ? $data : [];
    }
    
    public function save($tasks) {
        // التأكد من وجود المجلد
        $this->ensureDirectoryExists();
        
        $json = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new Exception('فشل تحويل البيانات إلى JSON');
        }
        
        if (file_put_contents($this->filePath, $json) === false) {
            throw new Exception('لا يمكن حفظ البيانات في الملف');
        }
        
        return true;
    }
}
?>