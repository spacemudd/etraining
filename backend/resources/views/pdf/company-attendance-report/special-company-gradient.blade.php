<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف - التصميم المتدرج المبتكر</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(45deg, #1e3c72 0%, #2a5298 25%, #ff6b6b 50%, #feca57 75%, #48dbfb 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            color: #2c3e50;
            line-height: 1.6;
            padding: 15px;
            min-height: 100vh;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 0;
            margin: 0 auto;
            max-width: 1500px;
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,255,255,0.1),
                inset 0 1px 0 rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }
        
        .main-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, 
                #ff6b6b 0%, 
                #feca57 20%, 
                #48dbfb 40%, 
                #ff9ff3 60%, 
                #54a0ff 80%, 
                #5f27cd 100%);
            animation: rainbowFlow 3s linear infinite;
        }
        
        @keyframes rainbowFlow {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        
        .hero-section {
            background: linear-gradient(135deg, 
                rgba(255, 107, 107, 0.9) 0%, 
                rgba(254, 202, 87, 0.9) 25%, 
                rgba(72, 219, 251, 0.9) 50%, 
                rgba(255, 159, 243, 0.9) 75%, 
                rgba(84, 160, 255, 0.9) 100%);
            background-size: 300% 300%;
            animation: heroGradient 8s ease infinite;
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes heroGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: sparkle 4s ease-in-out infinite;
        }
        
        @keyframes sparkle {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0.3; }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0.8; }
        }
        
        .hero-title {
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 15px;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
            background: linear-gradient(45deg, #fff, #f8f9fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 20px;
            font-weight: 400;
            opacity: 0.95;
            position: relative;
            z-index: 2;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        
        .content-wrapper {
            padding: 30px;
        }
        
        .company-showcase {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .company-showcase::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: companyGlow 6s ease-in-out infinite;
        }
        
        @keyframes companyGlow {
            0%, 100% { transform: translateX(50%) translateY(50%) rotate(30deg); }
            50% { transform: translateX(-50%) translateY(-50%) rotate(30deg); }
        }
        
        .company-info {
            position: relative;
            z-index: 2;
        }
        
        .company-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .company-subtitle {
            font-size: 18px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .logo-container {
            position: relative;
            z-index: 2;
        }
        
        .logo {
            max-width: 120px;
            height: auto;
            border-radius: 15px;
            border: 4px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.05) rotate(2deg);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border: none;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
            background-size: 200% 200%;
            animation: statGradient 3s ease infinite;
        }
        
        @keyframes statGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
            display: block;
        }
        
        .stat-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .attendance-container {
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 10px;
            text-align: center;
            font-weight: 700;
            font-size: 12px;
            border: none;
            position: relative;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .attendance-table th:first-child {
            border-top-left-radius: 0;
        }
        
        .attendance-table th:last-child {
            border-top-right-radius: 0;
        }
        
        .attendance-table td {
            padding: 15px 8px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: white;
            font-size: 11px;
            vertical-align: middle;
            transition: all 0.3s ease;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-table tr:hover td {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            transform: scale(1.01);
        }
        
        .employee-info {
            text-align: right;
        }
        
        .employee-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 13px;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .attendance-badge {
            display: inline-block;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            line-height: 26px;
            font-weight: 700;
            font-size: 11px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .attendance-badge::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .attendance-badge:hover {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        
        .attendance-badge:hover::before {
            opacity: 1;
        }
        
        .present {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .absent {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
            color: white;
        }
        
        .vacation {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #212529;
        }
        
        .day-header {
            background: linear-gradient(135deg, #007bff, #6f42c1) !important;
            color: white !important;
            font-weight: 700;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            color: white !important;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .status-active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        .summary-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 15px 30px rgba(240, 147, 251, 0.3);
        }
        
        .summary-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }
        
        .summary-item {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .summary-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.3);
        }
        
        .summary-number {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .summary-label {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        
        .footer-section {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(44, 62, 80, 0.3);
        }
        
        .footer-section p {
            margin: 8px 0;
            font-size: 14px;
        }
        
        .footer-highlight {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .main-container {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .attendance-table {
                box-shadow: none !important;
            }
            
            .stat-card:hover,
            .attendance-badge:hover,
            .summary-item:hover {
                transform: none !important;
            }
            
            .hero-section,
            .company-showcase,
            .summary-section,
            .footer-section {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }
            
            .hero-title {
                font-size: 28px;
            }
            
            .company-name {
                font-size: 24px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }
        
        /* تأثيرات إضافية */
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="floating-shapes">
                <div class="shape" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="shape" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="shape" style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            </div>
            <h1 class="hero-title">تقرير الحضور والانصراف</h1>
            <p class="hero-subtitle">Attendance & Absence Report - Premium Gradient Design</p>
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
            
            <!-- Attendance Table -->
            <div class="attendance-container">
                <div class="table-header">
                    📋 جدول الحضور والانصراف التفصيلي
                </div>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">م</th>
                            @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                                <th style="width: 80px;">الرقم الوظيفي</th>
                            @endif
                            <th style="width: 100px;">الحالة</th>
                            <th style="width: 250px;">اسم المتدرب</th>
                            <th style="width: 120px;">رقم الهوية</th>
                            @foreach ($days as $day)
                                <th style="width: 30px;" class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}">
                                    {{ $day['name'] }}
                                </th>
                            @endforeach
                            <th style="width: 80px;">إجمالي الغياب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($active_trainees as $index => $trainee)
                            <tr>
                                <td style="font-weight: 700; color: #667eea; font-size: 14px;">{{ $index + 1 }}</td>
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
                                        @php
                                            $attendance = $trainee->trainee->attendances()
                                                ->whereDate('session_starts_at', $day['date'])
                                                ->first();
                                        @endphp
                                        
                                        @if ($day['vacation_day'])
                                            <span class="attendance-badge vacation">ع</span>
                                        @elseif ($attendance)
                                            @if ($attendance->attendance_status === 'present')
                                                <span class="attendance-badge present">ح</span>
                                            @elseif ($attendance->attendance_status === 'absent')
                                                <span class="attendance-badge absent">غ</span>
                                            @elseif ($attendance->attendance_status === 'absent_forgiven')
                                                <span class="attendance-badge vacation">إ</span>
                                            @else
                                                <span class="attendance-badge absent">غ</span>
                                            @endif
                                        @else
                                            <span class="attendance-badge absent">غ</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td style="font-weight: 700; color: #dc3545; font-size: 14px;">
                                    @php
                                        $absentCount = 0;
                                        foreach ($days as $day) {
                                            if (!$day['vacation_day']) {
                                        $attendance = $trainee->trainee->attendances()
                                            ->whereDate('session_starts_at', $day['date'])
                                            ->first();
                                                if (!$attendance || $attendance->status === 'absent') {
                                                    $absentCount++;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $absentCount }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Statistics -->
            <div class="summary-section">
                <h2 class="summary-title">📈 إحصائيات التقرير الشاملة</h2>
                <div class="summary-grid">
                    @php
                        $totalDays = count($days);
                        $workingDays = collect($days)->where('vacation_day', false)->count();
                        $totalTrainees = $active_trainees->count();
                        $totalPresentDays = 0;
                        $totalAbsentDays = 0;
                        
                        foreach ($active_trainees as $trainee) {
                            foreach ($days as $day) {
                                if (!$day['vacation_day']) {
                                        $attendance = $trainee->trainee->attendances()
                                            ->whereDate('session_starts_at', $day['date'])
                                            ->first();
                                    if ($attendance && $attendance->attendance_status === 'present') {
                                        $totalPresentDays++;
                                    } else {
                                        $totalAbsentDays++;
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    <div class="summary-item">
                        <div class="summary-number">{{ $totalTrainees }}</div>
                        <div class="summary-label">إجمالي المتدربين</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $workingDays }}</div>
                        <div class="summary-label">أيام العمل</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $totalPresentDays }}</div>
                        <div class="summary-label">إجمالي الحضور</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $totalAbsentDays }}</div>
                        <div class="summary-label">إجمالي الغياب</div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer-section">
                <p><span class="footer-highlight">تم إنشاء التقرير في:</span> {{ now()->format('Y-m-d H:i:s') }}</p>
                <p><span class="footer-highlight">نوع القالب:</span> التصميم المتدرج المبتكر</p>
                <p>جميع الحقوق محفوظة © {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>