// public/js/main.js
const API_URL = '../api/tasks.php';

// إضافة مهمة جديدة
async function addTask() {
    const input = document.getElementById('taskInput');
    const title = input.value.trim();
    
    if (!title) {
        alert('⚠️ الرجاء إدخال عنوان المهمة');
        return;
    }
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                action: 'add', 
                title: title 
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('❌ حدث خطأ: ' + (result.error || 'غير معروف'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم');
    }
}

// إنهاء مهمة
async function markDone(id) {
    if (!confirm('هل تريد إنهاء هذه المهمة؟')) {
        return;
    }
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                action: 'done', 
                id: id 
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('❌ حدث خطأ أثناء إنهاء المهمة');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم');
    }
}

// حذف مهمة
async function deleteTask(id) {
    if (!confirm('⚠️ هل أنت متأكد من حذف هذه المهمة؟')) {
        return;
    }
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                action: 'delete', 
                id: id 
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('❌ حدث خطأ أثناء حذف المهمة');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم');
    }
}

// عرض كل المهام
async function toggleAllTasks() {
    const container = document.getElementById('allTasks');
    const btn = document.getElementById('showAllBtn');
    
    if (container.style.display === 'none' || container.style.display === '') {
        try {
            const response = await fetch(API_URL + '?action=all');
            const tasks = await response.json();
            
            if (tasks.length === 0) {
                container.innerHTML = '<p style="text-align:center;color:#888;padding:20px;">📭 لا توجد مهام</p>';
            } else {
                container.innerHTML = tasks.map(task => `
                    <div class="task-item ${task.done ? 'done-task' : ''}">
                        <span class="task-title">${escapeHtml(task.title)}</span>
                        <span class="task-date">📅 ${task.created_at}</span>
                        <span>${task.done ? '✅ منتهية' : '⏳ معلقة'}</span>
                    </div>
                `).join('');
            }
            
            container.style.display = 'block';
            btn.textContent = '🔽 إخفاء المهام';
        } catch (error) {
            console.error('Error:', error);
            alert('❌ حدث خطأ في جلب المهام');
        }
    } else {
        container.style.display = 'none';
        btn.textContent = '📋 عرض كل المهام';
    }
}

// دالة مساعدة لتجنب XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// إضافة حدث الضغط على Enter
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('taskInput');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                addTask();
            }
        });
    }
});