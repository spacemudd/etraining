<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>التقرير الفردي المضغوط</title>
    <style>
        /* Mini Individual Template - Compact Design with Orange Theme */
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
            font-size: 12px;
        }
        
        .mini-container {
            background: white;
            border: 2px solid #ff8c00;
            border-radius: 12px;
            padding: 20px;
            margin: 0 auto;
            max-width: 1200px;
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
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .mini-header .subtitle {
            font-size: 13px;
            opacity: 0.9;
        }
        
        /* Employee Spotlight Card */
        .mini-employee-spotlight {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 2px solid #ffb74d;
            border-left: 6px solid #ff8c00;
            box-shadow: 0 3px 10px rgba(255, 140, 0, 0.15);
        }
        
        .mini-employee-name {
            font-size: 24px;
            font-weight: bold;
            color: #bf360c;
            margin-bottom: 15px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(191, 54, 12, 0.1);
        }
        
        .mini-employee-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .mini-employee-detail {
            background: rgba(255, 140, 0, 0.1);
            padding: 10px;
            border-radius: 6px;
            border: 1px solid rgba(255, 140, 0, 0.3);
            text-align: center;
        }
        
        .mini-employee-detail strong {
            display: block;
            color: #e65100;
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .mini-employee-detail span {
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }
        
        /* Company Info Compact */
        .mini-company-info {
            background: rgba(255, 224, 179, 0.5);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #ffcc80;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mini-company-name {
            font-size: 16px;
            font-weight: bold;
            color: #bf360c;
        }
        
        .mini-report-period {
            font-size: 12px;
            color: #e65100;
            background: rgba(255, 140, 0, 0.2);
            padding: 5px 10px;
            border-radius: 15px;
        }
        
        /* Ultra Compact Individual Table */
        .mini-individual-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 2px solid #ff8c00;
            border-radius: 8px;
            overflow: hidden;
            font-size: 10px;
        }
        
        .mini-individual-table th {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #e65100;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
        }
        
        .mini-individual-table td {
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #ffcc80;
            background: white;
            font-size: 9px;
            vertical-align: middle;
        }
        
        .mini-individual-table tr:nth-child(even) {
            background: rgba(255, 224, 179, 0.3);
        }
        
        /* Mini Attendance Marks for Individual */
        .mini-individual-mark {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            line-height: 20px;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            border: 1px solid;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .mini-individual-present {
            background: #4caf50;
            color: white;
            border-color: #388e3c;
        }
        
        .mini-individual-absent {
            background: #f44336;
            color: white;
            border-color: #d32f2f;
        }
        
        .mini-individual-vacation {
            background: #ffc107;
            color: #333;
            border-color: #f57f17;
        }
        
        /* Day Headers for Individual */
        .mini-individual-day-header {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%) !important;
            color: white !important;
            font-weight: bold;
        }
        
        .mini-individual-vacation-day {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
            color: #333 !important;
        }
        
        /* Summary Stats */
        .mini-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .mini-stat-card {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #ffb74d;
            box-shadow: 0 2px 5px rgba(255, 140, 0, 0.1);
        }
        
        .mini-stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #bf360c;
            display: block;
        }
        
        .mini-stat-label {
            font-size: 10px;
            color: #e65100;
            margin-top: 5px;
        }
        
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
            .mini-individual-present, .mini-individual-absent, .mini-individual-vacation {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
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
            <h1>��� التقرير الفردي المضغوط</h1>
            <div class="subtitle">نظام إدارة التدريب الإلكتروني - القالب المصغر الفردي</div>
        </div>
        
        <!-- Employee Spotlight -->
        <div class="mini-employee-spotlight">
            <div class="mini-employee-name">��� {{ $trainee->name }}</div>
            
            <div class="mini-employee-grid">
                <div class="mini-employee-detail">
                    <strong>رقم الهوية</strong>
                    <span>{{ $trainee->civil_number }}</span>
                </div>
                <div class="mini-employee-detail">
                    <strong>الرقم الوظيفي</strong>
                    <span>{{ $trainee->job_number ?? 'غير محدد' }}</span>
                </div>
                <div class="mini-employee-detail">
                    <strong>رقم الهاتف</strong>
                    <span>{{ $trainee->phone ?? 'غير محدد' }}</span>
                </div>
                <div class="mini-employee-detail">
                    <strong>البريد الإلكتروني</strong>
                    <span style="font-size: 10px;">{{ $trainee->email ?? 'غير محدد' }}</span>
                </div>
                <div class="mini-employee-detail">
                    <strong>المدينة</strong>
                    <span>{{ $trainee->city->name ?? 'غير محددة' }}</span>
                </div>
                <div class="mini-employee-detail">
                    <strong>أيام العمل</strong>
                    <span>{{ $trainee->working_days_count ?? 0 }}</span>
                </div>
            </div>
        </div>
        
        <!-- Company Info -->
        <div class="mini-company-info">
            <div class="mini-company-name">��� {{ $report->company->name }}</div>
            <div class="mini-report-period">��� {{ $report->date_from }} إلى {{ $report->date_to }}</div>
        </div>
        
        <!-- Individual Attendance Table -->
        <table class="mini-individual-table">
            <thead>
                <tr>
                    <th style="width: 80px;">التاريخ</th>
                    <th style="width: 60px;">اليوم</th>
                    <th style="width: 80px;">الحضور</th>
                    <th style="width: 80px;">الانصراف</th>
                    <th style="width: 60px;">الحالة</th>
                    <th style="width: 100px;">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dates as $date)
                    @php
                        $attendance = $traineeAttendances[$trainee->id][$date->format('Y-m-d')] ?? null;
                        $isVacation = in_array($date->format('Y-m-d'), $vacations);
                        $dayName = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'][$date->format('w')];
                    @endphp
                    <tr class="{{ $isVacation ? 'mini-individual-vacation-day' : '' }}">
                        <td style="font-weight: bold;">{{ $date->format('Y/m/d') }}</td>
                        <td>{{ $dayName }}</td>
                        <td>
                            @if($attendance && $attendance->attended_at)
                                {{ \Carbon\Carbon::parse($attendance->attended_at)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($attendance && $attendance->left_at)
                                {{ \Carbon\Carbon::parse($attendance->left_at)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($isVacation)
                                <span class="mini-individual-mark mini-individual-vacation">ع</span>
                            @elseif($attendance && $attendance->attended_at)
                                <span class="mini-individual-mark mini-individual-present">✓</span>
                            @else
                                <span class="mini-individual-mark mini-individual-absent">✗</span>
                            @endif
                        </td>
                        <td style="font-size: 8px;">
                            @if($isVacation)
                                عطلة رسمية
                            @elseif($attendance && $attendance->attended_at)
                                حضور
                            @else
                                غياب
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Mini Statistics -->
        <div class="mini-stats-grid">
            <div class="mini-stat-card">
                <span class="mini-stat-number">{{ $attendanceDays }}</span>
                <div class="mini-stat-label">أيام الحضور</div>
            </div>
            <div class="mini-stat-card">
                <span class="mini-stat-number">{{ $absenceDays }}</span>
                <div class="mini-stat-label">أيام الغياب</div>
            </div>
            <div class="mini-stat-card">
                <span class="mini-stat-number">{{ count($vacations) }}</span>
                <div class="mini-stat-label">أيام العطل</div>
            </div>
            <div class="mini-stat-card">
                <span class="mini-stat-number">{{ number_format($attendancePercentage, 1) }}%</span>
                <div class="mini-stat-label">نسبة الحضور</div>
            </div>
        </div>
        
        <!-- Mini Footer -->
        <div style="margin-top: 15px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ffcc80; padding-top: 10px;">
            <strong>��� تم إنشاء التقرير في:</strong> {{ now()->format('Y/m/d H:i') }} | 
            <strong>��� القالب:</strong> المصغر الفردي (Mini Individual) |
            <strong>��� النسخة:</strong> 1.0
        </div>
    </div>
</body>
</html>
