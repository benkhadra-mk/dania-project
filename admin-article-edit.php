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
        
        /* Icon Picker Styles */
        .icon-picker-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .icon-picker-btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .icon-picker-btn:hover {
            background: #3a7bc8;
            transform: translateY(-2px);
        }
        
        .selected-icon-preview {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem;
            background: var(--light);
            border-radius: 8px;
            min-width: 150px;
        }
        
        .selected-icon-preview i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .selected-icon-preview span {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        /* Icon Modal */
        .icon-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .icon-modal.active {
            display: flex;
        }
        
        .icon-modal-content {
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .icon-modal-header {
            padding: 1.5rem;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .icon-modal-header h3 {
            color: var(--primary);
            margin: 0;
        }
        
        .icon-modal-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: var(--gray);
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .icon-modal-close:hover {
            background: var(--light);
            color: var(--dark);
        }
        
        .icon-grid {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 1rem;
            overflow-y: auto;
            max-height: 60vh;
        }
        
        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .icon-item:hover {
            border-color: var(--secondary);
            background: rgba(74, 144, 226, 0.1);
            transform: translateY(-3px);
        }
        
        .icon-item.selected {
            border-color: var(--primary);
            background: rgba(46, 139, 87, 0.15);
        }
        
        .icon-item i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .icon-item span {
            font-size: 0.7rem;
            color: var(--gray);
            text-align: center;
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
                        <div class="icon-picker-container">
                            <button type="button" class="icon-picker-btn" id="iconPickerBtn">
                                <i class="fas fa-icons"></i>
                                <span>اختر أيقونة</span>
                            </button>
                            <div class="selected-icon-preview" id="iconPreview">
                                <?php if ($isEdit && !empty($article['icon'])): ?>
                                    <i class="fas <?php echo htmlspecialchars($article['icon']); ?>"></i>
                                    <span><?php echo htmlspecialchars($article['icon']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <input type="hidden" id="icon" name="icon" required value="<?php echo $isEdit ? htmlspecialchars($article['icon']) : ''; ?>">
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
    
    <!-- Icon Picker Modal -->
    <div class="icon-modal" id="iconModal">
        <div class="icon-modal-content">
            <div class="icon-modal-header">
                <h3>اختر أيقونة</h3>
                <button type="button" class="icon-modal-close" onclick="closeIconModal()">&times;</button>
            </div>
            <div class="icon-grid" id="iconGrid">
                <!-- Icons will be generated by JavaScript -->
            </div>
        </div>
    </div>
    
    <script>
        // Available health-related icons
        const availableIcons = [
            { class: 'fa-syringe', name: 'حقنة' },
            { class: 'fa-heartbeat', name: 'نبض القلب' },
            { class: 'fa-heart', name: 'قلب' },
            { class: 'fa-stethoscope', name: 'سماعة' },
            { class: 'fa-pills', name: 'حبوب' },
            { class: 'fa-capsules', name: 'كبسولات' },
            { class: 'fa-prescription-bottle', name: 'زجاجة دواء' },
            { class: 'fa-lungs', name: 'رئتان' },
            { class: 'fa-brain', name: 'دماغ' },
            { class: 'fa-bone', name: 'عظم' },
            { class: 'fa-tooth', name: 'سن' },
            { class: 'fa-eye', name: 'عين' },
            { class: 'fa-dna', name: 'DNA' },
            { class: 'fa-apple-alt', name: 'تفاحة' },
            { class: 'fa-carrot', name: 'جزر' },
            { class: 'fa-leaf', name: 'ورقة' },
            { class: 'fa-fish', name: 'سمك' },
            { class: 'fa-bacon', name: 'لحم' },
            { class: 'fa-cheese', name: 'جبن' },
            { class: 'fa-drumstick-bite', name: 'دجاج' },
            { class: 'fa-running', name: 'جري' },
            { class: 'fa-dumbbell', name: 'أوزان' },
            { class: 'fa-walking', name: 'مشي' },
            { class: 'fa-biking', name: 'دراجة' },
            { class: 'fa-medkit', name: 'حقيبة إسعاف' },
            { class: 'fa-ambulance', name: 'إسعاف' },
            { class: 'fa-hospital', name: 'مستشفى' },
            { class: 'fa-user-md', name: 'طبيب' },
            { class: 'fa-weight', name: 'وزن' },
            { class: 'fa-plus-circle', name: 'إضافة' },
            { class: 'fa-briefcase-medical', name: 'حقيبة طبية' },
            { class: 'fa-first-aid', name: 'إسعافات' }
        ];
        
        let selectedIcon = '<?php echo $isEdit ? htmlspecialchars($article['icon']) : ''; ?>';
        
        // Initialize icon grid
        function initIconGrid() {
            const iconGrid = document.getElementById('iconGrid');
            iconGrid.innerHTML = '';
            
            availableIcons.forEach(icon => {
                const iconItem = document.createElement('div');
                iconItem.className = 'icon-item';
                if (selectedIcon === icon.class) {
                    iconItem.classList.add('selected');
                }
                
                iconItem.innerHTML = `
                    <i class="fas ${icon.class}"></i>
                    <span>${icon.name}</span>
                `;
                
                iconItem.addEventListener('click', () => selectIcon(icon.class, icon.name));
                iconGrid.appendChild(iconItem);
            });
        }
        
        // Open icon modal
        document.getElementById('iconPickerBtn').addEventListener('click', () => {
            initIconGrid();
            document.getElementById('iconModal').classList.add('active');
        });
        
        // Close icon modal
        function closeIconModal() {
            document.getElementById('iconModal').classList.remove('active');
        }
        
        // Close modal when clicking outside
        document.getElementById('iconModal').addEventListener('click', (e) => {
            if (e.target.id === 'iconModal') {
                closeIconModal();
            }
        });
        
        // Select icon
        function selectIcon(iconClass, iconName) {
            selectedIcon = iconClass;
            
            // Update hidden input
            document.getElementById('icon').value = iconClass;
            
            // Update preview
            const preview = document.getElementById('iconPreview');
            preview.innerHTML = `
                <i class="fas ${iconClass}"></i>
                <span>${iconClass}</span>
            `;
            
            // Update grid selection
            document.querySelectorAll('.icon-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.target.closest('.icon-item').classList.add('selected');
            
            // Close modal
            setTimeout(() => closeIconModal(), 300);
        }
        
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
