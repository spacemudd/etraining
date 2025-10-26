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
            font-family: 'Arial', sans-serif;
            background: #fdfdfd;
            color: #1a1a1a;
            line-height: 1.8;
            padding: 30px;
        }
        
        .container {
            background: white;
            padding: 40px;
            margin: 0 auto;
            max-width: 1400px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px;
            margin: -40px -40px 40px -40px;
            position: relative;
            border-bottom: 5px solid #1a252f;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="80" cy="80" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 12px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        
        .company-info {
            background: #f8f9fa;
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            position: relative;
            page-break-after: avoid;
        }
        
        .company-info::before {
            content: '';
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            height: 4px;
            background: linear-gradient(90deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            border-radius: 8px 8px 0 0;
        }
        
        .company-name {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            position: relative;
            display: block;
        }
        
        .company-name::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #34495e;
        }
        
        .company-subtitle {
            color: #6c757d;
            font-size: 16px;
            font-weight: 400;
            margin-top: 15px;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 50px;
            page-break-after: avoid;
        }
        
        .detail-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 4px;
            background: linear-gradient(90deg, #2c3e50, #34495e);
            border-radius: 0 0 4px 4px;
        }
        
        .detail-label {
            font-weight: 500;
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .detail-value {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.2;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 30px;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 18px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            border-bottom: 1px solid #1a252f;
            page-break-after: avoid;
            position: relative;
        }
        
        .attendance-table th:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .attendance-table td {
            padding: 14px 6px;
            text-align: center;
            border-bottom: 1px solid #f1f3f4;
            background: white;
            font-size: 10px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        
        .attendance-table td:not(:last-child) {
            border-right: 1px solid #f8f9fa;
        }
        
        .attendance-table tbody tr {
            page-break-inside: avoid;
            transition: background-color 0.2s ease;
        }
        
        .attendance-table tbody tr:hover td {
            background: #f8f9fa !important;
        }
        
        .attendance-table tbody tr:nth-child(even) td {
            background: #fbfcfd;
        }
        
        .attendance-table tbody tr:nth-child(odd) td {
            background: white;
        }
        
        .attendance-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .employee-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 11px;
            margin-bottom: 4px;
            line-height: 1.4;
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 8px;
            line-height: 1.2;
            font-weight: 400;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            line-height: 20px;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            position: relative;
        }
        
        .present {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            color: #2d5016;
            border: 2px solid #4a7c59;
            box-shadow: 0 2px 4px rgba(74,124,89,0.2);
        }
        
        .absent {
            background: linear-gradient(135deg, #f8e8e8 0%, #f5f0f0 100%);
            color: #8b1538;
            border: 2px solid #c53030;
            box-shadow: 0 2px 4px rgba(197,48,48,0.2);
        }
        
        .vacation {
            background: linear-gradient(135deg, #f0f0f0 0%, #f8f8f8 100%);
            color: #4a5568;
            border: 2px solid #718096;
            box-shadow: 0 2px 4px rgba(113,128,150,0.2);
        }
        
        .day-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            color: white !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #5a6c7d 0%, #6c7b7d 100%) !important;
            color: white !important;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .container {
                border: 2px solid #000;
                margin: 0;
                padding: 15px;
                max-width: none;
            }
            
            .header {
                page-break-after: avoid;
                margin-bottom: 15px;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .company-info {
                page-break-after: avoid;
                margin-bottom: 15px;
                border: 2px solid #000;
            }
            
            .report-details {
                page-break-after: avoid;
                margin-bottom: 15px;
            }
            
            .attendance-table {
                page-break-inside: auto;
                border: 2px solid #000 !important;
            }
            
            .attendance-table thead {
                display: table-header-group;
                page-break-after: avoid;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table tbody {
                display: table-row-group;
            }
            
            .attendance-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .attendance-table th {
                page-break-after: avoid;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border: 1px solid #000 !important;
            }
            
            .attendance-table td {
                page-break-inside: avoid;
                background: white !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border: 1px solid #000 !important;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                border: 2px solid #000 !important;
            }
        }
        
        /* أعمدة الجدول */
        .col-index { width: 30px; }
        .col-employee { width: 140px; }
        .col-job-number { width: 60px; }
        .col-civil { width: 90px; }
        .col-work-days { width: 50px; }
        .col-day { width: 18px; }
        .col-absence { width: 50px; }
        
        /* تحسين عرض الأيام */
        .day-column {
            min-width: 18px;
            max-width: 18px;
            overflow: hidden;
        }
        
        .day-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            line-height: 1.1;
        }
        
        .day-date {
            font-size: 6px;
            margin-top: 2px;
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
            <div class="company-name">{{ $report->company->name_ar }}</div>
            <div class="company-subtitle">{{ $report->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -25px;">
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
                        <th class="col-day day-column {{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                            <div class="day-text">
                                {{ $day['name'] }}<br>
                                <span class="day-date">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</span>
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
                                <td class="col-day day-column">
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
                                <td class="col-day day-column">
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

