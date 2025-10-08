<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور الفردي - التصميم المتدرج المبتكر</title>
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
            max-width: 1200px;
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
            padding: 35px 25px;
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
            font-size: 36px;
            font-weight: 900;
            margin-bottom: 12px;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
            background: linear-gradient(45deg, #fff, #f8f9fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 18px;
            font-weight: 400;
            opacity: 0.95;
            position: relative;
            z-index: 2;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        
        .content-wrapper {
            padding: 25px;
        }
        
        .company-showcase {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 25px;
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
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .company-subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .logo-container {
            position: relative;
            z-index: 2;
        }
        
        .logo {
            max-width: 100px;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border: none;
            padding: 20px;
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
            font-size: 28px;
            margin-bottom: 12px;
            display: block;
        }
        
        .stat-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 13px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .stat-value {
            font-size: 24px;
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
            margin-bottom: 25px;
        }
        
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px;
            text-align: center;
            font-size: 22px;
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
            padding: 15px 8px;
            text-align: center;
            font-weight: 700;
            font-size: 11px;
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
            padding: 12px 6px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: white;
            font-size: 10px;
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
            font-size: 12px;
            margin-bottom: 3px;
            line-height: 1.3;
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 9px;
            line-height: 1.2;
        }
        
        .attendance-badge {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            line-height: 24px;
            font-weight: 700;
            font-size: 10px;
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
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10px;
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
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 25px;
            box-shadow: 0 15px 30px rgba(240, 147, 251, 0.3);
        }
        
        .summary-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 18px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 18px;
        }
        
        .summary-item {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 18px;
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
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 6px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .summary-label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }
        
        .footer-section {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(44, 62, 80, 0.3);
        }
        
        .footer-section p {
            margin: 6px 0;
            font-size: 13px;
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
                font-size: 24px;
            }
            
            .company-name {
                font-size: 20px;
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
                <div class="shape" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="shape" style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="shape" style="width: 65px; height: 65px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            </div>
            <h1 class="hero-title">تقرير الحضور الفردي</h1>
            <p class="hero-subtitle">Individual Attendance Report - Premium Gradient Design</p>
        </div>
        
        <div class="content-wrapper">
            <!-- Company Showcase -->
            <div class="company-showcase">
                <div class="company-info">
                    <div class="company-name">{{ $record->company->name_ar }}</div>
                    <div class="company-subtitle">{{ $record->company->name_en }}</div>
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
                    <div class="stat-value">{{ $record->report->number }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">👤</span>
                    <div class="stat-label">اسم المتدرب</div>
                    <div class="stat-value">{{ $record->trainee->name_ar }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">🆔</span>
                    <div class="stat-label">رقم الهوية</div>
                    <div class="stat-value">{{ $record->trainee->national_id }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📅</span>
                    <div class="stat-label">الفترة</div>
                    <div class="stat-value">{{ $record->report->date_from->format('Y-m-d') }} - {{ $record->report->date_to->format('Y-m-d') }}</div>
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
                            <th style="width: 100px;">التاريخ</th>
                            <th style="width: 100px;">اليوم</th>
                            <th style="width: 100px;">الحالة</th>
                            <th style="width: 150px;">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($days as $index => $day)
                            <tr>
                                <td style="font-weight: 700; color: #667eea; font-size: 12px;">{{ $index + 1 }}</td>
                                <td style="font-weight: 600;">{{ $day['date']->format('Y-m-d') }}</td>
                                <td style="font-weight: 600;">{{ $day['name'] }}</td>
                                <td>
                                    @if ($day['vacation_day'])
                                        <span class="attendance-badge vacation">عطلة</span>
                                    @else
                                        <span class="attendance-badge present">حضور</span>
                                    @endif
                                </td>
                                <td style="font-size: 10px;">
                                    -
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
                        $vacationDays = collect($days)->where('vacation_day', true)->count();
                        $presentDays = $workingDays;
                        $absentDays = 0;
                    @endphp
                    
                    <div class="summary-item">
                        <div class="summary-number">{{ $totalDays }}</div>
                        <div class="summary-label">إجمالي الأيام</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $workingDays }}</div>
                        <div class="summary-label">أيام العمل</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $presentDays }}</div>
                        <div class="summary-label">أيام الحضور</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $absentDays }}</div>
                        <div class="summary-label">أيام الغياب</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $vacationDays }}</div>
                        <div class="summary-label">أيام الإجازة</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number">{{ $workingDays > 0 ? round(($presentDays / $workingDays) * 100, 1) : 0 }}%</div>
                        <div class="summary-label">نسبة الحضور</div>
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