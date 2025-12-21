<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف - التصميم الأنيق</title>
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
            font-family: 'Arial', 'Tahoma', 'Segoe UI', sans-serif;
            background: #f8f9fa;
            color: #212529;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            background: #ffffff;
            border: 2px solid #8b5cf6;
            border-radius: 12px;
            padding: 25px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.1);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            page-break-after: avoid;
            box-shadow: 0 2px 10px rgba(139, 92, 246, 0.2);
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 700;
            color: #ffffff !important;
            letter-spacing: 0.5px;
        }
        
        .header p {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 400;
            letter-spacing: 0.3px;
        }
        
        .company-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            page-break-after: avoid;
            text-align: center;
            position: relative;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #212529 !important;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        
        .company-subtitle {
            color: #6c757d !important;
            font-size: 16px;
            font-weight: 500;
        }
        
        .report-details {
            width: 100% !important;
            margin-bottom: 25px !important;
            page-break-after: avoid !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            justify-content: space-between !important;
            align-items: stretch !important;
            gap: 15px !important;
            clear: both !important;
        }
        
        body .container .report-details,
        html body .container .report-details,
        .container .report-details {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .detail-card {
            background: #ffffff !important;
            border: 2px solid #8b5cf6 !important;
            padding: 20px 15px !important;
            border-radius: 12px !important;
            text-align: center !important;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.12) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            position: relative !important;
            overflow: hidden !important;
            min-height: 100px !important;
            flex: 1 1 auto !important;
            flex-basis: 0 !important;
            min-width: 0 !important;
            width: auto !important;
            max-width: none !important;
            box-sizing: border-box !important;
            float: none !important;
        }
        
        body .container .report-details .detail-card,
        html body .container .report-details .detail-card,
        .container .report-details .detail-card {
            flex: 1 1 auto !important;
            flex-basis: 0 !important;
            width: auto !important;
            max-width: none !important;
            float: none !important;
            display: flex !important;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8b5cf6 0%, #a78bfa 50%, #8b5cf6 100%);
        }
        
        .detail-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(139, 92, 246, 0.1);
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d !important;
            font-size: 11px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1.4;
        }
        
        .detail-value {
            font-size: 26px;
            font-weight: 700;
            color: #8b5cf6 !important;
            line-height: 1.2;
            margin-top: 4px;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border: 2px solid #8b5cf6;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
            box-shadow: 0 2px 10px rgba(139, 92, 246, 0.1);
        }
        
        .attendance-table th {
            background: #8b5cf6 !important;
            color: #ffffff !important;
            padding: 12px 6px;
            text-align: center;
            font-weight: 700;
            font-size: 11px;
            border: 1px solid #7c3aed;
            page-break-after: avoid;
            letter-spacing: 0.3px;
        }
        
        .attendance-table td {
            padding: 10px 4px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: #ffffff;
            font-size: 10px;
            vertical-align: middle;
            page-break-inside: avoid;
            color: #212529 !important;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .employee-name {
            font-weight: 600;
            color: #212529 !important;
            font-size: 11px;
            margin-bottom: 3px;
            line-height: 1.4;
        }
        
        .employee-id {
            color: #6c757d !important;
            font-size: 9px;
            line-height: 1.3;
            font-weight: 400;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            line-height: 20px;
            font-weight: 700;
            font-size: 10px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .present {
            background: #8b5cf6 !important;
            color: #ffffff !important;
            border: 1px solid #7c3aed !important;
        }
        
        .absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
        }
        
        .attendance-mark.absent,
        span.attendance-mark.absent {
            background: #ffffff !important;
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
        }
        
        .vacation {
            background: #ffffff !important;
            color: #f59e0b !important;
            border: 2px solid #f59e0b !important;
        }
        
        .attendance-mark.vacation,
        span.attendance-mark.vacation {
            background: #ffffff !important;
            color: #f59e0b !important;
            border: 2px solid #f59e0b !important;
        }
        
        .day-header {
            background: #8b5cf6 !important;
            color: #ffffff !important;
        }
        
        .vacation-day {
            background: #fef3c7 !important;
            color: #92400e !important;
        }
        
        .logo {
            max-width: 90px;
            height: auto;
            border-radius: 6px;
            border: 2px solid #8b5cf6;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2);
        }
        
        .footer {
            margin-top: 25px;
            text-align: center;
            padding: 18px;
            background: #f8f9fa;
            border-radius: 8px;
            border-top: 2px solid #8b5cf6;
            page-break-before: avoid;
        }
        
        .footer p {
            color: #6c757d !important;
            font-size: 12px;
            margin: 0;
            font-weight: 400;
        }
        
        /* أعمدة الجدول */
        .col-index { width: 35px; }
        .col-employee { width: 150px; }
        .col-job-number { width: 70px; }
        .col-civil { width: 100px; }
        .col-work-days { width: 60px; }
        .col-day { width: 20px; }
        .col-absence { width: 55px; }
        
        /* تحسين عرض الأيام */
        .day-column {
            min-width: 20px;
            max-width: 20px;
            overflow: hidden;
            padding: 4px 2px !important;
        }
        
        .day-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            line-height: 1.2;
            font-weight: 600;
        }
        
        .day-date {
            font-size: 7px;
            margin-top: 3px;
            opacity: 0.9;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            
            .container {
                box-shadow: none;
                border: 2px solid #8b5cf6;
                margin: 0;
                padding: 12px;
                max-width: none;
                background: #ffffff !important;
            }
            
            .header {
                page-break-after: avoid;
                margin-bottom: 12px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .company-info {
                page-break-after: avoid;
                margin-bottom: 12px;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .report-details {
                page-break-after: avoid;
                margin-bottom: 12px;
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: space-between !important;
                gap: 10px;
                width: 100%;
                clear: both !important;
            }
            
            .detail-card {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
                page-break-inside: avoid;
                flex: 1 1 auto !important;
                flex-basis: 0 !important;
                min-width: 0;
                max-width: none !important;
                width: auto !important;
                box-sizing: border-box;
                float: none !important;
            }
            
            .attendance-table {
                page-break-inside: auto;
                border: 2px solid #8b5cf6;
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
                background: #8b5cf6 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .attendance-table td {
                page-break-inside: avoid;
                background: #ffffff !important;
                color: #212529 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .attendance-table tr:nth-child(even) td {
                background: #f8f9fa !important;
                color: #212529 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .present, .absent, .vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .absent, .attendance-mark.absent, span.attendance-mark.absent {
                background: #ffffff !important;
                color: #dc3545 !important;
                border: 2px solid #dc3545 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .vacation, .attendance-mark.vacation, span.attendance-mark.vacation {
                background: #ffffff !important;
                color: #f59e0b !important;
                border: 2px solid #f59e0b !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .detail-card, .footer {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور والانصراف</h1>
            <p>Attendance & Absence Report - Elegant Design</p>
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

