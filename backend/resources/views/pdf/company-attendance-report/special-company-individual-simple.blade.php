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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #2c3e50;
            line-height: 1.6;
            padding: 15px;
        }
        
        .container {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1200px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .company-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .company-name {
            font-size: 26px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .company-subtitle {
            color: #6c757d;
            font-size: 16px;
            font-weight: 500;
        }
        
        .trainee-info {
            background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 2px solid #27ae60;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.2);
        }
        
        .trainee-info h3 {
            margin: 0 0 20px 0;
            color: #27ae60;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
        }
        
        .trainee-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .trainee-item {
            background: white;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #27ae60;
            box-shadow: 0 3px 10px rgba(39, 174, 96, 0.1);
        }
        
        .trainee-label {
            font-weight: 600;
            color: #27ae60;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .trainee-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .report-item {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #2196f3;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.2);
        }
        
        .report-label {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .report-value {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 700;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 2px solid #e9ecef;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 12px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #e9ecef;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .attendance-table td {
            padding: 15px 12px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: white;
            font-size: 12px;
            transition: background-color 0.2s ease;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-table tr:hover td {
            background: #e3f2fd;
        }
        
        .present {
            background: #27ae60 !important;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(39, 174, 96, 0.3);
        }
        
        .absent {
            background: #e74c3c !important;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(231, 76, 60, 0.3);
        }
        
        .vacation {
            background: #f39c12 !important;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(243, 156, 18, 0.3);
        }
        
        .day-header {
            background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%) !important;
            color: #27ae60;
            font-weight: 600;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fef9e7 0%, #fdeaa7 100%) !important;
            color: #f39c12;
        }
        
        .summary {
            margin-top: 25px;
            padding: 25px;
            background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%);
            border-radius: 15px;
            border: 2px solid #27ae60;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.2);
        }
        
        .summary h4 {
            margin: 0 0 20px 0;
            color: #27ae60;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }
        
        .summary-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #27ae60;
            box-shadow: 0 3px 10px rgba(39, 174, 96, 0.1);
        }
        
        .summary-label {
            font-weight: 600;
            color: #27ae60;
            font-size: 13px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-value {
            font-size: 20px;
            color: #2c3e50;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            border-top: 3px solid #667eea;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.08);
        }
        
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
        }
        
        .footer p:last-child {
            margin: 5px 0 0 0;
            color: #adb5bd;
            font-size: 11px;
        }
        
        .logo {
            max-width: 130px;
            height: auto;
            border-radius: 10px;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            float: right;
            margin-top: -45px;
        }
        
        .legend {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        
        .legend h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
        }
        
        .legend-items {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #6c757d;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 1px solid #ddd;
                margin: 0;
                padding: 15px;
            }
            
            .header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table th {
                background: #667eea !important;
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
                <div class="trainee-item">
                    <div class="trainee-label">الاسم الكامل</div>
                    <div class="trainee-value">{{ $record->trainee->name }}</div>
                </div>
                <div class="trainee-item">
                    <div class="trainee-label">رقم الهوية</div>
                    <div class="trainee-value">{{ $record->trainee->clean_identity_number }}</div>
                </div>
                @if ($record->trainee->job_number)
                <div class="trainee-item">
                    <div class="trainee-label">الرقم الوظيفي</div>
                    <div class="trainee-value">{{ $record->trainee->job_number }}</div>
                </div>
                @endif
                <div class="trainee-item">
                    <div class="trainee-label">حالة التوظيف</div>
                    <div class="trainee-value">
                        <span style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 5px rgba(39, 174, 96, 0.3);">
                            {{ $record->status === 'active' ? 'موظف نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="report-item">
                <div class="report-label">رقم السجل</div>
                <div class="report-value">{{ $record->report->number }}</div>
            </div>
            <div class="report-item">
                <div class="report-label">فترة المتابعة</div>
                <div class="report-value">{{ $report->date_from->format('Y-m-d') }} - {{ $report->date_to->format('Y-m-d') }}</div>
            </div>
            <div class="report-item">
                <div class="report-label">أيام العمل المطلوبة</div>
                <div class="report-value">
                    @if ($record->start_date)
                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                    @else
                        {{ count($days) }}
                    @endif
                </div>
            </div>
            <div class="report-item">
                <div class="report-label">مدة المتابعة</div>
                <div class="report-value">{{ count($days) }} يوم</div>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="legend">
            <h4>دليل الرموز المستخدمة:</h4>
            <div class="legend-items">
                <div class="legend-item">
                    <span class="present">✓</span>
                    <span>حضور</span>
                </div>
                <div class="legend-item">
                    <span class="absent">✗</span>
                    <span>غياب</span>
                </div>
                <div class="legend-item">
                    <span class="vacation">X</span>
                    <span>إجازة رسمية</span>
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
                        <td style="font-weight: 600; color: #2c3e50;">{{ $day['date'] }}</td>
                        <td class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="font-weight: 600;">
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
                <div class="summary-item">
                    <div class="summary-label">أيام الحضور</div>
                    <div class="summary-value">
                        @if ($record->start_date)
                            {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                        @else
                            {{ count($days) }}
                        @endif
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">أيام الغياب</div>
                    <div class="summary-value">
                        @if ($record->start_date)
                            {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                        @else
                            0
                        @endif
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">أيام الإجازة</div>
                    <div class="summary-value">
                        {{ count(array_filter($days, function($day) { return $day['vacation_day']; })) }}
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">نسبة الحضور</div>
                    <div class="summary-value">
                        @if ($record->start_date)
                            {{ round((($record->start_date->diffInDays($record->end_date) + 1) / count($days)) * 100, 1) }}%
                        @else
                            100%
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>تم إنشاء هذا السجل في {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>This record was generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html> 