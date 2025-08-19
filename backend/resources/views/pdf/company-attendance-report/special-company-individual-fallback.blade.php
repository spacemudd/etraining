<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Individual Attendance Report - Special Design</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #667eea;
            color: white;
            border-radius: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 18px;
        }
        
        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }
        
        .company-info h2 {
            margin: 0;
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }
        
        .company-info p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        
        .trainee-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 2px solid #4caf50;
        }
        
        .trainee-info h3 {
            margin: 0 0 20px 0;
            color: #2e7d32;
            text-align: center;
        }
        
        .trainee-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #4caf50;
        }
        
        .detail-label {
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 16px;
            color: #333;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .report-item {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #2196f3;
        }
        
        .report-label {
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 5px;
        }
        
        .report-value {
            font-size: 18px;
            color: #333;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .attendance-table th {
            background: #667eea;
            color: white;
            padding: 15px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        .attendance-table td {
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: white;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .present {
            background: #4caf50 !important;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .absent {
            background: #f44336 !important;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .vacation {
            background: #ff9800 !important;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .day-header {
            background: #e8f5e8;
            font-weight: bold;
            color: #2e7d32;
        }
        
        .vacation-day {
            background: #fff3e0 !important;
            color: #e65100;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-top: 3px solid #667eea;
        }
        
        .footer p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .footer p:last-child {
            margin: 5px 0 0 0;
            color: #999;
            font-size: 12px;
        }
        
        .logo {
            max-width: 150px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 10px;
            border: 2px solid #4caf50;
        }
        
        .summary h4 {
            margin: 0 0 15px 0;
            color: #2e7d32;
            text-align: center;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-weight: bold;
            color: #2e7d32;
        }
        
        .summary-value {
            font-size: 18px;
            color: #333;
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
            <h1>تقرير الحضور الفردي</h1>
            <p>Individual Attendance Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div>
                <h2>{{ $record->company->name_ar }}</h2>
                <p>{{ $record->company->name_en }}</p>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Trainee Info -->
        <div class="trainee-info">
            <h3>معلومات المتدرب</h3>
            <div class="trainee-details">
                <div class="detail-item">
                    <div class="detail-label">الاسم</div>
                    <div class="detail-value">{{ $record->trainee->name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">السجل المدني</div>
                    <div class="detail-value">{{ $record->trainee->clean_identity_number }}</div>
                </div>
                @if ($record->trainee->job_number)
                <div class="detail-item">
                    <div class="detail-label">الرقم الوظيفي</div>
                    <div class="detail-value">{{ $record->trainee->job_number }}</div>
                </div>
                @endif
                <div class="detail-item">
                    <div class="detail-label">الحالة</div>
                    <div class="detail-value">
                        <span style="background: #4caf50; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold;">
                            {{ $record->status === 'active' ? 'فعال' : 'غير فعال' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="report-item">
                <div class="report-label">رقم التقرير</div>
                <div class="report-value">{{ $record->report->number }}</div>
            </div>
            <div class="report-item">
                <div class="report-label">من تاريخ</div>
                <div class="report-value">{{ $record->report->date_from->format('Y-m-d') }}</div>
            </div>
            <div class="report-item">
                <div class="report-label">إلى تاريخ</div>
                <div class="report-value">{{ $record->report->date_to->format('Y-m-d') }}</div>
            </div>
            <div class="report-item">
                <div class="report-label">أيام الدوام</div>
                <div class="report-value">
                    @if ($record->start_date)
                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                    @else
                        {{ count($days) }}
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 80px;">التاريخ</th>
                    <th style="width: 100px;">اليوم</th>
                    <th style="width: 120px;">الحالة</th>
                    @if ($with_attendance_times)
                        <th style="width: 100px;">وقت الحضور</th>
                        <th style="width: 100px;">وقت الانصراف</th>
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
                                <span class="vacation">إجازة</span>
                            @else
                                @if ($record->start_date)
                                    @if ($day['date_carbon']->isBetween($record->start_date, $record->end_date))
                                        <span class="present">حاضر</span>
                                    @else
                                        @if ($record->status === 'new_registration')
                                            <span class="absent">غائب</span>
                                        @endif
                                    @endif
                                @else
                                    <span class="present">حاضر</span>
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
            <h4>ملخص الحضور</h4>
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
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>This report was generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html> 