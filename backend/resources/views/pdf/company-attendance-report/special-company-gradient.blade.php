<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>تقرير الحضور والانصراف - التصميم المتدرج العصري</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* إعدادات أساسية لضمان وضوح الألوان */
        html, body {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 25%, #fecfef 50%, #fecfef 75%, #ff9a9e 100%);
            color: #2d3748;
            line-height: 1.6;
            padding: 15px;
        }
        
        .main-container {
            background: white;
            border-radius: 20px;
            padding: 0;
            margin: 0 auto;
            max-width: 1500px;
            box-shadow: 0 15px 35px rgba(74, 144, 226, 0.3);
            overflow: hidden;
            border: 3px solid #4A90E2;
        }
        
        .header-accent {
            background: linear-gradient(90deg, #4A90E2 0%, #5BA0F2 25%, #6BB0FF 50%, #5BA0F2 75%, #4A90E2 100%);
            height: 12px;
            position: relative;
        }
        
        .header-accent::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ffd93d 0%, #4A90E2 50%, #ffd93d 100%);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #4A90E2 0%, #5BA0F2 50%, #6BB0FF 100%);
            color: white;
            padding: 35px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }
        
        .hero-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
            color: #ffffff !important;
        }
        
        .hero-subtitle {
            font-size: 18px;
            opacity: 1;
            font-weight: 300;
            position: relative;
            z-index: 1;
            color: #ffffff !important;
        }
        
        .content-wrapper {
            padding: 30px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
        }
        
        .company-showcase {
            display: table;
            width: 100%;
            background: #ffd93d !important;
            background-color: #ffd93d !important;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            border: 4px solid #4A90E2;
            box-shadow: 0 8px 20px rgba(74, 144, 226, 0.2);
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .company-info {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        
        .company-name {
            font-size: 30px;
            font-weight: bold;
            color: #2c5282 !important;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .company-subtitle {
            font-size: 18px;
            color: #3182ce !important;
            font-weight: 600;
        }
        
        .logo-container {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            width: 160px;
        }
        
        .logo {
            max-width: 130px;
            max-height: 110px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            border: 3px solid #4A90E2;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 20px 0;
        }
        
        .stat-card {
            display: table-cell;
            background: #a8e6cf !important;
            background-color: #a8e6cf !important;
            padding: 25px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(168, 230, 207, 0.3);
            border: 3px solid #00b894;
            position: relative;
            overflow: hidden;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #00b894 0%, #00cec9 50%, #00b894 100%);
        }
        
        .stat-icon {
            font-size: 32px;
            margin-bottom: 12px;
            display: block;
            color: #2d3748 !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .stat-label {
            font-weight: 700;
            color: #2d3748 !important;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .stat-value {
            font-size: 26px;
            font-weight: bold;
            color: #00b894 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .attendance-container {
            background: white;
            border-radius: 18px;
            padding: 0;
            box-shadow: 0 10px 25px rgba(168, 230, 207, 0.15);
            overflow: hidden;
            margin-bottom: 25px;
            border: 3px solid #00b894;
        }
        
        .table-header {
            background: #00b894 !important;
            background-color: #00b894 !important;
            color: #ffffff !important;
            padding: 22px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        
        .attendance-table thead {
            display: table-header-group;
        }
        
        .attendance-table tbody {
            display: table-row-group;
        }
        
        .attendance-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .attendance-table th {
            background: #00b894 !important;
            background-color: #00b894 !important;
            color: #ffffff !important;
            padding: 14px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        
        .attendance-table td {
            padding: 12px 8px;
            text-align: center;
            border: 2px solid #ffe8e8;
            font-size: 11px;
            color: #2d3748 !important;
            background: #ffffff;
            page-break-inside: avoid;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
            page-break-inside: avoid;
        }
        
        .attendance-table tbody tr:hover {
            background: linear-gradient(135deg, #ffe8e8 0%, #ffd1d1 100%);
        }
        
        .attendance-table tbody tr {
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        
        .day-header {
            background: #00b894 !important;
            background-color: #00b894 !important;
            font-weight: bold;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .vacation-day {
            background: #fd79a8 !important;
            background-color: #fd79a8 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }
        
        .status-active {
            background: #00b894 !important;
            background-color: #00b894 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .status-inactive {
            background: #e17055 !important;
            background-color: #e17055 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .employee-info {
            text-align: right !important;
            padding-right: 15px !important;
        }
        
        .employee-name {
            font-weight: bold;
            color: #2d3748 !important;
            font-size: 13px;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .employee-id {
            font-size: 11px;
            color: #636e72 !important;
            line-height: 1.2;
        }
        
        .attendance-mark {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-block;
            line-height: 28px;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
            text-align: center;
            vertical-align: middle;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }
        
        .mark-present {
            background: #00b894 !important;
            background-color: #00b894 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .mark-absent {
            background: #e17055 !important;
            background-color: #e17055 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .mark-vacation {
            background: #fdcb6e !important;
            background-color: #fdcb6e !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .mark-excuse {
            background: #74b9ff !important;
            background-color: #74b9ff !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .mark-resignation {
            background: #636e72 !important;
            background-color: #636e72 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .total-cell {
            font-weight: bold;
            background: #fdcb6e !important;
            background-color: #fdcb6e !important;
            color: #ffffff !important;
            font-size: 13px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .legend-container {
            background: #a8e6cf !important;
            background-color: #a8e6cf !important;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border: 3px solid #00b894;
            box-shadow: 0 6px 15px rgba(168, 230, 207, 0.3);
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .legend-title {
            font-weight: bold;
            color: #00b894 !important;
            margin-bottom: 15px;
            font-size: 16px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .legend-items {
            display: table;
            width: 100%;
            border-spacing: 15px 0;
        }
        
        .legend-item {
            display: table-cell;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #2d3748 !important;
        }
        
        .legend-symbol {
            display: inline-block;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            margin-bottom: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
            border: 2px solid rgba(255, 255, 255, 0.8);
            line-height: 28px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }
        
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .main-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            /* ضمان وضوح الألوان في الطباعة */
            .hero-title, .hero-subtitle {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .company-name, .company-subtitle {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-label, .stat-value {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .table-header {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table th {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            
            .attendance-table thead {
                display: table-header-group !important;
            }
            
            .attendance-table tbody {
                display: table-row-group !important;
            }
            
            .attendance-table tr {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
            
            .attendance-table td {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                page-break-inside: avoid;
            }
            
            .employee-name, .employee-id {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .legend-title, .legend-item {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .footer-text, .footer-brand {
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .mark-present, .mark-absent, .mark-vacation, .mark-excuse, .mark-resignation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-icon {
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header Accent Line -->
        <div class="header-accent"></div>
        
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">تقرير الحضور والانصراف</h1>
            <p class="hero-subtitle">Attendance & Absence Report - Modern Gradient Design</p>
        </div>
        
        <div class="content-wrapper">
            <!-- Company Showcase -->
            <div class="company-showcase">
                <div class="company-info">
                    <div class="company-name">{{ $report->company->name_ar }}</div>
                    <div class="company-subtitle">{{ $report->company->name_en }}</div>
                </div>
                @if ($base64logo)
                    <div class="logo-container">
                        <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
                    </div>
                @endif
            </div>
            
            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">رقم التقرير</div>
                    <div class="stat-value">{{ $report->number }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">من تاريخ</div>
                    <div class="stat-value">{{ $report->date_from->format('Y-m-d') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">إلى تاريخ</div>
                    <div class="stat-value">{{ $report->date_to->format('Y-m-d') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">عدد المتدربين</div>
                    <div class="stat-value">{{ $report->activeTraineesCount() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">أيام العمل</div>
                    <div class="stat-value">{{ count($days) }}</div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="legend-container">
                <div class="legend-title">📌 دليل الرموز</div>
                <div class="legend-items">
                    <div class="legend-item">
                        <span style="width: 28px; height: 28px; border-radius: 50%; margin-bottom: 8px; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8); line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; display: inline-block; background: #00b894 !important; color: #ffffff !important;">ح</span>
                        <div>حضور</div>
                    </div>
                    <div class="legend-item">
                        <span style="width: 28px; height: 28px; border-radius: 50%; margin-bottom: 8px; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8); line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; display: inline-block; background: #e17055 !important; color: #ffffff !important;">غ</span>
                        <div>غياب</div>
                    </div>
                    <div class="legend-item">
                        <span style="width: 28px; height: 28px; border-radius: 50%; margin-bottom: 8px; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8); line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; display: inline-block; background: #fdcb6e !important; color: #ffffff !important;">إ</span>
                        <div>إجازة</div>
                    </div>
                    <div class="legend-item">
                        <span style="width: 28px; height: 28px; border-radius: 50%; margin-bottom: 8px; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8); line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; display: inline-block; background: #636e72 !important; color: #ffffff !important;">س</span>
                        <div>استقالة</div>
                    </div>
                </div>
            </div>
            
            <!-- Attendance Table -->
            <div class="attendance-container">
                <div class="table-header">
                    📋 جدول الحضور والانصراف التفصيلي
                </div>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">م</th>
                            @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                                <th style="width: 70px;">الرقم الوظيفي</th>
                            @endif
                            <th style="width: 80px;">الحالة</th>
                            <th style="width: 200px;">اسم المتدرب</th>
                            <th style="width: 100px;">رقم الهوية</th>
                            @foreach ($days as $day)
                                <th style="width: 30px;" class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                                    <div style="font-size: 9px; line-height: 1.2;">
                                        {{ $day['name'] }}<br>
                                        <span style="font-size: 7px;">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</span>
                                    </div>
                                </th>
                            @endforeach
                            <th style="width: 60px;">إجمالي الحضور</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($active_trainees->count() == 0)
                            <tr>
                                <td colspan="{{ 5 + count($days) }}" style="text-align: center; padding: 20px; color: #666;">
                                    لا توجد بيانات متدربين متاحة
                                </td>
                            </tr>
                        @endif
                        @foreach ($active_trainees as $index => $trainee)
                            <tr>
                                <td style="font-weight: bold; color: #00b894 !important;">{{ $index + 1 }}</td>
                                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                                    <td style="font-weight: 600; color: #2d3748 !important;">{{ $trainee->trainee->job_number ?? '-' }}</td>
                                @endif
                                <td>
                                    @if ($trainee->active)
                                        <span style="background: #00b894 !important; color: #ffffff !important; padding: 6px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; display: inline-block; box-shadow: 0 3px 8px rgba(0,0,0,0.2);">نشط</span>
                                    @else
                                        <span style="background: #e17055 !important; color: #ffffff !important; padding: 6px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; display: inline-block; box-shadow: 0 3px 8px rgba(0,0,0,0.2);">غير نشط</span>
                                    @endif
                                </td>
                                <td class="employee-info">
                                    <div class="employee-name">{{ $trainee->trainee->name_ar ?? $trainee->trainee->name ?? 'غير محدد' }}</div>
                                    <div class="employee-id">{{ $trainee->trainee->name_en ?? $trainee->trainee->english_name ?? '' }}</div>
                                    @if($trainee->trainee->attendanceReportRecords->count() > 0)
                                        <div style="font-size: 8px; color: #28a745; margin-top: 2px;">
                                            {{ $trainee->trainee->attendanceReportRecords->count() }} سجل حضور
                                        </div>
                                    @endif
                                </td>
                                <td style="font-weight: 600;">{{ $trainee->trainee->national_id ?? $trainee->trainee->identity_number ?? 'غير محدد' }}</td>
                                @foreach ($days as $day)
                                    <td>
                                        @if ($day['vacation_day'])
                                            @if ($trainee->start_date && $day['date_carbon']->isAfter($trainee->end_date))
                                                {{-- Weekend after resignation date - show empty --}}
                                            @else
                                                <span class="attendance-mark mark-vacation">إ</span>
                                            @endif
                                        @else
                                            @php
                                                $attendance = $trainee->trainee->attendanceReportRecords
                                                    ->where('date', $day['date'])
                                                    ->first();
                                                
                                                // Check if this date is after resignation date
                                                $isAfterResignation = $trainee->end_date && $day['date_carbon']->isAfter($trainee->end_date);
                                                
                                                // Check if this date is before start date
                                                $isBeforeStart = $trainee->start_date && $day['date_carbon']->isBefore($trainee->start_date);
                                            @endphp
                                            
                                            @if ($isAfterResignation)
                                                {{-- After resignation date - show resignation mark --}}
                                                <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #636e72 !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">س</span>
                                            @elseif ($isBeforeStart)
                                                {{-- Before start date - show empty --}}
                                                <span style="color: #ccc;">-</span>
                                            @elseif ($attendance)
                                                @if ($attendance->status == 'حضور')
                                                    <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #00b894 !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">ح</span>
                                                @elseif ($attendance->status == 'غياب')
                                                    {{-- تغيير الغياب إلى حضور --}}
                                                    <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #00b894 !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">ح</span>
                                                @elseif ($attendance->status == 'إجازة')
                                                    <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #fdcb6e !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">إ</span>
                                                @elseif ($attendance->status == 'عذر')
                                                    {{-- تغيير العذر إلى حضور --}}
                                                    <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #00b894 !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">ح</span>
                                                @else
                                                    <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #00b894 !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">ح</span>
                                                @endif
                                            @else
                                                {{-- No attendance record - show present by default --}}
                                                <span style="width: 28px; height: 28px; border-radius: 50%; display: inline-block; line-height: 28px; font-weight: bold; font-size: 14px; text-align: center; vertical-align: middle; background: #00b894 !important; color: #ffffff !important; box-shadow: 0 3px 6px rgba(0,0,0,0.3); border: 2px solid rgba(255, 255, 255, 0.8);">ح</span>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                                <td class="total-cell">
                                    @php
                                        $presentCount = 0;
                                        foreach ($days as $day) {
                                            if (!$day['vacation_day']) {
                                                $attendance = $trainee->trainee->attendanceReportRecords
                                                    ->where('date', $day['date'])
                                                    ->first();
                                                
                                                $isAfterResignation = $trainee->end_date && $day['date_carbon']->isAfter($trainee->end_date);
                                                $isBeforeStart = $trainee->start_date && $day['date_carbon']->isBefore($trainee->start_date);
                                                
                                                if (!$isAfterResignation && !$isBeforeStart) {
                                                    // حساب الحضور (جميع الحالات تعتبر حضور الآن)
                                                    $presentCount++;
                                                }
                                            }
                                        }
                                    @endphp
                                    <strong>{{ $presentCount }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</body>
</html>
