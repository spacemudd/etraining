<html>
<head></head>
<body style="font-family: 'Courier New', monospace;">
⚠ Trainee restored<br/><br/>
---<br/>
<br/>
Name: {{ $trainee->name }}<br/>
Email: {{ $trainee->email }}<br/>
Phone: {{ $trainee->phone }}<br/>
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
