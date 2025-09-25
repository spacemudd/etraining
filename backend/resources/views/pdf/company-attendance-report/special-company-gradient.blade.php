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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(30deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(30deg); }
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        .company-info {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
        }
        
        .company-name {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .company-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            border-radius: 10px;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .report-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border: none;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .detail-card:hover {
            transform: translateY(-5px);
        }
        
        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .detail-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            table-layout: fixed;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: none;
            position: relative;
        }
        
        .attendance-table th:first-child {
            border-top-left-radius: 15px;
        }
        
        .attendance-table th:last-child {
            border-top-right-radius: 15px;
        }
        
        .attendance-table td {
            padding: 12px 6px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: white;
            font-size: 10px;
            vertical-align: middle;
            transition: background-color 0.3s ease;
        }
        
        .attendance-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .attendance-table tr:hover td {
            background: #e3f2fd;
        }
        
        .employee-name {
            font-weight: bold;
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
        
        .attendance-mark {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            line-height: 22px;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .attendance-mark:hover {
            transform: scale(1.1);
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
            font-weight: bold;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            color: white !important;
        }
        
        .status-active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            border-radius: 15px;
            border-top: 4px solid #667eea;
        }
        
        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .container {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 20px !important;
            }
            
            .attendance-table {
                box-shadow: none !important;
            }
            
            .detail-card:hover,
            .attendance-mark:hover {
                transform: none !important;
            }
        }
        
        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 10px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .company-name {
                font-size: 20px;
            }
            
            .report-details {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور والانصراف</h1>
            <p>Attendance & Absence Report - Gradient Design</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div>
                <div class="company-name">{{ $report->company->name_ar }}</div>
                <div class="company-subtitle">{{ $report->company->name_en }}</div>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">رقم التقرير</div>
                <div class="detail-value">{{ $report->number }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">من تاريخ</div>
                <div class="detail-value">{{ $report->date_from->format('Y-m-d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">إلى تاريخ</div>
                <div class="detail-value">{{ $report->date_to->format('Y-m-d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">عدد المتدربين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 40px;">م</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th style="width: 60px;">الرقم الوظيفي</th>
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
                            <td>{{ $trainee->trainee->job_number ?? '-' }}</td>
                        @endif
                        <td>
                            @if ($trainee->active)
                                <span class="status-active">نشط</span>
                            @else
                                <span style="background: #6c757d; color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px;">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <div class="employee-name">{{ $trainee->trainee->name_ar }}</div>
                            <div class="employee-id">{{ $trainee->trainee->name_en }}</div>
                        </td>
                        <td>{{ $trainee->trainee->national_id }}</td>
                        @foreach ($days as $day)
                            <td>
                                @php
                                    $attendance = $trainee->trainee->attendances()
                                        ->where('date', $day['date'])
                                        ->first();
                                @endphp
                                
                                @if ($day['vacation_day'])
                                    <span class="attendance-mark vacation">ع</span>
                                @elseif ($attendance)
                                    @if ($attendance->status === 'present')
                                        <span class="attendance-mark present">ح</span>
                                    @elseif ($attendance->status === 'absent')
                                        <span class="attendance-mark absent">غ</span>
                                    @elseif ($attendance->status === 'vacation')
                                        <span class="attendance-mark vacation">إ</span>
                                    @endif
                                @else
                                    <span class="attendance-mark absent">غ</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="font-weight: bold; color: #dc3545;">
                            @php
                                $absentCount = 0;
                                foreach ($days as $day) {
                                    if (!$day['vacation_day']) {
                                        $attendance = $trainee->trainee->attendances()
                                            ->where('date', $day['date'])
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
        
        <!-- Summary Statistics -->
        <div class="summary-stats">
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
                                ->where('date', $day['date'])
                                ->first();
                            if ($attendance && $attendance->status === 'present') {
                                $totalPresentDays++;
                            } else {
                                $totalAbsentDays++;
                            }
                        }
                    }
                }
            @endphp
            
            <div class="stat-item">
                <div class="stat-number">{{ $totalTrainees }}</div>
                <div class="stat-label">إجمالي المتدربين</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $workingDays }}</div>
                <div class="stat-label">أيام العمل</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $totalPresentDays }}</div>
                <div class="stat-label">إجمالي الحضور</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $totalAbsentDays }}</div>
                <div class="stat-label">إجمالي الغياب</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>تم إنشاء التقرير في:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            <p><strong>نوع القالب:</strong> التصميم المتدرج الجديد</p>
            <p>جميع الحقوق محفوظة © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
