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
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: #ffffff;
            color: #000000;
            line-height: 1.4;
            padding: 15px;
        }
        
        .report-container {
            background: #ffffff;
            max-width: 100%;
            margin: 0 auto;
            border: 2px solid #000000;
        }
        
        /* Header Section - Excel Style */
        .report-header {
            background: #d9d9d9;
            border-bottom: 2px solid #000000;
            padding: 20px;
        }
        
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #000000;
            text-align: center;
            flex: 1;
        }
        
        .logo-container {
            width: 100px;
        }
        
        .company-logo {
            max-width: 100px;
            height: auto;
            border: 1px solid #000000;
            background: #ffffff;
            padding: 3px;
        }
        
        /* Info Grid - Excel Style */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: #000000;
            border: 1px solid #000000;
            margin-bottom: 0;
        }
        
        .info-cell {
            background: #f2f2f2;
            padding: 10px;
            border: none;
        }
        
        .info-label {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
        }
        
        /* Table Section - Excel Style */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            background: #ffffff;
        }
        
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #000000;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
        }
        
        .attendance-table thead th {
            background: #bfbfbf;
            color: #000000;
            font-weight: bold;
            font-size: 10px;
        }
        
        .attendance-table thead th.header-main {
            background: #808080;
            color: #ffffff;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .attendance-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }
        
        /* Column Widths */
        .col-number {
            width: 35px;
        }
        
        .col-name {
            width: 180px;
            text-align: right;
            padding-right: 8px;
        }
        
        .col-job-number {
            width: 100px;
        }
        
        .col-national-id {
            width: 120px;
        }
        
        .col-work-days {
            width: 70px;
            background: #e6e6e6;
        }
        
        .col-day {
            width: 28px;
            font-size: 9px;
        }
        
        .col-absence {
            width: 70px;
            background: #e6e6e6;
        }
        
        /* Day Headers */
        .day-normal {
            background: #d9d9d9;
        }
        
        .day-weekend {
            background: #a6a6a6;
            color: #ffffff;
        }
        
        /* Attendance Marks - Excel Style */
        .mark-present {
            color: #000000;
            font-weight: bold;
            font-size: 14px;
        }
        
        .mark-absent {
            color: #000000;
            font-weight: bold;
            font-size: 14px;
        }
        
        .mark-vacation {
            color: #666666;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Employee Info */
        .employee-name {
            font-weight: bold;
            color: #000000;
            font-size: 11px;
        }
        
        .employee-id {
            color: #666666;
            font-size: 9px;
            margin-top: 2px;
        }
        
        /* Day Name in Header */
        .day-name-vertical {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            line-height: 1.2;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .report-container {
                border: 1px solid #000000;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header Section -->
        <div class="report-header">
            <div class="header-row">
                <div style="width: 100px;"></div>
                <div class="header-title">تقرير الحضور والانصراف</div>
                @if ($base64logo)
                    <div class="logo-container">
                        <img src="{{ $base64logo }}" alt="Logo" class="company-logo">
                    </div>
                @else
                    <div style="width: 100px;"></div>
                @endif
            </div>
            
            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-cell">
                    <div class="info-label">اسم الشركة</div>
                    <div class="info-value">{{ $report->company->name_ar }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">رقم التقرير</div>
                    <div class="info-value">{{ $report->number }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">من تاريخ</div>
                    <div class="info-value">{{ $report->date_from->format('Y-m-d') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">إلى تاريخ</div>
                    <div class="info-value">{{ $report->date_to->format('Y-m-d') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="col-number header-main">م</th>
                    <th class="col-name header-main">اسم الموظف</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th class="col-job-number header-main">الرقم الوظيفي</th>
                    @endif
                    <th class="col-national-id header-main">السجل المدني</th>
                    <th class="col-work-days header-main">أيام العمل</th>
                    @foreach (($days ?? []) as $day)
                        <th class="col-day {{ $day['vacation_day'] ? 'day-weekend' : 'day-normal' }}">
                            <div class="day-name-vertical">
                                {{ $day['name'] }}<br>
                                <small style="font-size: 7px;">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</small>
                            </div>
                        </th>
                    @endforeach
                    <th class="col-absence header-main">أيام الغياب</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($active_trainees))
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        @foreach ($active_trainees as $counter => $record)
                            @if ($record->status === 'temporary_stop')
                                @continue
                            @endif
                            <tr>
                                <td class="col-number">{{ $loop->iteration }}</td>
                                <td class="col-name">
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">معرف: {{ $record->trainee->id }}</div>
                                </td>
                                <td class="col-job-number">{{ $record->trainee->job_number }}</td>
                                <td class="col-national-id">{{ $record->trainee->clean_identity_number }}</td>
                                <td class="col-work-days">
                                    @php
                                        $workDays = 0;
                                        foreach ($days ?? [] as $day) {
                                            if ($record->start_date) {
                                                if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                    $workDays++;
                                                }
                                            } else {
                                                $workDays++;
                                            }
                                        }
                                    @endphp
                                    {{ $workDays }}
                                </td>
                                @for($i=0;$i<count($days ?? []);$i++)
                                    <td class="col-day {{ $days[$i]['vacation_day'] ? 'day-weekend' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                                {{-- Weekend after resignation date - show empty --}}
                                            @else
                                                <span class="mark-vacation">X</span>
                                            @endif
                                        @else
                                            @if ($record->start_date)
                                                @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                    <span class="mark-present">✓</span>
                                                @else
                                                    @if ($record->status === 'new_registration')
                                                        <span class="mark-absent">✗</span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="mark-present">✓</span>
                                            @endif
                                        @endif
                                    </td>
                                @endfor
                                <td class="col-absence">0</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($active_trainees as $counter => $record)
                            @if ($record->status === 'temporary_stop')
                                @continue
                            @endif
                            <tr>
                                <td class="col-number">{{ $loop->iteration }}</td>
                                <td class="col-name">
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">معرف: {{ $record->trainee->id }}</div>
                                </td>
                                <td class="col-national-id">{{ $record->trainee->clean_identity_number }}</td>
                                <td class="col-work-days">
                                    @php
                                        $workDays = 0;
                                        foreach ($days ?? [] as $day) {
                                            if ($record->start_date) {
                                                if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                    $workDays++;
                                                }
                                            } else {
                                                $workDays++;
                                            }
                                        }
                                    @endphp
                                    {{ $workDays }}
                                </td>
                                @for($i=0;$i<count($days ?? []);$i++)
                                    <td class="col-day {{ $days[$i]['vacation_day'] ? 'day-weekend' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                                {{-- Weekend after resignation date - show empty --}}
                                            @else
                                                <span class="mark-vacation">X</span>
                                            @endif
                                        @else
                                            @if ($record->start_date)
                                                @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                    <span class="mark-present">✓</span>
                                                @else
                                                    @if ($record->status === 'new_registration')
                                                        <span class="mark-absent">✗</span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="mark-present">✓</span>
                                            @endif
                                        @endif
                                    </td>
                                @endfor
                                <td class="col-absence">0</td>
                            </tr>
                        @endforeach
                    @endif
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>