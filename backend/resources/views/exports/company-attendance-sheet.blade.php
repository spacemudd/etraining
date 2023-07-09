<table>
    <thead>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>رقم التقرير</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->number }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>من</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->date_from->format('Y-m-d') }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>الى</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->date_to->format('Y-m-d') }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>العدد</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->activeTraineesCount() }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>الشركة</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->company->name_ar }}</strong></th>
        </tr>
        <tr></tr>
        <tr>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>SI #</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>Emp. #</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>Status</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>Employee<br/> name</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>ID</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"colspan="1"></th>
            @foreach ($days as $day)
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><div class="vertical-text" style="position:absolute;white-space:nowrap;height:35px;">{{ $day['name'] }}</div></th>
            @endforeach
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><div class="vertical-text" style="position:absolute;white-space:nowrap;height:55px;">عدد الغياب</div></th>
        </tr>
        <tr style="height:120px;background:#e0e0e0;">
            <th class="vertical-text">م</th>
            <th class="vertical-text" style="white-space: nowrap">الرقم الوظيفي</th>
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
    @foreach ($active_trainees as $counter => $record)
        @if(! ($counter ===  (count($active_trainees) - 1) || $counter ===  (count($active_trainees) - 2)) )
            @if ($record->status === 'temporary_stop')
                @continue
            @endif
            <tr>
                <td>{{ ++$counter }}</td>
                <td>مسجل</td>
                <td>فعال</td>
                <td>{{ $record->trainee->name }}</td>
                <td>{{ $record->trainee->clean_identity_number }}</td>
                <td style="text-align: center;">
                    @if ($record->start_date)
                        {{ $record->start_date->diffInDays($record->end_date) + 1 }}
                    @else
                        {{ count($days) }}
                    @endif
                </td>
                @for($i=0;$i<count($days);$i++)
                    <td style="{{ $days[$i]['vacation_day'] ? 'background:#e0e0e0;' : '' }}">
                        @if ($record->start_date)
                            @if ($days[$i]['date_carbon']->isBetween($record->start_date, $record->end_date))
                                &#10003;
                            @else
                                @if ($record->status === 'new_registration')
                                    {{-- Considered absent --}}
                                    &#120;
                                @endif
                            @endif
                        @else
                            &#10003;
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
    </tbody>
</table>
