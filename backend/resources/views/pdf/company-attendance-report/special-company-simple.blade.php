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
            max-width: 1200px;
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
        
        .employee-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 12px;
        }
        
        .employee-id {
            color: #7f8c8d;
            font-size: 10px;
            margin-top: 2px;
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
            color: #27ae60;
            font-weight: bold;
        }
        
        .vacation-day {
            background: #fef9e7 !important;
            color: #f39c12;
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
                    <div class="detail-value">{{ $report->number }}</div>
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
                    <th style="width: 40px;">م</th>
                    <th style="width: 180px;">بيانات الموظف</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th style="width: 80px;">الرقم الوظيفي</th>
                    @endif
                    <th style="width: 100px;">السجل المدني</th>
                    <th style="width: 80px;">أيام العمل</th>
                    @foreach ($days as $day)
                        <th class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="width: 25px;">
                            <div style="writing-mode: vertical-rl; transform: rotate(180deg); height: 80px; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                {{ $day['name'] }}<br>
                                <small>{{ $day['date'] }}</small>
                            </div>
                        </th>
                    @endforeach
                    <th style="width: 80px;">أيام الغياب</th>
                </tr>
            </thead>
            <tbody>
                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                    @foreach ($active_trainees as $counter => $record)
                        @if(! ($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)) )
                            @if ($record->status === 'temporary_stop')
                                @continue
                            @endif
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td>
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">معرف: {{ $record->trainee->id }}</div>
                                </td>
                                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                                    <td>{{ $record->trainee->job_number }}</td>
                                @endif
                                <td>{{ $record->trainee->clean_identity_number }}</td>
                                <td>
                                    @if ($record->start_date)
                                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                    @else
                                        {{ count($days) }}
                                    @endif
                                </td>
                                @for($i=0;$i<count($days);$i++)
                                    <td class="{{ $days[$i]['vacation_day'] ? 'vacation-day' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            <span class="vacation">X</span>
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
                                <td>
                                    @if ($record->start_date)
                                        {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    @foreach ($active_trainees as $counter => $record)
                        @if(! ($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)) )
                            @if ($record->status === 'temporary_stop')
                                @continue
                            @endif
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td>
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">معرف: {{ $record->trainee->id }}</div>
                                </td>
                                <td>{{ $record->trainee->clean_identity_number }}</td>
                                <td>
                                    @if ($record->start_date)
                                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                    @else
                                        {{ count($days) }}
                                    @endif
                                </td>
                                @for($i=0;$i<count($days);$i++)
                                    <td class="{{ $days[$i]['vacation_day'] ? 'vacation-day' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            <span class="vacation">X</span>
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
                                <td>
                                    @if ($record->start_date)
                                        {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            <p>تم إنشاء هذا السجل في {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>This record was generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html> 