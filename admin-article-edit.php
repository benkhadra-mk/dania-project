<?php
/**
 * Admin Article Create/Edit Page
 */

require_once 'config/session.php';
require_once 'config/database.php';
require_once 'utils/security.php';

requireAdmin();

$db = getDB();
$isEdit = isset($_GET['id']);
$article = null;

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM health_content WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $article = $stmt->fetch();
    
    if (!$article) {
        header('Location: admin-dashboard.php');
        exit();
    }
    
    // Parse JSON sections
    $article['sections'] = json_decode($article['content_sections'], true);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'تعديل المقال' : 'إضافة مقال جديد'; ?> - WellCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        
        :root {
            --primary: #2E8B57;
            --primary-light: #3CB371;
            --secondary: #4A90E2;
            --accent: #FF6B6B;
            --light: #F8F9FA;
            --dark: #343A40;
            --gray: #6C757D;
            --white: #FFFFFF;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            font-size: 1.8rem;
        }
        
        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }
        
        main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 2rem;
        }
        
        .form-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .form-section h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .list-items {
            margin-top: 0.5rem;
        }
        
        .list-item {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .list-item input {
            flex: 1;
        }
        
        .btn-remove {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-add {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 0.5rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .btn-secondary {
            background: var(--gray);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1 class="logo"><?php echo $isEdit ? 'تعديل المقال' : 'إضافة مقال جديد'; ?></h1>
            <a href="admin-dashboard.php" class="btn-back">
                <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
            </a>
        </div>
    </header>
    
    <main>
        <form id="articleForm">
            <input type="hidden" id="articleId" value="<?php echo $isEdit ? $article['id'] : ''; ?>">
            
            <!-- Basic Info -->
            <div class="form-section">
                <h2><i class="fas fa-info-circle"></i> المعلومات الأساسية</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>العنوان *</label>
                        <input type="text" id="title" required value="<?php echo $isEdit ? htmlspecialchars($article['title']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>الرمز (Slug) *</label>
                        <input type="text" id="slug" required value="<?php echo $isEdit ? htmlspecialchars($article['slug']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>النوع *</label>
                        <select id="type" required>
                            <option value="">اختر النوع</option>
                            <option value="disease" <?php echo ($isEdit && $article['type'] === 'disease') ? 'selected' : ''; ?>>مرض</option>
                            <option value="diet" <?php echo ($isEdit && $article['type'] === 'diet') ? 'selected' : ''; ?>>نظام غذائي</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>الأيقونة (Font Awesome) *</label>
                        <input type="text" id="icon" required placeholder="مثال: fa-syringe" value="<?php echo $isEdit ? htmlspecialchars($article['icon']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>الوصف القصير</label>
                    <textarea id="short_description"><?php echo $isEdit ? htmlspecialchars($article['short_description']) : ''; ?></textarea>
                </div>
            </div>
            
            <!-- Content Sections -->
            <div class="form-section">
                <h2><i class="fas fa-file-alt"></i> المحتوى</h2>
                
                <div class="form-group">
                    <label>التعريف</label>
                    <textarea id="definition"><?php echo $isEdit && isset($article['sections']['definition']) ? htmlspecialchars($article['sections']['definition']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>الأنواع</label>
                    <div class="list-items" id="typesList">
                        <?php 
                        if ($isEdit && isset($article['sections']['types'])) {
                            foreach ($article['sections']['types'] as $type) {
                                echo '<div class="list-item">
                                    <input type="text" class="type-item" value="' . htmlspecialchars($type) . '">
                                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addListItem('typesList', 'type-item')">
                        <i class="fas fa-plus"></i> إضافة نوع
                    </button>
                </div>
                
                <div class="form-group">
                    <label>الأعراض</label>
                    <div class="list-items" id="symptomsList">
                        <?php 
                        if ($isEdit && isset($article['sections']['symptoms'])) {
                            foreach ($article['sections']['symptoms'] as $symptom) {
                                echo '<div class="list-item">
                                    <input type="text" class="symptom-item" value="' . htmlspecialchars($symptom) . '">
                                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addListItem('symptomsList', 'symptom-item')">
                        <i class="fas fa-plus"></i> إضافة عرض
                    </button>
                </div>
                
                <div class="form-group">
                    <label>طرق الوقاية</label>
                    <div class="list-items" id="preventionList">
                        <?php 
                        if ($isEdit && isset($article['sections']['prevention'])) {
                            foreach ($article['sections']['prevention'] as $prevention) {
                                echo '<div class="list-item">
                                    <input type="text" class="prevention-item" value="' . htmlspecialchars($prevention) . '">
                                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    <button type="button" class="btn-add" onclick="addListItem('preventionList', 'prevention-item')">
                        <i class="fas fa-plus"></i> إضافة طريقة وقاية
                    </button>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="admin-dashboard.php" class="btn-secondary">إلغاء</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'تحديث المقال' : 'حفظ المقال'; ?>
                </button>
            </div>
        </form>
    </main>
    
    <script>
        function addListItem(containerId, className) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <input type="text" class="${className}">
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(div);
        }
        
        document.getElementById('articleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Gather form data
            const id = document.getElementById('articleId').value;
            const types = Array.from(document.querySelectorAll('.type-item')).map(i => i.value).filter(v => v);
            const symptoms = Array.from(document.querySelectorAll('.symptom-item')).map(i => i.value).filter(v => v);
            const prevention = Array.from(document.querySelectorAll('.prevention-item')).map(i => i.value).filter(v => v);
            
            const data = {
                id: id || null,
                title: document.getElementById('title').value,
                slug: document.getElementById('slug').value,
                type: document.getElementById('type').value,
                icon: document.getElementById('icon').value,
                short_description: document.getElementById('short_description').value,
                content_sections: {
                    definition: document.getElementById('definition').value,
                    types: types,
                    symptoms: symptoms,
                    prevention: prevention
                }
            };
            
            try {
                const response = await fetch('/dania-project/api/admin/save-article.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    window.location.href = 'admin-dashboard.php';
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('حدث خطأ أثناء الحفظ');
            }
        });
    </script>
</body>
</html>
