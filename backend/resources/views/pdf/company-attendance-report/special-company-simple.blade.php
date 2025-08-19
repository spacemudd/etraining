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
            max-width: 1400px;
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
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .detail-item {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #2196f3;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.2);
            transition: transform 0.3s ease;
        }
        
        .detail-item:hover {
            transform: translateY(-3px);
        }
        
        .detail-label {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 20px;
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
        
        .employee-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 10px;
            margin-top: 3px;
            font-style: italic;
        }
        
        .status-active {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(39, 174, 96, 0.3);
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
        
        .day-header {
            background: linear-gradient(135deg, #d5f4e6 0%, #a8e6cf 100%) !important;
            color: #27ae60;
            font-weight: 600;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fef9e7 0%, #fdeaa7 100%) !important;
            color: #f39c12;
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
            <div class="detail-item">
                <div class="detail-label">رقم السجل</div>
                <div class="detail-value">{{ $report->number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">فترة المتابعة</div>
                <div class="detail-value">{{ $report->date_from->format('Y-m-d') }} - {{ $report->date_to->format('Y-m-d') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">إجمالي الموظفين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">عدد أيام العمل</div>
                <div class="detail-value">{{ count($days) }}</div>
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
                    <th style="width: 45px;">م</th>
                    <th style="width: 180px;">بيانات الموظف</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th style="width: 80px;">الرقم الوظيفي</th>
                    @endif
                    <th style="width: 100px;">السجل المدني</th>
                    <th style="width: 80px;">أيام العمل</th>
                    @foreach ($days as $day)
                        <th class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="width: 28px;">
                            <div style="writing-mode: vertical-rl; transform: rotate(180deg); height: 100px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600;">
                                {{ $day['name'] }}<br>
                                <small style="font-size: 9px;">{{ $day['date'] }}</small>
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
                                <td style="font-weight: 600; color: #667eea;">{{ ++$counter }}</td>
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
                                <td style="font-weight: 600; color: #e74c3c;">
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
                                <td style="font-weight: 600; color: #667eea;">{{ ++$counter }}</td>
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
                                <td style="font-weight: 600; color: #e74c3c;">
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