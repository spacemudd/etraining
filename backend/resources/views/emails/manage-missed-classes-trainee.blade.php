<html>
<head></head>
<body style="font-family: 'Courier New', monospace;">
⚠ غياب لأكثر من 3 مرات<br/><br/>
---<br/>
<br/>
الاسم: {{ $trainee->name }}<br/>
الهوية: {{ $trainee->identity_number }}<br/>
الرابط: {{ $trainee->show_url }}<br/>
<br/>
---<br/>
<br/>
سجل الغياب:<br/>
@foreach ($trainee->warnings as $warning)
    <ul>
        <li>{{ $warning->attendance_report_record->course_batch_session->starts_at->toDateString() }} - {{ $warning->attendance_report_record->course_batch_session->course->name_ar }}</li>
    </ul>
@endforeach
<br/>
<br/>
{{ now()->toDateTimeString() }}
</body>
</html>
