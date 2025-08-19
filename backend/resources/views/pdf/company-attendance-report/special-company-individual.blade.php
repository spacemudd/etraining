<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Individual Attendance Report - Special Design</title>
    <link rel="stylesheet" href="{{ public_path('css/special-company-pdf.css') }}">
    <style>
        
        /* Additional custom styles for individual report */
        .trainee-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 2px solid #4caf50;
        }
        
        .trainee-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
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
        
        @if ($with_attendance_times)
        .time-info {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        @endif
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور الفردي</h1>
            <p style="margin: 10px 0 0 0; font-size: 18px;">Individual Attendance Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div>
                <h2 style="margin: 0; color: #333; font-size: 24px;">{{ $record->company->name_ar }}</h2>
                <p style="margin: 5px 0 0 0; color: #666;">{{ $record->company->name_en }}</p>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Trainee Info -->
        <div class="trainee-info">
            <h3 style="margin: 0 0 20px 0; color: #2e7d32; text-align: center;">معلومات المتدرب</h3>
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
        <div style="margin-top: 30px; padding: 20px; background: #e8f5e8; border-radius: 10px; border: 2px solid #4caf50;">
            <h4 style="margin: 0 0 15px 0; color: #2e7d32; text-align: center;">ملخص الحضور</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #2e7d32;">أيام الحضور</div>
                    <div style="font-size: 18px; color: #333;">
                        @if ($record->start_date)
                            {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                        @else
                            {{ count($days) }}
                        @endif
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #f44336;">أيام الغياب</div>
                    <div style="font-size: 18px; color: #333;">
                        @if ($record->start_date)
                            {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                        @else
                            0
                        @endif
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="font-weight: bold; color: #ff9800;">أيام الإجازة</div>
                    <div style="font-size: 18px; color: #333;">
                        {{ count(array_filter($days, function($day) { return $day['vacation_day']; })) }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0; color: #666; font-size: 14px;">
                تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}
            </p>
            <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">
                This report was generated on {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html> 