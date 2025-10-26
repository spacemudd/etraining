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
            font-family: 'Times New Roman', serif;
            background: white;
            color: #000;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 20px;
            margin: 0 auto;
            max-width: 1400px;
        }
        
        .header {
            text-align: center;
            background: #000;
            color: white;
            padding: 30px;
            margin-bottom: 25px;
            border-bottom: 4px solid #333;
            position: relative;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: #000;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 14px;
        }
        
        .company-info {
            background: #fafafa;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 6px solid #000;
            border-right: 1px solid #ddd;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            position: relative;
            page-break-after: avoid;
        }
        
        .company-info::before {
            content: '';
            position: absolute;
            top: 0;
            right: -6px;
            width: 6px;
            height: 100%;
            background: #333;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            border-bottom: 3px solid #333;
            display: inline-block;
            padding-bottom: 5px;
        }
        
        .company-subtitle {
            color: #666;
            font-size: 14px;
            font-style: italic;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            page-break-after: avoid;
        }
        
        .detail-card {
            background: white;
            border: 1px solid #ddd;
            border-top: 4px solid #000;
            padding: 18px;
            text-align: center;
            position: relative;
        }
        
        .detail-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 2px;
            background: #333;
        }
        
        .detail-label {
            font-weight: bold;
            color: #333;
            font-size: 11px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-top: 5px;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 25px;
            border: 1px solid #000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .attendance-table th {
            background: #1a1a1a;
            color: white;
            padding: 15px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #000;
            border-bottom: 3px solid #000;
            page-break-after: avoid;
            position: relative;
        }
        
        .attendance-table th:not(:last-child) {
            border-right: 2px solid #333;
        }
        
        .attendance-table td {
            padding: 10px 4px;
            text-align: center;
            border: 1px solid #ddd;
            background: white;
            font-size: 9px;
            vertical-align: middle;
            page-break-inside: avoid;
            position: relative;
        }
        
        .attendance-table td:not(:last-child) {
            border-right: 1px solid #eee;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .attendance-table tr:hover td {
            background: #f5f5f5 !important;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #fafafa;
        }
        
        .attendance-table tr:nth-child(odd) td {
            background: white;
        }
        
        .employee-name {
            font-weight: bold;
            color: #000;
            font-size: 11px;
            margin-bottom: 2px;
            line-height: 1.3;
        }
        
        .employee-id {
            color: #666;
            font-size: 8px;
            line-height: 1.2;
            font-style: italic;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 3px;
            line-height: 18px;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            position: relative;
        }
        
        .present {
            background: #f8f8f8;
            color: #000;
            border: 2px solid #333;
            box-shadow: inset 0 0 3px rgba(0,0,0,0.1);
        }
        
        .absent {
            background: #555;
            color: white;
            border: 2px solid #000;
            box-shadow: inset 0 0 3px rgba(0,0,0,0.3);
        }
        
        .vacation {
            background: #e0e0e0;
            color: #333;
            border: 2px solid #999;
            box-shadow: inset 0 0 3px rgba(0,0,0,0.1);
        }
        
        .day-header {
            background: #000 !important;
            color: white !important;
        }
        
        .vacation-day {
            background: #333 !important;
            color: white !important;
        }
        
        .logo {
            max-width: 70px;
            height: auto;
            border: 2px solid #000;
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
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -40px; border: 2px solid #000;">
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

