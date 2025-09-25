<?php
// التحديثات المطلوبة في app/Http/Controllers/Back/CompanyAttendanceReportController.php

// ===== التحديث الأول: دالة updateTemplate =====
// البحث عن هذا السطر:
// 'template_type' => 'required|in:default,simple,modern',

// واستبداله بهذا:
'template_type' => 'required|in:default,simple,modern,mini',

// ===== التحديث الثاني: دالة getTemplateName =====
// البحث عن هذا الكود:
/*
switch ($templateType) {
    case 'simple':
        return 'القالب المبسط';
    case 'modern':
        return 'القالب الحديث';
    default:
        return 'القالب الافتراضي';
}
*/

// واستبداله بهذا:
switch ($templateType) {
    case 'simple':
        return 'القالب المبسط';
    case 'modern':
        return 'القالب الحديث';
    case 'mini':
        return 'القالب المضغوط';
    default:
        return 'القالب الافتراضي';
}
