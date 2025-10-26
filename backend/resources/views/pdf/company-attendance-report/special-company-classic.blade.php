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
        
        /* Report Details Cards */
        .report-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            padding: 25px 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #2c3e50;
        }
        
        .detail-card {
            background: #ffffff;
            border: 2px solid #2c3e50;
            border-radius: 8px;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(44,62,80,0.1);
            transition: all 0.3s ease;
        }
        
        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: #5a6c7d;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 26px;
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
            font-size: 10px;
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
        
        .employee-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 11px;
            margin-bottom: 3px;
            line-height: 1.4;
        }
        
        .employee-id {
            color: #6c757d;
            font-size: 9px;
            font-weight: 400;
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
            
            .company-info {
                page-break-after: avoid;
            }
            
            .report-details {
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
            <h1>تقرير الحضور والانصراف</h1>
            <p>Attendance & Absence Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
            <div class="company-name">{{ $report->company->name_ar }}</div>
            <div class="company-subtitle">{{ $report->company->name_en }}</div>
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-card">
                <div class="detail-label">رقم التقرير</div>
                <div class="detail-value">{{ str_replace('ATR-', '', $report->number) }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">فترة التقرير</div>
                <div class="detail-value">{{ $report->date_from->format('m/d') }} - {{ $report->date_to->format('m/d') }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">عدد الموظفين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">أيام العمل</div>
                <div class="detail-value">{{ count($days) }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 35px;">م</th>
                    <th style="width: 180px;">اسم الموظف</th>
                    <th style="width: 120px;">الهوية المدنية</th>
                    <th style="width: 80px;">أيام العمل</th>
                    <th style="width: 60px;">غياب</th>
                    @foreach(($days ?? []) as $day)
                        <th class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="width: 25px;">
                            {{ \Carbon\Carbon::parse($day['date'])->format('d') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($report->activeTrainees as $index => $trainee)
                    @php
                        $dates_array = collect($days)->pluck('date')->toArray();
                        $attendances = $trainee->attendances->whereIn('date', $dates_array);
                        $presentCount = $attendances->where('status', 'present')->count();
                        $absentCount = $attendances->where('status', 'absent')->count();
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #2c3e50;">{{ $index + 1 }}</td>
                        <td>
                            <div class="employee-name">{{ $trainee->first_name_ar }} {{ $trainee->last_name_ar }}</div>
                            <div class="employee-id">{{ $trainee->national_id ?? 'غير محدد' }}</div>
                        </td>
                        <td style="font-weight: 600; color: #5a6c7d;">{{ $trainee->national_id ?? 'غير محدد' }}</td>
                        <td style="font-weight: 700; color: #28a745;">{{ $presentCount }}</td>
                        <td style="font-weight: 700; color: #dc3545;">{{ $absentCount }}</td>
                        @foreach(($days ?? []) as $day)
                            @php
                                $attendance = $attendances->where('date', $day['date'])->first();
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
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>