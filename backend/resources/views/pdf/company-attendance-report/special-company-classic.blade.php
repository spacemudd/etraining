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
            background: #f5f7fa;
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }
        
        .report-wrapper {
            background: #ffffff;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }
        
        /* Top Header Section */
        .top-header {
            background: #2c3e50;
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .report-subtitle {
            font-size: 14px;
            opacity: 0.85;
            margin-top: 5px;
        }
        
        .logo-section {
            text-align: left;
        }
        
        .logo-img {
            max-width: 120px;
            height: auto;
            border: 2px solid rgba(255,255,255,0.2);
            padding: 5px;
            background: white;
        }
        
        /* Main Header */
        .main-header {
            background: linear-gradient(to right, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 3px solid #1a252f;
        }
        
        .header-left {
            flex: 1;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .company-english {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .header-right {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            min-width: 300px;
        }
        
        .info-box {
            background: rgba(255,255,255,0.1);
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .info-label {
            font-size: 10px;
            opacity: 0.8;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 700;
        }
        
        /* Stats Cards Section */
        .stats-section {
            background: #f8f9fa;
            padding: 25px 30px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .stat-card {
            background: white;
            padding: 20px 15px;
            text-align: center;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .stat-card.top-border-blue {
            border-top: 4px solid #3498db;
        }
        
        .stat-card.top-border-green {
            border-top: 4px solid #2ecc71;
        }
        
        .stat-card.top-border-orange {
            border-top: 4px solid #f39c12;
        }
        
        .stat-card.top-border-red {
            border-top: 4px solid #e74c3c;
        }
        
        .stat-label {
            font-size: 11px;
            color: #7f8c8d;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        /* Table Container */
        .table-container {
            padding: 25px;
            background: white;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11px;
        }
        
        .attendance-table thead th {
            background: linear-gradient(to bottom, #34495e, #2c3e50);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            border-right: 1px solid rgba(255,255,255,0.15);
        }
        
        .attendance-table thead th:last-child {
            border-right: none;
        }
        
        .attendance-table thead th.col-number {
            width: 40px;
            background: linear-gradient(to bottom, #1a252f, #2c3e50);
        }
        
        .attendance-table thead th.col-name {
            width: 200px;
            text-align: right;
            padding-right: 15px;
        }
        
        .attendance-table thead th.col-id {
            width: 130px;
        }
        
        .attendance-table thead th.col-work-days {
            width: 80px;
        }
        
        .attendance-table thead th.col-absent {
            width: 60px;
        }
        
        .attendance-table thead th.col-date {
            width: 28px;
            font-size: 9px;
            padding: 8px 4px;
        }
        
        .attendance-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .attendance-table tbody tr:hover {
            background: #eef2f5 !important;
        }
        
        .attendance-table tbody td {
            padding: 10px 8px;
            border-right: 1px solid #ecf0f1;
            text-align: center;
        }
        
        .attendance-table tbody td.col-number {
            font-weight: 700;
            color: #34495e;
            font-size: 12px;
        }
        
        .attendance-table tbody td.col-name {
            text-align: right;
            padding-right: 15px;
        }
        
        .employee-name-row {
            font-weight: 700;
            color: #2c3e50;
            font-size: 12px;
        }
        
        .employee-id-row {
            color: #7f8c8d;
            font-size: 10px;
            margin-top: 3px;
        }
        
        .attendance-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 11px;
        }
        
        .marker-present {
            background: #d5f4e6;
            color: #27ae60;
            border: 2px solid #2ecc71;
        }
        
        .marker-absent {
            background: #fadbd8;
            color: #c0392b;
            border: 2px solid #e74c3c;
        }
        
        .marker-vacation {
            background: #fef9e7;
            color: #d68910;
            border: 2px solid #f39c12;
        }
        
        .day-header-normal {
            background: linear-gradient(to bottom, #34495e, #2c3e50);
        }
        
        .day-header-vacation {
            background: linear-gradient(to bottom, #5d6d7e, #4a5d6d);
        }
        
        /* Summary Footer */
        .summary-footer {
            background: #ecf0f1;
            padding: 20px 30px;
            border-top: 2px solid #bdc3c7;
            text-align: center;
            color: #34495e;
            font-size: 12px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .report-wrapper {
                box-shadow: none;
            }
            
            .attendance-table tbody tr:hover {
                background: inherit !important;
            }
        }
    </style>
</head>
<body>
    <div class="report-wrapper">
        <!-- Top Header -->
        <div class="top-header">
            <div>
                <div class="report-title">تقرير الحضور والانصراف</div>
                <div class="report-subtitle">Attendance & Absence Report</div>
            </div>
            @if ($base64logo)
                <div class="logo-section">
                    <img src="{{ $base64logo }}" alt="Logo" class="logo-img">
                </div>
            @endif
        </div>
        
        <!-- Main Header -->
        <div class="main-header">
            <div class="header-left">
                <div class="company-name">{{ $report->company->name_ar }}</div>
                <div class="company-english">{{ $report->company->name_en }}</div>
            </div>
            <div class="header-right">
                <div class="info-box">
                    <div class="info-label">رقم التقرير</div>
                    <div class="info-value">{{ str_replace('ATR-', '', $report->number) }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">فترة التقرير</div>
                    <div class="info-value">{{ $report->date_from->format('m/d') }} - {{ $report->date_to->format('m/d') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-card top-border-blue">
                <div class="stat-label">عدد الموظفين</div>
                <div class="stat-value">{{ $report->activeTraineesCount() }}</div>
            </div>
            <div class="stat-card top-border-green">
                <div class="stat-label">أيام العمل</div>
                <div class="stat-value">{{ count($days ?? []) }}</div>
            </div>
            <div class="stat-card top-border-orange">
                <div class="stat-label">إجمالي الحضور</div>
                <div class="stat-value">{{ $report->totalPresentCount() }}</div>
            </div>
            <div class="stat-card top-border-red">
                <div class="stat-label">إجمالي الغياب</div>
                <div class="stat-value">{{ $report->totalAbsentCount() }}</div>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="table-container">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="col-number">م</th>
                        <th class="col-name">اسم الموظف</th>
                        <th class="col-id">الهوية المدنية</th>
                        <th class="col-work-days">أيام العمل</th>
                        <th class="col-absent">غياب</th>
                        @foreach(($days ?? []) as $day)
                            <th class="col-date {{ $day['vacation_day'] ? 'day-header-vacation' : 'day-header-normal' }}">
                                {{ \Carbon\Carbon::parse($day['date'])->format('d') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if(isset($active_trainees))
                        @foreach($active_trainees as $index => $record)
                            @php
                                $trainee = $record->trainee ?? $record;
                                $dates_array = collect($days ?? [])->pluck('date')->toArray();
                                $attendances = $trainee->attendances->whereIn('date', $dates_array) ?? collect([]);
                                $presentCount = $attendances->where('status', 'present')->count();
                                $absentCount = $attendances->where('status', 'absent')->count();
                            @endphp
                            <tr>
                                <td class="col-number">{{ $index + 1 }}</td>
                                <td class="col-name">
                                    <div class="employee-name-row">{{ $trainee->first_name_ar }} {{ $trainee->last_name_ar }}</div>
                                    <div class="employee-id-row">ID: {{ $trainee->national_id ?? 'غير محدد' }}</div>
                                </td>
                                <td>{{ $trainee->national_id ?? 'غير محدد' }}</td>
                                <td style="font-weight: 700; color: #27ae60;">{{ $presentCount }}</td>
                                <td style="font-weight: 700; color: #e74c3c;">{{ $absentCount }}</td>
                                @foreach(($days ?? []) as $day)
                                    @php
                                        $attendance = $attendances->where('date', $day['date'])->first();
                                    @endphp
                                    <td>
                                        @if($day['vacation_day'])
                                            <span class="attendance-marker marker-vacation">أ</span>
                                        @elseif($attendance)
                                            @if($attendance->status == 'present')
                                                <span class="attendance-marker marker-present">ح</span>
                                            @elseif($attendance->status == 'absent')
                                                <span class="attendance-marker marker-absent">غ</span>
                                            @else
                                                <span class="attendance-marker marker-vacation">أ</span>
                                            @endif
                                        @else
                                            <span class="attendance-marker marker-absent">غ</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Summary Footer -->
        <div class="summary-footer">
            <strong>تم إنشاء هذا التقرير تلقائياً بواسطة نظام إدارة التدريب</strong>
        </div>
    </div>
</body>
</html>