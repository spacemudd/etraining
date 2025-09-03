<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>سجل المتابعة الفردي للموظف</title>
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
            max-width: 1000px;
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
        
        .trainee-info {
            background: #d5f4e6;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 2px solid #27ae60;
        }
        
        .trainee-info h3 {
            margin: 0 0 15px 0;
            color: #27ae60;
            text-align: center;
            font-size: 18px;
        }
        
        .trainee-details {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .trainee-row {
            display: table-row;
        }
        
        .trainee-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #27ae60;
            background: white;
            text-align: center;
            width: 25%;
        }
        
        .trainee-label {
            font-weight: bold;
            color: #27ae60;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .trainee-value {
            font-size: 14px;
            color: #333;
        }
        
        .report-details {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .report-row {
            display: table-row;
        }
        
        .report-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #3498db;
            background: #ebf3fd;
            text-align: center;
            width: 25%;
        }
        
        .report-label {
            font-weight: bold;
            color: #2980b9;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .report-value {
            font-size: 16px;
            color: #333;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 2px solid #ddd;
        }
        
        .attendance-table th {
            background: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        
        .attendance-table td {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: white;
            font-size: 11px;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .present {
            background: #27ae60 !important;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-block;
            line-height: 20px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .absent {
            background: #e74c3c !important;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-block;
            line-height: 20px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .vacation {
            background: #f39c12 !important;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-block;
            line-height: 20px;
            font-weight: bold;
            font-size: 10px;
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
        
        .summary {
            margin-top: 20px;
            padding: 20px;
            background: #d5f4e6;
            border-radius: 6px;
            border: 2px solid #27ae60;
        }
        
        .summary h4 {
            margin: 0 0 15px 0;
            color: #27ae60;
            text-align: center;
            font-size: 16px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #27ae60;
            background: white;
            text-align: center;
            width: 33.33%;
        }
        
        .summary-label {
            font-weight: bold;
            color: #27ae60;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            color: #333;
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
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                border: none;
                margin: 0;
                padding: 10px;
            }
            
            .header {
                background: #4a90e2 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
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
            <h1>سجل المتابعة الفردي للموظف</h1>
            <p>Individual Employee Attendance Tracking Record</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">{{ $record->company->name_ar }}</div>
            <div class="company-subtitle">{{ $record->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Trainee Info -->
        <div class="trainee-info">
            <h3>معلومات الموظف</h3>
            <div class="trainee-details">
                <div class="trainee-row">
                    <div class="trainee-cell">
                        <div class="trainee-label">الاسم الكامل</div>
                        <div class="trainee-value">{{ $record->trainee->name }}</div>
                    </div>
                    <div class="trainee-cell">
                        <div class="trainee-label">رقم الهوية</div>
                        <div class="trainee-value">{{ $record->trainee->clean_identity_number }}</div>
                    </div>
                    @if ($record->trainee->job_number)
                    <div class="trainee-cell">
                        <div class="trainee-label">الرقم الوظيفي</div>
                        <div class="trainee-value">{{ $record->trainee->job_number }}</div>
                    </div>
                    @endif
                    <div class="trainee-cell">
                        <div class="trainee-label">حالة التوظيف</div>
                        <div class="trainee-value">
                            <span style="background: #27ae60; color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: bold;">
                                {{ $record->status === 'active' ? 'موظف نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="report-row">
                <div class="report-cell">
                    <div class="report-label">رقم السجل</div>
                    <div class="report-value">{{ str_replace('ATR-', '', $record->report->number) }}</div>
                </div>
                <div class="report-cell">
                    <div class="report-label">فترة المتابعة</div>
                    <div class="report-value">{{ $report->date_from->format('Y-m-d') }} - {{ $report->date_to->format('Y-m-d') }}</div>
                </div>
                <div class="report-cell">
                    <div class="report-label">أيام العمل المطلوبة</div>
                    <div class="report-value">
                        @if ($record->start_date)
                            {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                        @else
                            {{ count($days) }}
                        @endif
                    </div>
                </div>
                <div class="report-cell">
                    <div class="report-label">مدة المتابعة</div>
                    <div class="report-value">{{ count($days) }} يوم</div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 100px;">التاريخ</th>
                    <th style="width: 120px;">اليوم</th>
                    <th style="width: 140px;">حالة الحضور</th>
                    @if ($with_attendance_times)
                        <th style="width: 120px;">وقت بدء العمل</th>
                        <th style="width: 120px;">وقت انتهاء العمل</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($days as $day)
                    <tr>
                        <td>{{ $day['date'] }}</td>
                        <td class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                            {{ $day['name'] }}
                        </td>
                        <td>
                            @if ($day['vacation_day'])
                                <span class="vacation">إجازة رسمية</span>
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span class="present">حضور</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span class="absent">غياب</span>
                                        @endif
                                    @endif
                                @else
                                    <span class="present">حضور</span>
                                @endif
                            @endif
                        </td>
                        @if ($with_attendance_times)
                            <td>
                                @if (!$day['vacation_day'])
                                    @if ($record->start_date)
                                        @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                            08:{{sprintf("%02d",rand(1,10))}}
                                        @endif
                                    @else
                                        08:{{sprintf("%02d",rand(1,10))}}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if (!$day['vacation_day'])
                                    @if ($record->start_date)
                                        @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                            16:{{sprintf("%02d",rand(0,5))}}
                                        @endif
                                    @else
                                        16:{{sprintf("%02d",rand(0,5))}}
                                    @endif
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <h4>ملخص سجل المتابعة</h4>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">
                        <div class="summary-label">أيام الحضور</div>
                        <div class="summary-value">
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
                                    {{ count($days) }}
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="summary-cell">
                        <div class="summary-label">أيام الغياب</div>
                        <div class="summary-value">
                            @if (isset($record->is_resignation) && $record->is_resignation)
                                0
                            @else
                                @if ($record->start_date)
                                    {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                @else
                                    0
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="summary-cell">
                        <div class="summary-label">أيام الإجازة</div>
                        <div class="summary-value">
                            {{ count(array_filter($days, function($day) { return $day['vacation_day']; })) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <!-- تم إزالة Footer بناءً على طلب المستخدم -->
    </div>
</body>
</html> 