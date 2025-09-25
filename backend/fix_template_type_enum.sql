-- تحديث enum لإضافة 'mini' إلى template_type
ALTER TABLE company_attendance_reports 
MODIFY COLUMN template_type ENUM('default', 'simple', 'modern', 'mini') DEFAULT 'default';

-- تحديث التقرير المحدد لاستخدام القالب المضغوط
UPDATE company_attendance_reports 
SET template_type = 'mini' 
WHERE id = 129289;

-- عرض النتيجة للتأكد
SELECT id, number, template_type FROM company_attendance_reports WHERE id = 129289;
