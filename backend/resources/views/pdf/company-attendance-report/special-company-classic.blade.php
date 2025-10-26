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
            border: 2px solid #000;
            padding: 20px;
            margin: 0 auto;
            max-width: 1400px;
        }
        
        .header {
            text-align: center;
            background: #000;
            color: white;
            padding: 25px;
            margin-bottom: 20px;
            border: 3px solid #333;
            page-break-after: avoid;
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
            background: #f5f5f5;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #333;
            border-right: 5px solid #000;
            page-break-after: avoid;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .company-subtitle {
            color: #666;
            font-size: 14px;
            font-style: italic;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
            page-break-after: avoid;
        }
        
        .detail-card {
            background: white;
            border: 2px solid #333;
            padding: 15px;
            text-align: center;
        }
        
        .detail-label {
            font-weight: bold;
            color: #000;
            font-size: 12px;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border: 2px solid #000;
        }
        
        .attendance-table th {
            background: #000;
            color: white;
            padding: 12px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #000;
            page-break-after: avoid;
        }
        
        .attendance-table td {
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #333;
            background: white;
            font-size: 9px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
            border-bottom: 1px solid #ccc;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f9f9f9;
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
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            line-height: 16px;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }
        
        .present {
            background: white;
            color: #000;
            border: 2px solid #000;
        }
        
        .absent {
            background: #666;
            color: white;
            border: 2px solid #000;
        }
        
        .vacation {
            background: #ccc;
            color: #000;
            border: 2px solid #000;
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
        
        <!-- Footer -->
        <div style="margin-top: 25px; text-align: center; padding: 15px; background: white; border-top: 2px solid #000; page-break-before: avoid;">
            <p style="color: #666; font-size: 12px; margin: 0;">تم إنشاؤه تلقائياً بواسطة نظام إدارة التدريب</p>
        </div>
    </div>
</body>
</html>

