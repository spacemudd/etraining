<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>تقرير الحضور المضغوط</title>
    <style>
        /* Mini Template - Compact Design with Orange Theme */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #fff5e6 0%, #ffe0b3 100%);
            color: #2c3e50;
            line-height: 1.3;
            padding: 15px;
            font-size: 11px;
        }
        
        .mini-container {
            background: white;
            border: 2px solid #ff8c00;
            border-radius: 12px;
            padding: 20px;
            margin: 0 auto;
            max-width: 1300px;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.2);
        }
        
        /* Compact Header */
        .mini-header {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(255, 140, 0, 0.3);
        }
        
        .mini-header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .mini-header .subtitle {
            font-size: 12px;
            opacity: 0.9;
        }
        
        /* Compact Info Cards */
        .mini-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .mini-card {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ffb74d;
            border-left: 4px solid #ff8c00;
        }
        
        .mini-card h3 {
            font-size: 13px;
            color: #e65100;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .mini-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            font-size: 10px;
        }
        
        .mini-detail {
            background: rgba(255, 140, 0, 0.1);
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px solid rgba(255, 140, 0, 0.2);
        }
        
        .mini-detail strong {
            color: #bf360c;
            display: block;
            margin-bottom: 2px;
        }
        
        /* Ultra Compact Table */
        .mini-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 2px solid #ff8c00;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
            font-size: 8px;
        }
        
        .mini-table th {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
            color: white;
            padding: 6px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #e65100;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
        }
        
        .mini-table td {
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #ffcc80;
            background: white;
            font-size: 7px;
            vertical-align: middle;
        }
        
        .mini-table tr:nth-child(even) {
            background: rgba(255, 224, 179, 0.3);
        }
        
        .mini-table tr:hover {
            background: rgba(255, 140, 0, 0.1);
        }
        
        /* Mini Attendance Marks */
        .mini-mark {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 3px;
            line-height: 14px;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
            border: 1px solid;
        }
        
        .mini-present {
            background: #4caf50;
            color: white;
            border-color: #388e3c;
        }
        
        .mini-absent {
            background: #f44336;
            color: white;
            border-color: #d32f2f;
        }
        
        .mini-vacation {
            background: #ffc107;
            color: #333;
            border-color: #f57f17;
        }
        
        /* Mini Day Headers */
        .mini-day-header {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%) !important;
            color: white !important;
            font-weight: bold;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-size: 7px;
        }
        
        .mini-vacation-day {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
            color: #333 !important;
        }
        
        /* Mini Column Widths */
        .mini-col-index { width: 25px; }
        .mini-col-employee { width: 120px; }
        .mini-col-job { width: 60px; }
        .mini-col-civil { width: 80px; }
        .mini-col-work { width: 40px; }
        .mini-col-day { width: 16px; }
        .mini-col-absence { width: 40px; }
        
        /* Print Optimization */
        @media print {
            body {
                background: white;
                padding: 10px;
            }
            .mini-container {
                box-shadow: none;
                border: 1px solid #ccc;
            }
            .mini-present, .mini-absent, .mini-vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .mini-table tr {
                page-break-inside: avoid;
            }
        }
        
        /* Watermark */
        .mini-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(255, 140, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="mini-watermark">MINI</div>
    
    <div class="mini-container">
        <!-- Mini Header -->
        <div class="mini-header">
            <h1>��� تقرير الحضور والانصراف المضغوط</h1>
            <div class="subtitle">نظام إدارة التدريب الإلكتروني - القالب المصغر</div>
        </div>
        
        <!-- Mini Info Grid -->
        <div class="mini-info-grid">
            <!-- Company Card -->
            <div class="mini-card">
                <h3>��� بيانات الشركة</h3>
                <div class="mini-details">
                    <div class="mini-detail">
                        <strong>اسم الشركة:</strong>
                        {{ $report->company->name }}
                    </div>
                    <div class="mini-detail">
                        <strong>رقم السجل:</strong>
                        {{ $report->company->commercial_registration_number ?? 'غير محدد' }}
                    </div>
                    <div class="mini-detail">
                        <strong>رقم الهاتف:</strong>
                        {{ $report->company->phone ?? 'غير محدد' }}
                    </div>
                    <div class="mini-detail">
                        <strong>المدينة:</strong>
                        {{ $report->company->city->name ?? 'غير محددة' }}
                    </div>
                </div>
            </div>
            
            <!-- Report Card -->
            <div class="mini-card">
                <h3>��� تفاصيل التقرير</h3>
                <div class="mini-details">
                    <div class="mini-detail">
                        <strong>رقم التقرير:</strong>
                        {{ $report->number }}
                    </div>
                    <div class="mini-detail">
                        <strong>من تاريخ:</strong>
                        {{ $report->date_from }}
                    </div>
                    <div class="mini-detail">
                        <strong>إلى تاريخ:</strong>
                        {{ $report->date_to }}
                    </div>
                    <div class="mini-detail">
                        <strong>عدد المتدربين:</strong>
                        {{ $report->activeTraineesCount() }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mini Attendance Table -->
        <table class="mini-table">
            <thead>
                <tr>
                    <th class="mini-col-index">#</th>
                    <th class="mini-col-employee">اسم المتدرب</th>
                    <th class="mini-col-job">رقم وظيفي</th>
                    <th class="mini-col-civil">الهوية</th>
                    <th class="mini-col-work">أيام عمل</th>
                    
                    @foreach($dates as $date)
                        @php
                            $isVacation = in_array($date->format('Y-m-d'), $vacations);
                        @endphp
                        <th class="mini-col-day {{ $isVacation ? 'mini-vacation-day' : 'mini-day-header' }}">
                            {{ $date->format('d') }}
                        </th>
                    @endforeach
                    
                    <th class="mini-col-absence">غياب</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->trainees()->wherePivot('active', 1)->get() as $index => $trainee)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ $trainee->name }}</td>
                        <td>{{ $trainee->job_number ?? '-' }}</td>
                        <td>{{ $trainee->civil_number }}</td>
                        <td>{{ $trainee->working_days_count ?? 0 }}</td>
                        
                        @foreach($dates as $date)
                            @php
                                $attendance = $traineeAttendances[$trainee->id][$date->format('Y-m-d')] ?? null;
                                $isVacation = in_array($date->format('Y-m-d'), $vacations);
                            @endphp
                            <td class="{{ $isVacation ? 'mini-vacation-day' : '' }}">
                                @if($isVacation)
                                    <span class="mini-mark mini-vacation">ع</span>
                                @elseif($attendance && $attendance->attended_at)
                                    <span class="mini-mark mini-present">✓</span>
                                @else
                                    <span class="mini-mark mini-absent">✗</span>
                                @endif
                            </td>
                        @endforeach
                        
                        <td style="font-weight: bold; color: #d32f2f;">
                            {{ $traineeAbsenceCounts[$trainee->id] ?? 0 }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Mini Footer -->
        <div style="margin-top: 15px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ffcc80; padding-top: 10px;">
            <strong>��� تم إنشاء التقرير في:</strong> {{ now()->format('Y/m/d H:i') }} | 
            <strong>��� القالب:</strong> المصغر (Mini) |
            <strong>��� النسخة:</strong> 1.0
        </div>
    </div>
</body>
</html>
