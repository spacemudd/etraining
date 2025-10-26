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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            line-height: 1.6;
            padding: 15px;
        }
        
        .container {
            background: #ffffff;
            max-width: 1200px;
            margin: 0 auto;
            border: 2px solid #2c3e50;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            color: white;
            padding: 35px 20px;
            text-align: center;
            border-bottom: 5px solid #1a252f;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255,255,255,.03) 10px,
                rgba(255,255,255,.03) 20px
            );
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 400;
            letter-spacing: 1.5px;
            position: relative;
            z-index: 1;
        }
        
        /* Company Info Styles */
        .company-info {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
            padding: 30px 25px;
            border-bottom: 3px solid #2c3e50;
            position: relative;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .company-subtitle {
            font-size: 18px;
            color: #5a6c7d;
            text-align: center;
            font-weight: 400;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            display: block;
            margin: -80px auto 15px auto;
            border: 3px solid #2c3e50;
            padding: 8px;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        
        /* Trainee Info Styles */
        .trainee-info {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
            padding: 30px 25px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .trainee-name {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .trainee-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .detail-item {
            background: white;
            border: 2px solid #2c3e50;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(44,62,80,0.1);
        }
        
        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #5a6c7d;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        /* Table Styles */
        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
            background: white;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: white;
            padding: 12px 6px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            border-right: 1px solid rgba(255,255,255,0.1);
            vertical-align: middle;
        }
        
        .attendance-table th:last-child {
            border-right: none;
        }
        
        .attendance-table td {
            padding: 12px 6px;
            text-align: center;
            border: 1px solid #e9ecef;
            font-size: 10px;
            vertical-align: middle;
            background: white;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .attendance-table tbody tr:nth-child(odd) {
            background: white;
        }
        
        /* Attendance Marks */
        .attendance-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 13px;
            border: 2px solid;
        }
        
        .present {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            box-shadow: 0 2px 5px rgba(21,87,36,0.15);
        }
        
        .absent {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            box-shadow: 0 2px 5px rgba(114,28,36,0.15);
        }
        
        .vacation {
            background: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
            box-shadow: 0 2px 5px rgba(133,100,4,0.15);
        }
        
        .day-header {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%) !important;
            color: white !important;
        }
        
        .vacation-day {
            background: linear-gradient(135deg, #5a6c7d 0%, #4a5d6d 100%) !important;
            color: white !important;
        }
        
        /* Print Optimizations */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                border: 2px solid #000;
                box-shadow: none;
                margin: 0;
            }
            
            .header {
                page-break-after: avoid;
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .company-info, .trainee-info {
                page-break-after: avoid;
            }
            
            .attendance-table {
                page-break-inside: auto;
            }
            
            .attendance-table tbody tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور والانصراف الفردي</h1>
            <p>Individual Attendance & Absence Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
            <div class="company-name">{{ $record->company->name_ar }}</div>
            <div class="company-subtitle">{{ $record->company->name_en }}</div>
        </div>
        
        <!-- Trainee Info -->
        <div class="trainee-info">
            <div class="trainee-name">{{ $record->user->first_name_ar }} {{ $record->user->last_name_ar }}</div>
            
            <div class="trainee-details">
                <div class="detail-item">
                    <div class="detail-label">الاسم الكامل</div>
                    <div class="detail-value" style="font-size: 22px;">{{ $record->user->first_name_ar }} {{ $record->user->last_name_ar }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">رقم الهوية</div>
                    <div class="detail-value" style="font-size: 22px;">{{ $record->user->national_id ?? 'غير محدد' }}</div>
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
                    @foreach($days as $day)
                        <th class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="width: 25px;">
                            {{ \Carbon\Carbon::parse($day['date'])->format('d') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($days as $day)
                        @php
                            $attendance = $record->attendances->where('date', $day['date'])->first();
                        @endphp
                        <td>
                            @if($day['vacation_day'])
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