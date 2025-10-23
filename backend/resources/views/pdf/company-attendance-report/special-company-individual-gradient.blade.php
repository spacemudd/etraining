<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور الفردي - التصميم المتدرج</title>
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
            padding: 20px;
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            padding: 0;
            margin: 0 auto;
            max-width: 1200px;
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
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 14px;
            opacity: 0.95;
        }
        
        .content-wrapper {
            padding: 30px;
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
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 14px;
            color: #6c757d;
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
        
        .trainee-profile {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 3px solid #667eea;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .profile-name-en {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .profile-details {
            display: table;
            width: 100%;
            border-spacing: 15px 10px;
        }
        
        .detail-row {
            display: table-row;
        }
        
        .detail-label {
            display: table-cell;
            font-weight: bold;
            color: #667eea;
            width: 40%;
            padding: 10px;
            background: white;
            border-radius: 8px;
        }
        
        .detail-value {
            display: table-cell;
            color: #2c3e50;
            padding: 10px;
            background: white;
            border-radius: 8px;
            font-weight: 600;
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
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
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
            font-size: 18px;
            font-weight: bold;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .attendance-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .day-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .attendance-mark {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-block;
            line-height: 24px;
            font-weight: bold;
            font-size: 12px;
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
            <h1 class="hero-title">تقرير الحضور الفردي</h1>
            <p class="hero-subtitle">Individual Attendance Report - Gradient Design</p>
        </div>
        
        <div class="content-wrapper">
            <!-- Company Showcase -->
            <div class="company-showcase">
                <div class="company-info">
                    <div class="company-name">{{ $record->report->company->name_ar }}</div>
                    <div class="company-subtitle">{{ $record->report->company->name_en }}</div>
                </div>
                @if ($record->report->company->logo_files->count())
                    <div class="logo-container">
                        @php
                            $context = stream_context_create([
                                'ssl' => [
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                ],
                            ]);
                            $logoContent = @file_get_contents('https://prod.jisr-ksa.com/back/media/'.$record->report->company->logo_files->first()->id, false, $context);
                            $base64logo = $logoContent ? 'data:image/jpeg;base64,'.base64_encode($logoContent) : null;
                        @endphp
                        @if ($base64logo)
                            <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Trainee Profile -->
            <div class="trainee-profile">
                <div class="profile-header">
                    <div class="profile-avatar">
                        {{ mb_substr($record->trainee->name_ar, 0, 1) }}
                    </div>
                    <div class="profile-name">{{ $record->trainee->name_ar }}</div>
                    <div class="profile-name-en">{{ $record->trainee->name_en }}</div>
                </div>
                
                <div class="profile-details">
                    <div class="detail-row">
                        <div class="detail-label">🆔 رقم الهوية</div>
                        <div class="detail-value">{{ $record->trainee->national_id }}</div>
                    </div>
                    @if ($record->trainee->job_number)
                        <div class="detail-row">
                            <div class="detail-label">💼 الرقم الوظيفي</div>
                            <div class="detail-value">{{ $record->trainee->job_number }}</div>
                        </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-label">📊 رقم التقرير</div>
                        <div class="detail-value">{{ $record->report->number }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">📅</span>
                    <div class="stat-label">من تاريخ</div>
                    <div class="stat-value">{{ $record->report->date_from->format('Y-m-d') }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📅</span>
                    <div class="stat-label">إلى تاريخ</div>
                    <div class="stat-value">{{ $record->report->date_to->format('Y-m-d') }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">✅</span>
                    <div class="stat-label">أيام الحضور</div>
                    <div class="stat-value">{{ $record->trainee_attendance_records->where('status', 'حضور')->count() }}</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">❌</span>
                    <div class="stat-label">أيام الغياب</div>
                    <div class="stat-value">{{ $record->trainee_attendance_records->where('status', 'غياب')->count() }}</div>
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
                </div>
            </div>
            
            <!-- Attendance Table -->
            <div class="attendance-container">
                <div class="table-header">
                    📋 جدول الحضور التفصيلي
                </div>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>اليوم</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            @if ($with_attendance_times)
                                <th>وقت الحضور</th>
                                <th>وقت الانصراف</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($days as $day)
                            @php
                                $attendance = $record->trainee_attendance_records
                                    ->where('date', $day['date'])
                                    ->first();
                            @endphp
                            <tr>
                                <td class="{{ $day['vacation_day'] ? 'vacation-day' : '' }}" style="font-weight: bold;">
                                    {{ $day['name'] }}
                                </td>
                                <td>{{ $day['date'] }}</td>
                                <td>
                                    @if ($day['vacation_day'])
                                        <span class="attendance-mark mark-vacation">إ</span>
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
                                        <span class="attendance-mark mark-absent">غ</span>
                                    @endif
                                </td>
                                @if ($with_attendance_times)
                                    <td>{{ $attendance ? $attendance->check_in_time : '-' }}</td>
                                    <td>{{ $attendance ? $attendance->check_out_time : '-' }}</td>
                                @endif
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
