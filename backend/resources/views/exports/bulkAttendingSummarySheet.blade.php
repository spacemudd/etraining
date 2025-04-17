<table>
    <thead>
    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.course-name') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $courseName }}</strong></th>
    </tr>

    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.company') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ optional($company)->name_ar }}</strong></th>
    </tr>

    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.start-date') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ optional($startDate)->format('d-m-Y') }}</strong></th>
    </tr>

    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.end-date') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ optional($endDate)->format('d-m-Y') }}</strong></th>
    </tr>

    <tr></tr>
    <tr></tr>

    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.course') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.name') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.email') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.identity_number') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.absent-counts') }}</strong></th>
    </tr>

    @foreach ($attendanceRecords as $attendance)
        @if ($attendance->trainee)
            <tr>
                <th style="width:50px;border:1px solid black;">{{ optional($attendance->trainee)->name }}</th>
                <th style="width:50px;border:1px solid black;">{{ optional($attendance->trainee)->name }}</th>
                <th style="width:50px; text-align:left;border:1px solid black;">{{ optional($attendance->trainee)->email }}</th>
                <th style="width:50px; text-align:left;border:1px solid black;">{{ optional($attendance->trainee)->identity_number }}</th>
                <th style="width:50px; text-align:center;border:1px solid black;">{{ '="'.optional($attendance->trainee)->phone.'"' }}</th>
                <th style="width:50px; text-align:center;border:1px solid black;">{{ optional(optional($attendance->trainee)->company)->name_ar }}</th>
                <th style="width:50px; text-align:center;border:1px solid black;{{ $attendance->warnings_count >= 4 ? 'background-color:red;' : '' }}">{{ $attendance->warnings_count }}</th>
            </tr>
        @endif
    @endforeach

    </thead>

    <tbody>

    </tbody>
</table>
