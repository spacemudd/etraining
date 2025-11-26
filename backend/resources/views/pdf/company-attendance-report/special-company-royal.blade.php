<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف - التصميم الملكي</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: 'Arial', 'Tahoma', sans-serif;
            background: #0C2D66;
            color: #FFFFFF;
            line-height: 1.4;
            padding: 20px;
        }
        
        .container {
            background: #0C2D66;
            border: 3px solid #E2C044;
            border-radius: 12px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 8px 24px rgba(12, 45, 102, 0.5);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            color: #E2C044;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 3px solid #E2C044;
            box-shadow: 0 4px 12px rgba(12, 45, 102, 0.4);
            page-break-after: avoid;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #E2C044 0%, #f4d76e 50%, #E2C044 100%);
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            color: #FFFFFF !important;
        }
        
        .header p {
            font-size: 16px;
            color: #FFFFFF !important;
            font-weight: 300;
        }
        
        .company-info {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 2px solid #E2C044;
            box-shadow: 0 4px 8px rgba(226, 192, 68, 0.3);
            page-break-after: avoid;
            position: relative;
        }
        
        .company-info::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, #E2C044 0%, #f4d76e 100%);
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #FFFFFF !important;
            margin-bottom: 8px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .company-subtitle {
            color: #E2C044 !important;
            font-size: 16px;
            font-weight: 500;
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
            page-break-after: avoid;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            border: 2px solid #E2C044;
            padding: 18px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(226, 192, 68, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #E2C044 0%, #f4d76e 50%, #E2C044 100%);
        }
        
        .detail-label {
            font-weight: bold;
            color: #FFFFFF !important;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 20px;
            font-weight: bold;
            color: #E2C044 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border: 2px solid #E2C044;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
            box-shadow: 0 4px 12px rgba(12, 45, 102, 0.2);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%) !important;
            color: #E2C044 !important;
            padding: 14px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #E2C044;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            page-break-after: avoid;
        }
        
        .attendance-table td {
            padding: 10px 4px;
            text-align: center;
            border: 1px solid #E2C044;
            background: #1a4a8a;
            font-size: 10px;
            vertical-align: middle;
            page-break-inside: avoid;
            color: #FFFFFF !important;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #0C2D66;
        }
        
        .employee-name {
            font-weight: bold;
            color: #FFFFFF !important;
            font-size: 12px;
            margin-bottom: 3px;
            line-height: 1.3;
        }
        
        .employee-id {
            color: #E2C044 !important;
            font-size: 9px;
            line-height: 1.2;
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 1px solid rgba(12, 45, 102, 0.3);
        }
        
        .present {
            background: #E2C044 !important;
            color: #0C2D66 !important;
            font-weight: bold;
        }
        
        .absent {
            background: #c0392b !important;
            color: #ffffff !important;
        }
        
        .vacation {
            background: #d68910 !important;
            color: #ffffff !important;
        }
        
        .day-header {
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%) !important;
            color: #E2C044 !important;
            border: 1px solid #E2C044 !important;
        }
        
        .vacation-day {
            background: #8b6914 !important;
            color: #E2C044 !important;
            border: 1px solid #E2C044 !important;
        }
        
        .logo {
            max-width: 90px;
            height: auto;
            border-radius: 6px;
            border: 2px solid #E2C044;
            box-shadow: 0 3px 6px rgba(12, 45, 102, 0.2);
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            padding: 18px;
            background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%);
            border-radius: 8px;
            border-top: 3px solid #E2C044;
            page-break-before: avoid;
        }
        
        .footer p {
            color: #FFFFFF !important;
            font-size: 12px;
            margin: 0;
            font-weight: 500;
        }
        
        /* تحسينات الطباعة والتقسيم */
        @media print {
            body {
                background: #0C2D66;
                padding: 0;
                margin: 0;
            }
            
            .container {
                box-shadow: none;
                border: 2px solid #E2C044;
                margin: 0;
                padding: 10px;
                max-width: none;
                background: #0C2D66 !important;
            }
            
            .header {
                page-break-after: avoid;
                margin-bottom: 15px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .company-info {
                page-break-after: avoid;
                margin-bottom: 15px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .report-details {
                page-break-after: avoid;
                margin-bottom: 15px;
            }
            
            .attendance-table {
                page-break-inside: auto;
                border: 2px solid #E2C044;
            }
            
            .attendance-table thead {
                display: table-header-group;
                page-break-after: avoid;
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
                background: linear-gradient(135deg, #0C2D66 0%, #1a4a8a 100%) !important;
                color: #E2C044 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table td {
                page-break-inside: avoid;
                background: #1a4a8a !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #0C2D66 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .detail-card {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .footer {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* أعمدة الجدول محسنة */
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
            <p>Attendance & Absence Report - Royal Design</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">{{ $report->company->name_ar }}</div>
            <div class="company-subtitle">{{ $report->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -50px;">
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
        <div class="footer">
            <p>تم إنشاؤه تلقائياً بواسطة نظام إدارة التدريب - التصميم الملكي</p>
        </div>
    </div>
</body>
</html>

