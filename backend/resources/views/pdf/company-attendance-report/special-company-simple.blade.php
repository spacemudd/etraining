<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>سجل المتابعة اليومية للموظفين</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.4;
            padding: 20px;
        }
        
        .container {
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 0 auto;
            max-width: 1400px;
            min-width: 1200px;
        }
        
        .header {
            text-align: center;
            background: #4a90e2;
            color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .company-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #4a90e2;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .report-details {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .detail-row {
            display: table-row;
        }
        
        .detail-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            text-align: center;
            width: 25%;
        }
        
        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 16px;
            color: #333;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 2px solid #ddd;
            table-layout: fixed;
        }
        
        .attendance-table th {
            background: #34495e;
            color: white;
            padding: 12px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .attendance-table td {
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #ddd;
            background: white;
            font-size: 10px;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .present {
            background: #27ae60 !important;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: inline-block;
            line-height: 18px;
            font-weight: bold;
            font-size: 9px;
        }
        
        .absent {
            background: #e74c3c !important;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: inline-block;
            line-height: 18px;
            font-weight: bold;
            font-size: 9px;
        }
        
        .vacation {
            background: #f39c12 !important;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: inline-block;
            line-height: 18px;
            font-weight: bold;
            font-size: 9px;
        }
        
        .employee-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 11px;
            line-height: 1.2;
        }
        
        .employee-id {
            color: #7f8c8d;
            font-size: 9px;
            margin-top: 2px;
            line-height: 1.1;
        }
        
        .status-active {
            background: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            padding: 15px;
            background: #ecf0f1;
            border-radius: 6px;
            border-top: 2px solid #bdc3c7;
        }
        
        .footer p {
            margin: 0;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .logo {
            max-width: 120px;
            height: auto;
            border-radius: 6px;
            border: 2px solid #ddd;
            float: right;
            margin-top: -40px;
        }
        
        .day-header {
            background: #d5f4e6 !important;
            color: #1e7e34 !important;
            font-weight: bold;
        }
        
        .vacation-day {
            background: #fef9e7 !important;
            color: #d68910 !important;
        }
        
        /* تحسين عرض الأعمدة */
        .col-index { width: 40px; }
        .col-employee { width: 200px; }
        .col-job-number { width: 80px; }
        .col-civil { width: 100px; }
        .col-work-days { width: 80px; }
        .col-day { width: 22px; }
        .col-absence { width: 80px; }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                border: none;
                margin: 0;
                padding: 10px;
                max-width: none;
                min-width: auto;
            }
            
            .header {
                background: #4a90e2 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table {
                table-layout: auto;
                width: 100%;
            }
            
            .attendance-table th {
                background: #34495e !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>سجل المتابعة اليومية للموظفين</h1>
            <p>Daily Employee Attendance Tracking Record</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">{{ $report->company->name_ar }}</div>
            <div class="company-subtitle">{{ $report->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-row">
                <div class="detail-cell">
                    <div class="detail-label">رقم السجل</div>
                    <div class="detail-value">{{ str_replace('ATR-', '', $report->number) }}</div>
                </div>
                <div class="detail-cell">
                    <div class="detail-label">فترة المتابعة</div>
                    <div class="detail-value">{{ $report->date_from->format('Y-m-d') }} - {{ $report->date_to->format('Y-m-d') }}</div>
                </div>
                <div class="detail-cell">
                    <div class="detail-label">إجمالي الموظفين</div>
                    <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
                </div>
                <div class="detail-cell">
                    <div class="detail-label">عدد أيام العمل</div>
                    <div class="detail-value">{{ count($days) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="col-index">م</th>
                    <th class="col-employee">بيانات الموظف</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th class="col-job-number">الرقم الوظيفي</th>
                    @endif
                    <th class="col-civil">السجل المدني</th>
                    <th class="col-work-days">أيام العمل</th>
                    @foreach ($days as $day)
                        <th class="col-day {{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                            <div style="writing-mode: vertical-rl; transform: rotate(180deg); height: 70px; display: flex; align-items: center; justify-content: center; font-size: 9px;">
                                {{ $day['name'] }}<br>
                                <small style="font-size: 8px;">{{ $day['date'] }}</small>
                            </div>
                        </th>
                    @endforeach
                    <th class="col-absence">أيام الغياب</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Ensure all records have required properties
                    foreach ($active_trainees as $record) {
                        if (!isset($record->status)) {
                            $record->status = 'active';
                        }
                        if (!isset($record->start_date)) {
                            $record->start_date = null;
                        }
                        if (!isset($record->end_date)) {
                            $record->end_date = null;
                        }
                        // Ensure start_date and end_date are Carbon instances if they exist
                        if ($record->start_date && !($record->start_date instanceof \Carbon\Carbon)) {
                            $record->start_date = \Carbon\Carbon::parse($record->start_date);
                        }
                        if ($record->end_date && !($record->end_date instanceof \Carbon\Carbon)) {
                            $record->end_date = \Carbon\Carbon::parse($record->end_date);
                        }
                        // Ensure trainee relationship is loaded
                        if (!isset($record->trainee) && method_exists($record, 'getTraineeAttribute')) {
                            $record->trainee = $record->trainee;
                        }
                        
                        // For deleted trainees (with is_resignation flag), we should keep the original dates
                        // to maintain correct attendance calculation
                        if (isset($record->is_resignation) && $record->is_resignation) {
                            \Log::info('Keeping original dates for deleted trainee ' . $record->trainee->id . ' in view: start_date=' . ($record->start_date ? $record->start_date->format('Y-m-d') : 'null') . ', end_date=' . ($record->end_date ? $record->end_date->format('Y-m-d') : 'null'));
                        }
                    }
                @endphp
                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                    @foreach ($active_trainees as $counter => $record)
                        @if (isset($record->status) && $record->status === 'temporary_stop')
                            @continue
                        @endif
                        <tr>
                            <td class="col-index">{{ $loop->iteration }}</td>
                            <td class="col-employee">
                                <div class="employee-name">{{ $record->trainee->name }}</div>
                                <div class="employee-id">معرف: {{ $record->trainee->id }}</div>
                            </td>
                            @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                                <td class="col-job-number">{{ $record->trainee->job_number }}</td>
                            @endif
                            <td class="col-civil">{{ $record->trainee->clean_identity_number }}</td>
                            <td class="col-work-days">
                                @if (isset($record->is_resignation) && $record->is_resignation)
                                    @php
                                        $workDaysCount = 0;
                                        for($i=0;$i<count($days);$i++) {
                                            if (!$days[$i]['vacation_day']) {
                                                if ($record->start_date) {
                                                    if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                        $workDaysCount++;
                                                    }
                                                } else {
                                                    $workDaysCount++;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $workDaysCount }}
                                @else
                                    @if ($record->start_date)
                                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                    @else
                                        @if (isset($record->is_resignation) && $record->is_resignation)
                                            {{ count($days) }}
                                        @else
                                            {{ count($days) }}
                                        @endif
                                    @endif
                                @endif
                            </td>
                            @for($i=0;$i<count($days);$i++)
                                <td class="col-day {{ $days[$i]['vacation_day'] ? 'vacation-day' : '' }}">
                                    @if ($days[$i]['vacation_day'])
                                        @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                            {{-- Weekend after resignation date - show empty --}}
                                        @else
                                            <span class="vacation">X</span>
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                <span class="present">✓</span>
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    <span class="absent">✗</span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="present">✓</span>
                                        @endif
                                    @endif
                                </td>
                            @endfor
                            <td class="col-absence">
                                @if (isset($record->is_resignation) && $record->is_resignation)
                                    @php
                                        $attendance = \App\Services\CompanyAttendanceReportService::calculateAttendanceForDeletedTrainee($record, $days);
                                    @endphp
                                    {{ $attendance['absence_days'] }}
                                @else
                                    @if ($record->start_date)
                                        {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                    @else
                                        0
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    @foreach ($active_trainees as $counter => $record)
                        @if (isset($record->status) && $record->status === 'temporary_stop')
                            @continue
                        @endif
                        <tr>
                            <td class="col-index">{{ $loop->iteration }}</td>
                            <td class="col-employee">
                                <div class="employee-name">{{ $record->trainee->name }}</div>
                                <div class="employee-id">معرف: {{ $record->trainee->id }}</div>
                            </td>
                            <td class="col-civil">{{ $record->trainee->clean_identity_number }}</td>
                            <td class="col-work-days">
                                @if (isset($record->is_resignation) && $record->is_resignation)
                                    @php
                                        $workDaysCount = 0;
                                        for($i=0;$i<count($days);$i++) {
                                            if (!$days[$i]['vacation_day']) {
                                                if ($record->start_date) {
                                                    if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                        $workDaysCount++;
                                                    }
                                                } else {
                                                    $workDaysCount++;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $workDaysCount }}
                                @else
                                    @if ($record->start_date)
                                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                    @else
                                        @if (isset($record->is_resignation) && $record->is_resignation)
                                            {{ count($days) }}
                                        @else
                                            {{ count($days) }}
                                        @endif
                                    @endif
                                @endif
                            </td>
                            @for($i=0;$i<count($days);$i++)
                                <td class="col-day {{ $days[$i]['vacation_day'] ? 'vacation-day' : '' }}">
                                    @if ($days[$i]['vacation_day'])
                                        @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                            {{-- Weekend after resignation date - show empty --}}
                                        @else
                                            <span class="vacation">X</span>
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                <span class="present">✓</span>
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    <span class="absent">✗</span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="present">✓</span>
                                        @endif
                                    @endif
                                </td>
                            @endfor
                            <td class="col-absence">
                                @if (isset($record->is_resignation) && $record->is_resignation)
                                    0
                                @else
                                    @if ($record->start_date)
                                        {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                    @else
                                        0
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        
        <!-- Footer -->
        <!-- تم إزالة Footer بناءً على طلب المستخدم -->
    </div>
</body>
</html> 