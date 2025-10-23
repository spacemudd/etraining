<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف - التصميم المتدرج العصري</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            box-shadow: 0 15px 35px rgba(255, 154, 158, 0.3);
            overflow: hidden;
            border: 3px solid #ff6b6b;
        }
        
        .header-accent {
            background: linear-gradient(90deg, #ff6b6b 0%, #ff8e8e 25%, #ffa8a8 50%, #ff8e8e 75%, #ff6b6b 100%);
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
            background: linear-gradient(90deg, #ffd93d 0%, #ff6b6b 50%, #ffd93d 100%);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 50%, #ffa8a8 100%);
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
        }
        
        .hero-subtitle {
            font-size: 18px;
            opacity: 0.95;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }
        
        .content-wrapper {
            padding: 30px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
        }
        
        .company-showcase {
            display: table;
            width: 100%;
            background: linear-gradient(135deg, #ffd93d 0%, #ffed4e 50%, #ffd93d 100%);
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            border: 4px solid #ff6b6b;
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.2);
        }
        
        .company-info {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        
        .company-name {
            font-size: 30px;
            font-weight: bold;
            color: #d63031;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .company-subtitle {
            font-size: 18px;
            color: #e17055;
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
            border: 3px solid #ff6b6b;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 20px 0;
        }
        
        .stat-card {
            display: table-cell;
            background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 50%, #a8e6cf 100%);
            padding: 25px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(168, 230, 207, 0.3);
            border: 3px solid #00b894;
            position: relative;
            overflow: hidden;
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
        }
        
        .stat-label {
            font-weight: 700;
            color: #2d3748;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .stat-value {
            font-size: 26px;
            font-weight: bold;
            color: #00b894;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .attendance-container {
            background: white;
            border-radius: 18px;
            padding: 0;
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.15);
            overflow: hidden;
            margin-bottom: 25px;
            border: 3px solid #ff6b6b;
        }
        
        .table-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 50%, #ff6b6b 100%);
            color: white;
            padding: 22px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: white;
            padding: 14px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .attendance-table td {
            padding: 12px 8px;
            text-align: center;
            border: 2px solid #ffe8e8;
            font-size: 11px;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe8e8 100%);
        }
        
        .attendance-table tbody tr:hover {
            background: linear-gradient(135deg, #ffe8e8 0%, #ffd1d1 100%);
        }
        
        .day-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            font-weight: bold;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%);
            color: white;
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
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #e17055 0%, #d63031 100%);
            color: white;
        }
        
        .employee-info {
            text-align: right !important;
            padding-right: 15px !important;
        }
        
        .employee-name {
            font-weight: bold;
            color: #2d3748;
            font-size: 12px;
            margin-bottom: 4px;
        }
        
        .employee-id {
            font-size: 10px;
            color: #636e72;
        }
        
        .attendance-mark {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-block;
            line-height: 22px;
            font-weight: bold;
            font-size: 11px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
        
        .mark-present {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
        }
        
        .mark-absent {
            background: linear-gradient(135deg, #e17055 0%, #d63031 100%);
            color: white;
        }
        
        .mark-vacation {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
        }
        
        .mark-excuse {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }
        
        .mark-resignation {
            background: linear-gradient(135deg, #636e72 0%, #2d3436 100%);
            color: white;
        }
        
        .total-cell {
            font-weight: bold;
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            font-size: 13px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .legend-container {
            background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            border: 3px solid #00b894;
            box-shadow: 0 6px 15px rgba(168, 230, 207, 0.3);
        }
        
        .legend-title {
            font-weight: bold;
            color: #00b894;
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
            color: #2d3748;
        }
        
        .legend-symbol {
            display: inline-block;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            margin-bottom: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
        
        .footer-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            padding: 25px;
            text-align: center;
            color: white;
        }
        
        .footer-text {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.95;
        }
        
        .footer-brand {
            font-weight: bold;
            font-size: 16px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
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
                    <span class="stat-icon">📊</span>
                    <div class="stat-label">رقم التقرير</div>
                    <div class="stat-value">{{ $report->number }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📅</span>
                    <div class="stat-label">من تاريخ</div>
                    <div class="stat-value">{{ $report->date_from->format('Y-m-d') }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📅</span>
                    <div class="stat-label">إلى تاريخ</div>
                    <div class="stat-value">{{ $report->date_to->format('Y-m-d') }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">👥</span>
                    <div class="stat-label">عدد المتدربين</div>
                    <div class="stat-value">{{ $report->activeTraineesCount() }}</div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="legend-container">
                <div class="legend-title">📌 دليل الرموز</div>
                <div class="legend-items">
                    <div class="legend-item">
                        <span class="legend-symbol mark-present">ح</span>
                        <div>حضور</div>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol mark-absent">غ</span>
                        <div>غياب</div>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol mark-vacation">إ</span>
                        <div>إجازة</div>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol mark-excuse">ع</span>
                        <div>عذر</div>
                    </div>
                    <div class="legend-item">
                        <span class="legend-symbol mark-resignation">س</span>
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
                                <th style="width: 25px;" class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                                    {{ $day['name'] }}
                                </th>
                            @endforeach
                            <th style="width: 60px;">إجمالي الغياب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($active_trainees as $index => $trainee)
                            <tr>
                                <td style="font-weight: bold; color: #ff6b6b;">{{ $index + 1 }}</td>
                                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                                    <td style="font-weight: 600;">{{ $trainee->trainee->job_number ?? '-' }}</td>
                                @endif
                                <td>
                                    @if ($trainee->active)
                                        <span class="status-badge status-active">نشط</span>
                                    @else
                                        <span class="status-badge status-inactive">غير نشط</span>
                                    @endif
                                </td>
                                <td class="employee-info">
                                    <div class="employee-name">{{ $trainee->trainee->name_ar }}</div>
                                    <div class="employee-id">{{ $trainee->trainee->name_en }}</div>
                                </td>
                                <td style="font-weight: 600;">{{ $trainee->trainee->national_id }}</td>
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
                                                $attendance = $trainee->trainee_attendance_records
                                                    ->where('date', $day['date'])
                                                    ->first();
                                                
                                                // Check if this date is after resignation date
                                                $isAfterResignation = $trainee->end_date && $day['date_carbon']->isAfter($trainee->end_date);
                                            @endphp
                                            
                                            @if ($isAfterResignation)
                                                {{-- After resignation date - show resignation mark --}}
                                                <span class="attendance-mark mark-resignation">س</span>
                                            @elseif ($attendance)
                                                @if ($attendance->status == 'حضور')
                                                    <span class="attendance-mark mark-present">ح</span>
                                                @elseif ($attendance->status == 'غياب')
                                                    <span class="attendance-mark mark-absent">غ</span>
                                                @elseif ($attendance->status == 'إجازة')
                                                    <span class="attendance-mark mark-vacation">إ</span>
                                                @elseif ($attendance->status == 'عذر')
                                                    <span class="attendance-mark mark-excuse">ع</span>
                                                @else
                                                    {{ $attendance->status }}
                                                @endif
                                            @else
                                                @if ($trainee->start_date && $day['date_carbon']->isBefore($trainee->start_date))
                                                    {{-- Before start date - show empty --}}
                                                @else
                                                    {{-- No attendance record - show absent --}}
                                                    <span class="attendance-mark mark-absent">غ</span>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                                <td class="total-cell">
                                    {{ $trainee->trainee_attendance_records->where('status', 'غياب')->count() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="footer-section">
            <p class="footer-text">تم إنشاء هذا التقرير بواسطة نظام إدارة التدريب</p>
            <p class="footer-brand">🌟 {{ config('app.name') }} - Training Management System</p>
        </div>
    </div>
</body>
</html>
