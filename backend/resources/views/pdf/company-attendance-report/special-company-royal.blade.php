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
            background: #f5f5f5;
            color: #2c3e50;
            line-height: 1.5;
            padding: 20px;
        }
        
        .container {
            background: #ffffff;
            border: 4px solid #d4af37;
            border-radius: 16px;
            padding: 30px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), inset 0 0 60px rgba(212, 175, 55, 0.02);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            color: #2c3e50;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 3px solid #d4af37;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            page-break-after: avoid;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d4af37 0%, #f4d76e 30%, #d4af37 70%, #f4d76e 100%);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.6);
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.5) 50%, transparent 100%);
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            color: #2c3e50 !important;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 17px;
            color: #d4af37 !important;
            font-weight: 500;
            text-shadow: none;
            letter-spacing: 0.5px;
        }
        
        .company-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 3px solid #d4af37;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            page-break-after: avoid;
            position: relative;
            overflow: hidden;
        }
        
        .company-info::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, #d4af37 0%, #f4d76e 50%, #d4af37 100%);
            box-shadow: -2px 0 8px rgba(212, 175, 55, 0.5);
        }
        
        .company-info::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, transparent 0%, rgba(212, 175, 55, 0.3) 50%, transparent 100%);
        }
        
        .company-name {
            font-size: 26px;
            font-weight: 700;
            color: #2c3e50 !important;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }
        
        .company-subtitle {
            color: #d4af37 !important;
            font-size: 17px;
            font-weight: 600;
            text-shadow: none;
        }
        
        .report-details {
            width: 100%;
            margin-bottom: 25px;
            page-break-after: avoid;
            overflow: hidden;
            display: block;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #ffffff 100%);
            border: 3px solid #d4af37;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
            float: right;
            width: 23%;
            margin-left: 2.66%;
            box-sizing: border-box;
        }
        
        .detail-card:first-child {
            margin-left: 0;
        }
        
        .report-details::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37 0%, #f4d76e 40%, #d4af37 80%, #f4d76e 100%);
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.5);
        }
        
        .detail-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.3) 50%, transparent 100%);
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d !important;
            font-size: 13px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: none;
        }
        
        .detail-value {
            font-size: 24px;
            font-weight: 700;
            color: #d4af37 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border: 3px solid #d4af37;
            border-radius: 12px;
            overflow: hidden;
            table-layout: fixed;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5), inset 0 0 40px rgba(212, 175, 55, 0.05);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
            color: #2c3e50 !important;
            padding: 16px 6px;
            text-align: center;
            font-weight: 700;
            font-size: 12px;
            border: 2px solid #d4af37;
            text-shadow: none;
            page-break-after: avoid;
            letter-spacing: 0.5px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        
        .attendance-table td {
            padding: 12px 4px;
            text-align: center;
            border: 1px solid rgba(212, 175, 55, 0.3);
            background: #ffffff;
            font-size: 10px;
            vertical-align: middle;
            page-break-inside: avoid;
            color: #2c3e50 !important;
            transition: background 0.2s ease;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-table tr:hover td {
            background: rgba(212, 175, 55, 0.1) !important;
        }
        
        .employee-name {
            font-weight: 600;
            color: #2c3e50 !important;
            font-size: 12px;
            margin-bottom: 4px;
            line-height: 1.4;
            text-shadow: none;
        }
        
        .employee-id {
            color: #6c757d !important;
            font-size: 9px;
            line-height: 1.3;
            font-weight: 500;
            text-shadow: none;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            line-height: 22px;
            font-weight: 700;
            font-size: 11px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(0, 0, 0, 0.2);
        }
        
        .present {
            background: linear-gradient(135deg, #d4af37 0%, #f4d76e 100%) !important;
            color: #0a1f3d !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.3);
        }
        
        .absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none;
        }
        
        .attendance-mark.absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        span.attendance-mark.absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        .vacation {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none;
        }
        
        .attendance-mark.vacation {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        span.attendance-mark.vacation {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            font-weight: 700 !important;
            text-shadow: none !important;
        }
        
        .day-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
            color: #2c3e50 !important;
            border: 2px solid #d4af37 !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
            color: #856404 !important;
            border: 2px solid #d4af37 !important;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
            border: 3px solid #d4af37;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 22px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            border-radius: 12px;
            border-top: 4px solid #d4af37;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            page-break-before: avoid;
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37 0%, #f4d76e 50%, #d4af37 100%);
        }
        
        .footer p {
            color: #6c757d !important;
            font-size: 13px;
            margin: 0;
            font-weight: 500;
            text-shadow: none;
            letter-spacing: 0.3px;
        }
        
        /* تحسينات الطباعة والتقسيم */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            
            .container {
                box-shadow: none;
                border: 3px solid #d4af37;
                margin: 0;
                padding: 15px;
                max-width: none;
                background: #ffffff !important;
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
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%) !important;
                color: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table td {
                page-break-inside: avoid;
                background: #ffffff !important;
                color: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #f8f9fa !important;
                color: #2c3e50 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .absent, .attendance-mark.absent, span.attendance-mark.absent {
                background: #ffffff !important;
                color: #dc3545 !important;
                border: 2px solid #dc3545 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .vacation, .attendance-mark.vacation, span.attendance-mark.vacation {
                background: #ffffff !important;
                color: #dc3545 !important;
                border: 2px solid #dc3545 !important;
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
            <p>Attendance & Absence Report</p>
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
                                            <span class="attendance-mark vacation" style="background: #ffffff !important; color: #dc3545 !important; border: 2px solid #dc3545 !important;">X</span>
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                <span class="attendance-mark present">✓</span>
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    <span class="attendance-mark absent" style="background: #ffffff !important; color: #dc3545 !important; border: 2px solid #dc3545 !important;">✗</span>
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
                                            <span class="attendance-mark vacation" style="background: #ffffff !important; color: #dc3545 !important; border: 2px solid #dc3545 !important;">X</span>
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                <span class="attendance-mark present">✓</span>
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    <span class="attendance-mark absent" style="background: #ffffff !important; color: #dc3545 !important; border: 2px solid #dc3545 !important;">✗</span>
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
        </div>
    </div>
</body>
</html>

