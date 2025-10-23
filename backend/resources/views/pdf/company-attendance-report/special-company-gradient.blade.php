<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف - التصميم المتدرج</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #2c3e50;
            line-height: 1.6;
            padding: 15px;
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            padding: 0;
            margin: 0 auto;
            max-width: 1500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 8px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .hero-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 300;
        }
        
        .content-wrapper {
            padding: 25px;
        }
        
        .company-showcase {
            display: table;
            width: 100%;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid #667eea;
        }
        
        .company-info {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        
        .company-name {
            font-size: 26px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 16px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .logo-container {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            width: 150px;
        }
        
        .logo {
            max-width: 120px;
            max-height: 100px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 15px 0;
        }
        
        .stat-card {
            display: table-cell;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-top: 4px solid #667eea;
        }
        
        .stat-icon {
            font-size: 28px;
            margin-bottom: 10px;
            display: block;
        }
        
        .stat-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .attendance-container {
            background: white;
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
        }
        
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .attendance-table td {
            padding: 10px 6px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .attendance-table tbody tr:hover {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        }
        
        .day-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-weight: bold;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-active {
            background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);
            color: white;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .employee-info {
            text-align: right !important;
            padding-right: 12px !important;
        }
        
        .employee-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .employee-id {
            font-size: 9px;
            color: #6c757d;
        }
        
        .attendance-mark {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-block;
            line-height: 18px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .mark-present {
            background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);
            color: white;
        }
        
        .mark-absent {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .mark-vacation {
            background: linear-gradient(135deg, #ffd43b 0%, #fab005 100%);
            color: white;
        }
        
        .mark-excuse {
            background: linear-gradient(135deg, #74c0fc 0%, #339af0 100%);
            color: white;
        }
        
        .mark-resignation {
            background: linear-gradient(135deg, #868e96 0%, #495057 100%);
            color: white;
        }
        
        .total-cell {
            font-weight: bold;
            background: linear-gradient(135deg, #fff3bf 0%, #ffe066 100%);
            color: #2c3e50;
            font-size: 12px;
        }
        
        .footer-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            text-align: center;
            border-radius: 0 0 12px 12px;
            border-top: 3px solid #667eea;
        }
        
        .footer-text {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
        }
        
        .footer-brand {
            font-weight: bold;
            color: #667eea;
            font-size: 14px;
        }
        
        .legend-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #dee2e6;
        }
        
        .legend-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 12px;
            font-size: 14px;
            text-align: center;
        }
        
        .legend-items {
            display: table;
            width: 100%;
            border-spacing: 10px 0;
        }
        
        .legend-item {
            display: table-cell;
            text-align: center;
            font-size: 11px;
        }
        
        .legend-symbol {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            margin-bottom: 5px;
            vertical-align: middle;
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
        <!-- Header Gradient Line -->
        <div class="header-gradient"></div>
        
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">تقرير الحضور والانصراف</h1>
            <p class="hero-subtitle">Attendance & Absence Report - Gradient Design</p>
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
                                <td style="font-weight: bold; color: #667eea;">{{ $index + 1 }}</td>
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
