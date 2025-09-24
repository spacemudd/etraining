<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف - التصميم الحديث</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.5;
            padding: 15px;
            min-height: 100vh;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 0 auto;
            max-width: 1400px;
            min-width: 1200px;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #f9ca24, #f0932b);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            position: relative;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 8px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .company-info {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
        }
        
        .company-details h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(168, 237, 234, 0.3);
        }
        
        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 24px;
            font-weight: 700;
            color: #e74c3c;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .attendance-table td {
            padding: 12px 6px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
            background: white;
            font-size: 11px;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .employee-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 13px;
            margin-bottom: 4px;
        }
        
        .employee-id {
            color: #7f8c8d;
            font-size: 10px;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            line-height: 24px;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }
        
        .present {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
        }
        
        .absent {
            background: linear-gradient(135deg, #e17055, #d63031);
            color: white;
        }
        
        .vacation {
            background: linear-gradient(135deg, #fdcb6e, #e17055);
            color: white;
        }
        
        .day-header {
            background: linear-gradient(135deg, #74b9ff, #0984e3) !important;
            color: white !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fab1a0, #e17055) !important;
            color: white !important;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 1px solid #ddd;
                margin: 0;
                padding: 20px;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* أعمدة الجدول */
        .col-index { width: 40px; }
        .col-employee { width: 180px; }
        .col-job-number { width: 80px; }
        .col-civil { width: 120px; }
        .col-work-days { width: 70px; }
        .col-day { width: 25px; }
        .col-absence { width: 70px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>��� تقرير الحضور والانصراف المتقدم</h1>
            <p>Advanced Employee Attendance & Absence Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-details">
                <h2>{{ $report->company->name_ar }}</h2>
                <p>{{ $report->company->name_en }}</p>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">��� رقم التقرير</div>
                <div class="detail-value">{{ str_replace('ATR-', '', $report->number) }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">��� فترة التقرير</div>
                <div class="detail-value">{{ $report->date_from->format('m/d') }} - {{ $report->date_to->format('m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">��� عدد الموظفين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">���️ أيام العمل</div>
                <div class="detail-value">{{ count($days) }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="col-index">م</th>
                    <th class="col-employee">معلومات الموظف</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th class="col-job-number">الرقم الوظيفي</th>
                    @endif
                    <th class="col-civil">الهوية المدنية</th>
                    <th class="col-work-days">أيام العمل</th>
                    @foreach ($days as $day)
                        <th class="col-day {{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                            <div style="writing-mode: vertical-rl; transform: rotate(180deg); height: 60px; display: flex; align-items: center; justify-content: center; font-size: 9px; line-height: 1.2;">
                                {{ $day['name'] }}<br>
                                <small style="font-size: 7px;">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</small>
                            </div>
                        </th>
                    @endforeach
                    <th class="col-absence">الغياب</th>
                </tr>
            </thead>
            <tbody>
                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                    @foreach ($active_trainees as $counter => $record)
                        @if ($record->status === 'temporary_stop')
                            @continue
                        @endif
                        <tr>
                            <td class="col-index">{{ $loop->iteration }}</td>
                            <td class="col-employee">
                                <div class="employee-name">{{ $record->trainee->name }}</div>
                                <div class="employee-id">ID: {{ $record->trainee->id }}</div>
                            </td>
                            <td class="col-job-number">{{ $record->trainee->job_number }}</td>
                            <td class="col-civil">{{ $record->trainee->clean_identity_number }}</td>
                            <td class="col-work-days">
                                @php
                                    $workDays = 0;
                                    foreach ($days as $day) {
                                        if ($record->start_date) {
                                            if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                $workDays++;
                                            }
                                        } else {
                                            $workDays++;
                                        }
                                    }
                                @endphp
                                <strong>{{ $workDays }}</strong>
                            </td>
                            @for($i=0;$i<count($days);$i++)
                                <td class="col-day">
                                    @if ($days[$i]['vacation_day'])
                                        @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                            {{-- Weekend after resignation date - show empty --}}
                                        @else
                                            <span class="attendance-mark vacation">X</span>
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                <span class="attendance-mark present">✓</span>
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    <span class="attendance-mark absent">✗</span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="attendance-mark present">✓</span>
                                        @endif
                                    @endif
                                </td>
                            @endfor
                            <td class="col-absence"><strong>0</strong></td>
                        </tr>
                    @endforeach
                @else
                    @foreach ($active_trainees as $counter => $record)
                        @if ($record->status === 'temporary_stop')
                            @continue
                        @endif
                        <tr>
                            <td class="col-index">{{ $loop->iteration }}</td>
                            <td class="col-employee">
                                <div class="employee-name">{{ $record->trainee->name }}</div>
                                <div class="employee-id">ID: {{ $record->trainee->id }}</div>
                            </td>
                            <td class="col-civil">{{ $record->trainee->clean_identity_number }}</td>
                            <td class="col-work-days">
                                @php
                                    $workDays = 0;
                                    foreach ($days as $day) {
                                        if ($record->start_date) {
                                            if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                $workDays++;
                                            }
                                        } else {
                                            $workDays++;
                                        }
                                    }
                                @endphp
                                <strong>{{ $workDays }}</strong>
                            </td>
                            @for($i=0;$i<count($days);$i++)
                                <td class="col-day">
                                    @if ($days[$i]['vacation_day'])
                                        @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                            {{-- Weekend after resignation date - show empty --}}
                                        @else
                                            <span class="attendance-mark vacation">X</span>
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                <span class="attendance-mark present">✓</span>
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    <span class="attendance-mark absent">✗</span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="attendance-mark present">✓</span>
                                        @endif
                                    @endif
                                </td>
                            @endfor
                            <td class="col-absence"><strong>0</strong></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>
