<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور والانصراف الفردي</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #fdfdfd;
            color: #1a1a1a;
            line-height: 1.8;
            padding: 30px;
        }
        
        .container {
            background: white;
            padding: 40px;
            margin: 0 auto;
            max-width: 1400px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px;
            margin: -40px -40px 40px -40px;
            position: relative;
            border-bottom: 5px solid #1a252f;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="80" cy="80" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 12px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        
        .company-info {
            background: #f8f9fa;
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            position: relative;
            page-break-after: avoid;
        }
        
        .company-info::before {
            content: '';
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            height: 4px;
            background: linear-gradient(90deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            border-radius: 8px 8px 0 0;
        }
        
        .company-name {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            position: relative;
            display: block;
        }
        
        .company-name::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #34495e;
        }
        
        .company-subtitle {
            color: #6c757d;
            font-size: 16px;
            font-weight: 400;
            margin-top: 15px;
        }
        
        .trainee-info {
            background: #f8f9fa;
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            page-break-after: avoid;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        }
        
        .trainee-name {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            position: relative;
        }
        
        .trainee-name::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 2px;
            background: #34495e;
        }
        
        .trainee-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 25px;
        }
        
        .detail-item {
            text-align: center;
            padding: 20px 15px;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 1px 8px rgba(0,0,0,0.04);
        }
        
        .detail-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: linear-gradient(90deg, #2c3e50, #34495e);
            border-radius: 0 0 3px 3px;
        }
        
        .detail-label {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 30px;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 18px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            border-bottom: 1px solid #1a252f;
            page-break-after: avoid;
            position: relative;
        }
        
        .attendance-table th:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .attendance-table td {
            padding: 14px 6px;
            text-align: center;
            border-bottom: 1px solid #f1f3f4;
            background: white;
            font-size: 10px;
            vertical-align: middle;
            page-break-inside: avoid;
        }
        
        .attendance-table td:not(:last-child) {
            border-right: 1px solid #f8f9fa;
        }
        
        .attendance-table tbody tr {
            page-break-inside: avoid;
            transition: background-color 0.2s ease;
        }
        
        .attendance-table tbody tr:hover td {
            background: #f8f9fa !important;
        }
        
        .attendance-table tbody tr:nth-child(even) td {
            background: #fbfcfd;
        }
        
        .attendance-table tbody tr:nth-child(odd) td {
            background: white;
        }
        
        .attendance-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .attendance-mark {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            line-height: 20px;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            position: relative;
        }
        
        .present {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            color: #2d5016;
            border: 2px solid #4a7c59;
            box-shadow: 0 2px 4px rgba(74,124,89,0.2);
        }
        
        .absent {
            background: linear-gradient(135deg, #f8e8e8 0%, #f5f0f0 100%);
            color: #8b1538;
            border: 2px solid #c53030;
            box-shadow: 0 2px 4px rgba(197,48,48,0.2);
        }
        
        .vacation {
            background: linear-gradient(135deg, #f0f0f0 0%, #f8f8f8 100%);
            color: #4a5568;
            border: 2px solid #718096;
            box-shadow: 0 2px 4px rgba(113,128,150,0.2);
        }
        
        .day-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            color: white !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #5a6c7d 0%, #6c7b7d 100%) !important;
            color: white !important;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* تحسينات الطباعة */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 15px;
            }
            
            .container {
                box-shadow: none;
                border: 1px solid #ccc;
                padding: 20px;
            }
            
            .header {
                margin: -20px -20px 30px -20px;
                padding: 30px;
            }
            
            .attendance-table {
                font-size: 8px;
            }
            
            .attendance-table th,
            .attendance-table td {
                padding: 8px 4px;
            }
            
            .attendance-mark {
                width: 18px;
                height: 18px;
                line-height: 14px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور والانصراف الفردي</h1>
            <p>{{ $record->course->title }}</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div class="company-name">{{ $record->company->name_ar }}</div>
            <div class="company-subtitle">{{ $record->company->name_en }}</div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo" style="float: left; margin-top: -25px;">
            @endif
        </div>
        
        <!-- Trainee Info -->
        <div class="trainee-info">
            <div class="trainee-name">{{ $record->user->first_name_ar }} {{ $record->user->last_name_ar }}</div>
            
            <div class="trainee-details">
                <div class="detail-item">
                    <div class="detail-label">المتدرب</div>
                    <div class="detail-value">{{ $record->user->first_name_ar }} {{ $record->user->last_name_ar }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">رقم الهوية</div>
                    <div class="detail-value">{{ $record->user->national_id ?? 'غير محدد' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">معدل الحضور</div>
                    <div class="detail-value">{{ number_format($record->attendancePercentage(), 1) }}%</div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    @foreach($dates as $date)
                        @php
                            $dayName = \Carbon\Carbon::parse($date)->translatedFormat('l');
                            $dayNumber = \Carbon\Carbon::parse($date)->format('d');
                            $isVacation = in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [5, 6]);
                        @endphp
                        <th class="{{ $isVacation ? 'vacation-day' : 'day-header' }}">
                            <div style="font-size: 9px;">{{ $dayName }}</div>
                            <div style="font-size: 11px; margin-top: 3px;">{{ $dayNumber }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($dates as $date)
                        @php
                            $attendance = $record->attendances->where('date', $date)->first();
                            $isVacation = in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [5, 6]);
                        @endphp
                        <td>
                            @if($isVacation)
                                <span class="attendance-mark vacation">أ</span>
                            @elseif($attendance)
                                @if($attendance->status == 'present')
                                    <span class="attendance-mark present">ح</span>
                                @elseif($attendance->status == 'absent')
                                    <span class="attendance-mark absent">غ</span>
                                @else
                                    <span class="attendance-mark vacation">أ</span>
                                @endif
                            @else
                                <span class="attendance-mark absent">غ</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>