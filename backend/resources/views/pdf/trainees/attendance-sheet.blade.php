<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>{{ $trainee->name }}</title>
</head>
<body class="font-sans antialiased  bg-gray-50 dark:bg-gray-900">
<div>
    <table class="table">
        <colgroup>
            <col style="width:200px;">
        </colgroup>
        <tbody>
        <tr>
            <td>الاسم</td>
            <td>{{ $trainee->name }}</td>
        </tr>
        <tr>
            <td>الإيميل</td>
            <td>{{ $trainee->email }}</td>
        </tr>
        <tr>
            <td>الجوال</td>
            <td>{{ $trainee->phone }}</td>
        </tr>
        </tbody>
    </table>

    <hr>

    <table style="width:100%;">
        <colgroup>
            <col>
            <col style="width:200px;">
            <col style="width:200px;">
        </colgroup>
        <thead>
        <tr>
            <th style="text-align:right">{{ __('words.course') }}</th>
            <th style="text-align:right">وقت الحضور</th>
            <th style="text-align:right">{{ __('words.status') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($records as $record)
            <tr style="page-break-inside: avoid;">
                <td style="text-align:right;border:1px solid black;" class="border-t">
                    {{ $record->course_batch_session->course->name_ar }}<br/>
                    <span dir="ltr">{{ $record->course_batch_session->starts_at_timezone }}</span>
                </td>
                <td style="text-align:right;border:1px solid black;" class="border-t" dir="ltr">{{ $record->attended_at_timezone }}</td>
                <td style="text-align:right;border:1px solid black;" class="border-t">{{ __('words.'.$record->status_name) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
