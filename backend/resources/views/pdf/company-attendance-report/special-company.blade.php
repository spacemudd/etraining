<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Company attendance report - Special Design</title>
    <link rel="stylesheet" href="{{ public_path('css/special-company-pdf.css') }}">
    <style>
        /* Additional custom styles if needed */
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير الحضور والانصراف</h1>
            <p style="margin: 10px 0 0 0; font-size: 18px;">Attendance & Absence Report</p>
        </div>
        
        <!-- Company Info -->
        <div class="company-info">
            <div>
                <h2 style="margin: 0; color: #333; font-size: 24px;">{{ $report->company->name_ar }}</h2>
                <p style="margin: 5px 0 0 0; color: #666;">{{ $report->company->name_en }}</p>
            </div>
            @if ($base64logo)
                <img src="{{ $base64logo }}" alt="Company Logo" class="logo">
            @endif
        </div>
        
        <!-- Report Details -->
        <div class="report-details">
            <div class="detail-item">
                <div class="detail-label">رقم التقرير</div>
                <div class="detail-value">{{ $report->number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">من تاريخ</div>
                <div class="detail-value">{{ $report->date_from->format('Y-m-d') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">إلى تاريخ</div>
                <div class="detail-value">{{ $report->date_to->format('Y-m-d') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">عدد المتدربين</div>
                <div class="detail-value">{{ $report->activeTraineesCount() }}</div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 50px;">م</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th style="width: 80px;">الرقم الوظيفي</th>
                    @endif
                    <th style="width: 80px;">الحالة</th>
                    <th style="width: 200px;">اسم الموظف</th>
                    <th style="width: 120px;">السجل المدني</th>
                    <th style="width: 100px;">أيام الدوام</th>
                    @foreach ($days as $day)
                        <th class="{{ $day['vacation_day'] ? 'vacation-day' : 'day-header' }}" style="width: 30px;">
                            <div class="vertical-text">
                                {{ $day['name'] }}<br>
                                <small>{{ $day['date'] }}</small>
                            </div>
                        </th>
                    @endforeach
                    <th style="width: 80px;">عدد الغياب</th>
                </tr>
            </thead>
            <tbody>
                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                    @foreach ($active_trainees as $counter => $record)
                        @if(! ($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)) )
                            @if ($record->status === 'temporary_stop')
                                @continue
                            @endif
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td>{{ $record->trainee->job_number }}</td>
                                <td><span class="status-active">فعال</span></td>
                                <td>
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">ID: {{ $record->trainee->id }}</div>
                                </td>
                                <td>{{ $record->trainee->clean_identity_number }}</td>
                                <td>
                                    @if ($record->start_date)
                                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                    @else
                                        {{ count($days) }}
                                    @endif
                                </td>
                                @for($i=0;$i<count($days);$i++)
                                    <td class="{{ $days[$i]['vacation_day'] ? 'vacation-day' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                                {{-- Weekend after resignation date - show empty --}}
                                            @else
                                                <span class="vacation">X</span>
                                            @endif
                                        @else
                                            @if ($record->start_date)
                                                @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                    <span class="present">✓</span>
                                                @else
                                                    @if ($record->status === 'new_registration')
                                                        <span class="absent">✗</span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="present">✓</span>
                                            @endif
                                        @endif
                                    </td>
                                @endfor
                                <td>
                                    @if ($record->start_date)
                                        {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    @foreach ($active_trainees as $counter => $record)
                        @if(! ($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)) )
                            @if ($record->status === 'temporary_stop')
                                @continue
                            @endif
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td><span class="status-active">فعال</span></td>
                                <td>
                                    <div class="employee-name">{{ $record->trainee->name }}</div>
                                    <div class="employee-id">ID: {{ $record->trainee->id }}</div>
                                </td>
                                <td>{{ $record->trainee->clean_identity_number }}</td>
                                <td>
                                    @if ($record->start_date)
                                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                    @else
                                        {{ count($days) }}
                                    @endif
                                </td>
                                @for($i=0;$i<count($days);$i++)
                                    <td class="{{ $days[$i]['vacation_day'] ? 'vacation-day' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                                {{-- Weekend after resignation date - show empty --}}
                                            @else
                                                <span class="vacation">X</span>
                                            @endif
                                        @else
                                            @if ($record->start_date)
                                                @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                    <span class="present">✓</span>
                                                @else
                                                    @if ($record->status === 'new_registration')
                                                        <span class="absent">✗</span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="present">✓</span>
                                            @endif
                                        @endif
                                    </td>
                                @endfor
                                <td>
                                    @if ($record->start_date)
                                        {{ count($days) - $record->start_date->diffInDays($record->end_date) - 1 }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0; color: #666; font-size: 14px;">
                تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}
            </p>
            <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">
                This report was generated on {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html> 