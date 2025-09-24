<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.4;
            padding: 20px;
        }
        
        .container {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            background: #007bff;
            color: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .company-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-right: 4px solid #007bff;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .detail-label {
            font-weight: bold;
            color: #495057;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .attendance-table th {
            background: #495057;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #6c757d;
        }
        
        .attendance-table td {
            padding: 10px 6px;
            text-align: center;
            border: 1px solid #dee2e6;
            background: white;
            font-size: 10px;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .employee-name {
            font-weight: bold;
            color: #495057;
            font-size: 12px;
            margin-bottom: 2px;
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 9px;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            line-height: 20px;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }
        
        .present {
            background: #28a745;
            color: white;
        }
        
        .absent {
            background: #dc3545;
            color: white;
        }
        
        .vacation {
            background: #ffc107;
            color: #212529;
        }
        
        .day-header {
            background: #007bff !important;
            color: white !important;
        }
        
        .vacation-day {
            background: #6c757d !important;
            color: white !important;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                border: 1px solid #000;
                margin: 0;
                padding: 15px;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* أعمدة الجدول */
        .col-index { width: 35px; }
        .col-employee { width: 150px; }
        .col-job-number { width: 70px; }
        .col-civil { width: 100px; }
        .col-work-days { width: 60px; }
        .col-day { width: 20px; }
        .col-absence { width: 60px; }
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
            <div class="company-name">{{ $report->company->name_ar }}</div>
            <div class="company-subtitle">{{ $report->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -40px;">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">رقم التقرير</div>
                <div class="detail-value">{{ str_replace('ATR-', '', $report->number) }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">فترة التقرير</div>
                <div class="detail-value">{{ $report->date_from->format('m/d') }} - {{ $report->date_to->format('m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">عدد الموظفين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">أيام العمل</div>
                <div class="detail-value">{{ count($days) }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="col-index">م</th>
                    <th class="col-employee">اسم الموظف</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th class="col-job-number">الرقم الوظيفي</th>
                    @endif
                    <th class="col-civil">الهوية المدنية</th>
                    <th class="col-work-days">أيام العمل</th>
                    @foreach ($days as $day)
                        <th class="col-day {{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                            <div style="writing-mode: vertical-rl; transform: rotate(180deg); height: 50px; display: flex; align-items: center; justify-content: center; font-size: 8px;">
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
        
        <!-- Footer -->
        <div style="margin-top: 20px; text-align: center; padding: 15px; background: #f8f9fa; border-radius: 6px; border-top: 2px solid #007bff;">
            <p style="color: #6c757d; font-size: 12px; margin: 0;">تم إنشاؤه تلقائياً بواسطة نظام إدارة التدريب</p>
        </div>
    </div>
</body>
</html>
