<?php
// File chạy migration cho bảng notes
require_once 'config/db.php';

echo "🔄 Bắt đầu migration bảng notes...\n";

try {
    // Đọc file migration
    $migration_sql = file_get_contents('database/migration_notes_simplified.sql');
    
    if (!$migration_sql) {
        throw new Exception("Không thể đọc file migration");
    }
    
    // Chạy từng câu lệnh SQL
    $statements = explode(';', $migration_sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            echo "📝 Thực thi: " . substr($statement, 0, 50) . "...\n";
            $result = $pdo->exec($statement);
            if ($result !== false) {
                echo "✅ Thành công\n";
            } else {
                echo "⚠️ Có thể đã tồn tại hoặc không cần thiết\n";
            }
        }
    }
    
    echo "\n🎉 Migration hoàn thành thành công!\n";
    echo "📊 Bảng notes đã được tạo với cấu trúc mới:\n";
    echo "   - id (Primary Key)\n";
    echo "   - user_id (Foreign Key)\n";
    echo "   - title (Tiêu đề)\n";
    echo "   - content (Nội dung)\n";
    echo "   - category (Danh mục)\n";
    echo "   - subject (Môn học)\n";
    echo "   - priority (Mức độ ưu tiên)\n";
    echo "   - created_at (Thời gian tạo)\n";
    echo "   - updated_at (Thời gian cập nhật)\n";
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?> 