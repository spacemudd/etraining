<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    {{--        <link rel="stylesheet" href="{{ url('pdf.css') }}" media="screen">--}}
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}">
    <title>Company attendance report</title>
    <style>
        table { 
            page-break-after: auto;
            table-layout: fixed;
            width: 100%;
        }
        tr { 
            page-break-inside: avoid; 
            page-break-after: auto;
        }
        td { 
            page-break-inside: avoid; 
            page-break-after: auto;
        }
        th {
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        thead { 
            display: table-header-group;
            page-break-after: avoid;
        }
        tfoot { 
            display: table-footer-group;
        }
        tbody {
            page-break-inside: auto;
        }
        tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .vertical-text {
            writing-mode: vertical-rl;
            -webkit-transform: rotate(90deg);
            -webkit-transform-origin: center bottom auto;
        }
        
        /* إصلاح مشكلة الصفحة الثانية */
        .second-table {
            margin-top: 0;
            border-top: none;
        }
        
        .second-table thead {
            display: none;
        }
        
        /* تحسينات للطباعة */
        @media print {
            table {
                table-layout: fixed;
                border-collapse: collapse;
                width: 100%;
            }
            
            thead {
                display: table-header-group;
                page-break-after: avoid;
            }
            
            tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            td, th {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .second-table thead {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    @if ($report->with_logo && !$base64logo)
        <div class="row">
            <div class="col-2">
                <table class="table" style="width:100%;">
                    <tbody>
                    <tr>
                        <td style="background:#e0e0e0;">رقم التقرير</td>
                        <td>{{ $report->number }}</td>
                    </tr>
                    <tr>
                        <td style="background:#e0e0e0;">من</td>
                        <td>{{ $report->date_from->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td style="background:#e0e0e0;">الى</td>
                        <td>{{ $report->date_to->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td style="background:#e0e0e0;">العدد</td>
                        <td>{{ $report->activeTraineesCount() }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-8">
                <h1 style="text-align: center;">تقرير الحضور للمتدربات</h1>
            </div>
            <div class="col-2" style="text-align:right;">
                <img src="{{ public_path('/img/logo.png')}}" alt="logo" width="200"/>
            </div>
        </div>
    @endif

    @if ($base64logo)
        <div class="row" style="text-align: center;">
            <img style="margin:0 auto;border:none;" src="{{ $base64logo }}" alt="logo" width="200"/>
        </div>
    @endif

    <div class="row" style="margin-top: 10px;">
        <table class="table" style="width:100%;">
            <colgroup>
                <col style="width:35px">
                <col style="width:70px">
                <col style="width:50px">
                <col style="width:305px">
                <col style="width:100px">
                <col style="width:110px">
            </colgroup>
            <thead>
            <tr style="height:60px;">
                <th colspan="38" style="text-align: center;padding: 10px;font-size: 38px;">
                    {{ $report->company->name_ar }}
                </th>
            </tr>
            <tr style="height:100px;background:#e0e0e0;">
                <th dir="ltr" class="vertical-text" style="white-space: nowrap">SI #</th>
                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                    <th dir="ltr" class="vertical-text">Emp. #</th>
                @endif
                <th class="vertical-text">Status</th>
                <th class="vertical-text">Employee<br/> name</th>
                <th class="vertical-text">ID</th>
                <th colspan="1"></th>
                @foreach ($days as $day)
                    <th style="width:20px;{{ $day['vacation_day'] ? 'background:#e0e0e0;' : 'background: white;' }}">
                        <div class="vertical-text" style="position:absolute;white-space:nowrap;height:35px;">{{ $day['name'] }}</div>
                    </th>
                @endforeach
                <th rowspan="2" style="width:20px;">
                    <div class="vertical-text" style="position:absolute;white-space:nowrap;height:55px;">عدد الغياب</div>
                </th>
            </tr>
            <tr style="height:120px;background:#e0e0e0;">
                <th class="vertical-text">م</th>
                @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                    <th class="vertical-text" style="white-space: nowrap">الرقم الوظيفي</th>
                @endif
                <th class="vertical-text">الحالة</th>
                <th class="vertical-text" style="width:500px;">اسم الموظف</th>
                <th class="vertical-text">السجل المدني</th>
                <th class="vertical-text" style="white-space: nowrap">
                    <span>عدد ايام الدوام حسب</span>
                    <br/>
                    <span>الالتحاق بالتأمينات</span>
                </th>
                @foreach ($days as $day)
                    <th style="width:20px;{{ $day['vacation_day'] ? 'background:#e0e0e0;' : 'background: white;' }}">
                        <div class="vertical-text" style="position:absolute;white-space:nowrap;height:35px;width:30px;">{{ $day['date'] }}</div>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody style="page-break-inside: avoid;">
            @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                @foreach ($active_trainees as $counter => $record)
                    @if(! ($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)) )
                        @if ($record->status === 'temporary_stop')
                            @continue
                        @endif
                        <tr>
                            <td>{{ ++$counter }}</td>
                            <td>{{ $record->trainee->job_number }}</td>
                            <td>فعال</td>
                            <td>{{ $record->trainee->name }}</td>
                            <td>{{ $record->trainee->clean_identity_number }}</td>
                            <td style="text-align: center;">
                                @if ($record->start_date)
                                    {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                @else
                                    {{ count($days) }}
                                @endif
                                @if ($report->with_attendance_times)
                                    <span style="font-size:12px;">
                                            <br/>
                                            دخول:
                                            <br/>
                                            خروج:
                                            </span>
                                @endif
                            </td>
                            @for($i=0;$i<count($days);$i++)
                                <td style="{{ $days[$i]['vacation_day'] ? 'background:#e0e0e0;' : '' }}">
                                    @if ($days[$i]['vacation_day'])
                                        @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                            {{-- Weekend after resignation date - show empty --}}
                                        @else
                                            X
                                        @endif
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                &#10003;
                                                @if ($report->with_attendance_times)
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(1,10))}}</span>
                                                @endif
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    {{-- Considered absent --}}
                                                    &#120;
                                                @endif
                                            @endif
                                        @else
                                            &#10003;
                                            @if ($report->with_attendance_times)
                                                <br/>
                                                <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                <br/>
                                                <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(1,10))}}</span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            @endfor
                            <td>0</td>
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
                            <td>فعال</td>
                            <td>{{ $record->trainee->name }}</td>
                            <td>{{ $record->trainee->clean_identity_number }}</td>
                            <td style="text-align: center;">
                                @if ($record->start_date)
                                    {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                                @else
                                    {{ count($days) }}
                                @endif
                                @if ($report->with_attendance_times)
                                    <span style="font-size:12px;">
                                            <br/>
                                            دخول:
                                            <br/>
                                            خروج:
                                            </span>
                                @endif
                            </td>
                            @for($i=0;$i<count($days);$i++)
                                <td style="{{ $days[$i]['vacation_day'] ? 'background:#e0e0e0;' : '' }}">
                                    @if ($days[$i]['vacation_day'])
                                        X
                                    @else
                                        @if ($record->start_date)
                                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                &#10003;
                                                @if ($report->with_attendance_times)
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(0,5))}}</span>
                                                @endif
                                            @else
                                                @if ($record->status === 'new_registration')
                                                    {{-- Considered absent --}}
                                                    &#120;
                                                @endif
                                            @endif
                                        @else
                                            &#10003;
                                            @if ($report->with_attendance_times)
                                                <br/>
                                                <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                <br/>
                                                <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(0,5))}}</span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            @endfor
                            <td>0</td>
                        </tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
        <div style="page-break-inside: avoid !important;display:block;">
            <table class="table second-table" style="width:100%;">
                <colgroup>
                    <col style="width:35px">
                    <col style="width:70px">
                    <col style="width:50px">
                    <col style="width:305px">
                    <col style="width:100px">
                    <col style="width:110px">
                </colgroup>
                <thead style="display: none;">
                <tr style="height:60px;">
                    <th colspan="38" style="text-align: center;padding: 10px;font-size: 38px;">
                        {{ $report->company->name_ar }}
                    </th>
                </tr>
                <tr style="height:100px;background:#e0e0e0;">
                    <th dir="ltr" class="vertical-text" style="white-space: nowrap">SI #</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th dir="ltr" class="vertical-text">Emp. #</th>
                    @endif
                    <th class="vertical-text">Status</th>
                    <th class="vertical-text">Employee<br/> name</th>
                    <th class="vertical-text">ID</th>
                    <th colspan="1"></th>
                    @foreach ($days as $day)
                        <th style="width:20px;{{ $day['vacation_day'] ? 'background:#e0e0e0;' : 'background: white;' }}">
                            <div class="vertical-text" style="position:absolute;white-space:nowrap;height:35px;">{{ $day['name'] }}</div>
                        </th>
                    @endforeach
                    <th rowspan="2" style="width:20px;">
                        <div class="vertical-text" style="position:absolute;white-space:nowrap;height:55px;">عدد الغياب</div>
                    </th>
                </tr>
                <tr style="height:120px;background:#e0e0e0;">
                    <th class="vertical-text">م</th>
                    @if ($report->trainees()->where('job_number', '!=', NULL)->count())
                        <th class="vertical-text" style="white-space: nowrap">الرقم الوظيفي</th>
                    @endif
                    <th class="vertical-text">الحالة</th>
                    <th class="vertical-text" style="width:500px;">اسم الموظف</th>
                    <th class="vertical-text">السجل المدني</th>
                    <th class="vertical-text" style="white-space: nowrap">
                        <span>عدد ايام الدوام حسب</span>
                        <br/>
                        <span>الالتحاق بالتأمينات</span>
                    </th>
                    @foreach ($days as $day)
                        <th style="width:20px;{{ $day['vacation_day'] ? 'background:#e0e0e0;' : 'background: white;' }}">
                            <div class="vertical-text" style="position:absolute;white-space:nowrap;height:35px;width:30px;">{{ $day['date'] }}</div>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @if(\App\Models\Back\Trainee::where('company_id', $report->company_id)->where('job_number', '!=', NULL)->count() > 0)
                    @foreach ($active_trainees as $counter => $record)
                        @if(($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)))
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td>{{ $record->trainee->job_number }}</td>
                                <td>فعال</td>
                                <td>{{ $record->trainee->name }}</td>
                                <td>{{ $record->trainee->clean_identity_number }}</td>
                                <td style="text-align: center;">
                                    @php
                                        $workDays = 0;
                                        foreach ($days as $day) {
                                            // Count all days (including vacation days) to match header count
                                            if ($record->start_date) {
                                                // For resigned trainees, count days within their work period
                                                if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                    $workDays++;
                                                }
                                            } else {
                                                // For active trainees, count all days
                                                $workDays++;
                                            }
                                        }
                                    @endphp
                                    {{ $workDays }}
                                    @if ($report->with_attendance_times)
                                        <span style="font-size:12px;">
                                                <br/>
                                                دخول:
                                                <br/>
                                                خروج:
                                            </span>
                                    @endif
                                </td>
                                @for($i=0;$i<count($days);$i++)
                                    <td style="{{ $days[$i]['vacation_day'] ? 'background:#e0e0e0;' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            X
                                        @else
                                            @if ($record->start_date)
                                                @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                    &#10003;
                                                    @if ($report->with_attendance_times)
                                                        <br/>
                                                        <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                        <br/>
                                                        <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(0,5))}}</span>
                                                    @endif
                                                @else
                                                    @if ($record->status === 'new_registration')
                                                        {{-- Considered absent --}}
                                                        &#120;
                                                    @endif
                                                @endif
                                            @else
                                                &#10003;
                                                @if ($report->with_attendance_times)
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(0,5))}}</span>
                                                @endif
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
                        @if(($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)))
                            <tr>
                                <td>{{ ++$counter }}</td>
                                <td>فعال</td>
                                <td>{{ $record->trainee->name }}</td>
                                <td>{{ $record->trainee->clean_identity_number }}</td>
                                <td style="text-align: center;">
                                    @php
                                        $workDays = 0;
                                        foreach ($days as $day) {
                                            // Count all days (including vacation days) to match header count
                                            if ($record->start_date) {
                                                // For resigned trainees, count days within their work period
                                                if ($day['date_carbon']->isBetween($record->start_date, $record->end_date)) {
                                                    $workDays++;
                                                }
                                            } else {
                                                // For active trainees, count all days
                                                $workDays++;
                                            }
                                        }
                                    @endphp
                                    {{ $workDays }}
                                    @if ($report->with_attendance_times)
                                        <span style="font-size:12px;">
                                                <br/>
                                                دخول:
                                                <br/>
                                                خروج:
                                            </span>
                                    @endif
                                </td>
                                @for($i=0;$i<count($days);$i++)
                                    <td style="{{ $days[$i]['vacation_day'] ? 'background:#e0e0e0;' : '' }}">
                                        @if ($days[$i]['vacation_day'])
                                            @if ($record->start_date && $days[$i]['date_carbon']->isAfter($record->end_date))
                                                {{-- Weekend after resignation date - show empty --}}
                                            @else
                                                X
                                            @endif
                                        @else
                                            @if ($record->start_date)
                                                @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                                    &#10003;
                                                    @if ($report->with_attendance_times)
                                                        <br/>
                                                        <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                        <br/>
                                                        <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(0,5))}}</span>
                                                    @endif
                                                @else
                                                    @if ($record->status === 'new_registration')
                                                        {{-- Considered absent --}}
                                                        &#120;
                                                    @endif
                                                @endif
                                            @else
                                                &#10003;
                                                @if ($report->with_attendance_times)
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">08:{{sprintf("%02d",rand(1,10))}}</span>
                                                    <br/>
                                                    <span style="font-size:8px;text-align: center;">16:{{sprintf("%02d",rand(0,5))}}</span>
                                                @endif
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
                {{--                        <tr>--}}
                {{--                            <td colspan="6" style="text-align: center;">الإجمالي العام</td>--}}
                {{--                            <td colspan="{{ count($days) }}"></td>--}}
                {{--                            <td>0</td>--}}
                {{--                        </tr>--}}
                <tr>
                    @if ($report->with_logo && !$base64logo)
                        @if ($report->falls_under_ptc_net)
                            <td colspan="100%" style="background:#e0e0e0;text-align: center;">** هذا الكشف صحيح مالم تشعر الشركة من قبل العميل ببريد الكتروني يفيد بخلاف ذلك خلال ٥ ايام عمل من تاريخه.
                                في حال وجود اي استفسارات لا تترددو بالتواصل معنا على البريد الاكتروني.</td>
                        @else
                            <td colspan="100%" style="background:#e0e0e0;text-align: center;">** يعتبر الكشف صحيح ما لم يردنا اي ملاحظات خلال الاسبوع من الارسال</td>
                        @endif
                    @endif
                </tr>
                </tbody>
            </table>
            @if ($report->with_logo && !$base64logo)
                <div class="row" style="text-align:center;">
{{--                    <img style="margin:0 auto;border:none;" src="{{ public_path('/img/ptc_stamp_2023.png')}}" alt="logo" width="200"/>--}}
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
