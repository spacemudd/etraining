<?php
// التحديثات المطلوبة في app/Services/CompanyAttendanceReportService.php

// ===== التحديث الأول: دالة makePdf (حوالي السطر 279) =====
// استبدال هذا الكود:
/*
if (in_array($report->company->id, [
    '9ef83749-d1ba-44a5-82a9-f726840e02db',
    // ... باقي الشركات
])) {
    $view = 'pdf.company-attendance-report.special-company-simple';
} elseif (in_array($report->company->id, [
    '2ea73041-e686-4093-b830-260b488eb014',
])) {
    $view = 'pdf.company-attendance-report.special-company-modern';
} else {
    switch ($report->template_type ?? 'default') {
        // ...
    }
}
*/

// بهذا الكود:
switch ($report->template_type ?? 'default') {
    case 'simple':
        $view = 'pdf.company-attendance-report.special-company-simple';
        break;
    case 'modern':
        $view = 'pdf.company-attendance-report.special-company-modern';
        break;
    case 'mini':
        $view = 'pdf.company-attendance-report.special-company-mini';
        break;
    default:
        if (in_array($report->company->id, [
            '9ef83749-d1ba-44a5-82a9-f726840e02db',
            '92d30511-77a8-4290-8d20-419f93ede3fd',
            '19762266-e0fc-43e5-b6ae-b4deec886bb1',
            '73017d20-40c8-401f-8dc1-b36ca0416e35',
            '077e3421-a623-49f4-b3f2-dcf80c9d295f',
            'b455f112-ff48-4647-8db6-a3d365a3d0a3',
            '2d8b0e51-5ea6-4c4d-9c38-ec38429cb74e',
            '0e0e3d03-a9ad-4964-8c5a-6826cc5b0c6f',
        ])) {
            $view = 'pdf.company-attendance-report.special-company-simple';
        } elseif (in_array($report->company->id, [
            '2ea73041-e686-4093-b830-260b488eb014',
        ])) {
            $view = 'pdf.company-attendance-report.special-company-modern';
        } else {
            $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
        }
        break;
}

// ===== التحديث الثاني: دالة makeIndividualPdf (حوالي السطر 382) =====
// نفس التحديث للتقارير الفردية:

switch ($record->report->template_type ?? 'default') {
    case 'simple':
        $view = 'pdf.company-attendance-report.special-company-individual-simple';
        break;
    case 'modern':
        $view = 'pdf.company-attendance-report.special-company-individual-modern';
        break;
    case 'mini':
        $view = 'pdf.company-attendance-report.special-company-individual-mini';
        break;
    default:
        if (in_array($record->company->id, [
            '9ef83749-d1ba-44a5-82a9-f726840e02db',
            '92d30511-77a8-4290-8d20-419f93ede3fd',
            '19762266-e0fc-43e5-b6ae-b4deec886bb1',
            '73017d20-40c8-401f-8dc1-b36ca0416e35',
            '077e3421-a623-49f4-b3f2-dcf80c9d295f',
            'b455f112-ff48-4647-8db6-a3d365a3d0a3',
            '2d8b0e51-5ea6-4c4d-9c38-ec38429cb74e',
            '0e0e3d03-a9ad-4964-8c5a-6826cc5b0c6f',
        ])) {
            $view = 'pdf.company-attendance-report.special-company-individual-simple';
        } elseif (in_array($record->company->id, [
            '2ea73041-e686-4093-b830-260b488eb014',
        ])) {
            $view = 'pdf.company-attendance-report.special-company-individual-modern';
        } else {
            $view = 'pdf.company-attendance-report.individual-table';
        }
        break;
}
