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
            <col style="width:200px;">
            <col style="width:200px;">
        </colgroup>
        <thead>
        <tr>
            <th class="text-right">{{ __('words.course') }}</th>
            <th class="text-right">{{ __('words.date') }}</th>
            <th>{{ __('words.status') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
            @foreach ($records as $record)
                <td class="border-t">{{ $record->course_batch_session->course->name_ar }}</td>
                <td class="border-t text-right" dir="ltr">{{ $record->course_batch_session->starts_at_timezone }}</td>
                <td class="border-t text-center">{{ __('words.'.$record->status_name) }}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
