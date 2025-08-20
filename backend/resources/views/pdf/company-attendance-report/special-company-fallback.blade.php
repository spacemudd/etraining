<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Company attendance report - Special Design</title>
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
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #2196f3;
        }
        
        .detail-label {
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .detail-value {
            font-size: 18px;
            color: #333;
            font-weight: 600;
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
        
        .employee-name {
            font-weight: bold;
            color: #1976d2;
            font-size: 14px;
        }
        
        .employee-id {
            color: #666;
            font-size: 12px;
            margin-top: 3px;
        }
        
        .status-active {
            background: #4caf50;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
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
        
        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
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
            <h1>تقرير الحضور والانصراف</h1>
            <p>Attendance & Absence Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div>
                <h2>{{ $report->company->name_ar }}</h2>
                <p>{{ $report->company->name_en }}</p>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-item">
                <div class="detail-label">رقم التقرير</div>
                <div class="detail-value">{{ $report->number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">من تاريخ</div>
                <div class="detail-value">{{ $report->date_from->format('Y-m-d') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">إلى تاريخ</div>
                <div class="detail-value">{{ $report->date_to->format('Y-m-d') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">عدد المتدربين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 50px;">م</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th style="width: 80px;">الرقم الوظيفي</th>
                    @endif
                    <th style="width: 80px;">الحالة</th>
                    <th style="width: 200px;">اسم الموظف</th>
                    <th style="width: 120px;">السجل المدني</th>
                    <th style="width: 100px;">أيام الدوام</th>
                    @foreach ($days as $day)
                        <th class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="width: 30px;">
                            <div class="vertical-text">
                                {{ $day['name'] }}<br>
                                <small>{{ $day['date'] }}</small>
                            </div>
                        </th>
                    @endforeach
                    <th style="width: 80px;">عدد الغياب</th>
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
                                <td>{{ $record->trainee->job_number }}</td>
                                <td><span class="status-active">فعال</span></td>
                                <td>
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">ID: {{ $record->trainee->id }}</div>
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
                                <td><span class="status-active">فعال</span></td>
                                <td>
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">ID: {{ $record->trainee->id }}</div>
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
            <p>تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>This report was generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html> 