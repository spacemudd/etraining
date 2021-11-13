<html>
<head></head>
<body style="font-family: 'Courier New', monospace;">
⚠ Trainee restored<br/><br/>
---<br/>
<br/>
Name: {{ $trainee_name }}<br/>
Email: {{ $trainee_email }}<br/>
Phone: {{ $trainee_phone }}<br/>
Remark: {{ $block_reason }}<br/>
<br/>
---<br/>
<br/>
Action done by: {{ $done_by->name }} / {{ $done_by->email }}
<br/>
<br/>
{{ now()->toDateTimeString() }}
</body>
</html>
