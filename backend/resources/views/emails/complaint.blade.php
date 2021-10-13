<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
</head>
<body style="text-align:right;">
<h5>:اسم الدورة</h5>
<p>{{ $complaint->course_name }}</p>
<h5>:اسم المتدربة</h5>
<p>{{ $complaint->course_instructor }}</p>
<h5>:الرسالة</h5>
<p>{{ $complaint->message }}</p>
<h5>:من قبل</h5>
<p>{{ $complaint->user->name }} // {{ $complaint->user->phone }} // {{ $complaint->user->email }}</p>
</body>
</html>
