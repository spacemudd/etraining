<table>
    <thead>
    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.course-name') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $courseName }}</strong></th>
    </tr>

    <tr></tr>
    <tr></tr>

    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.name') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.email') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.absent-counts') }}</strong></th>
    </tr>

    @foreach ($attendanceRecords as $attendance)
        <tr>
            <th style="width:50px;border:1px solid black;">{{ $attendance->trainee->name }}</th>
            <th style="width:50px; text-align:left;border:1px solid black;">{{ $attendance->trainee->email }}</th>
            <th style="width:50px; text-align:center;border:1px solid black;">{{ '="'.$attendance->trainee->phone.'"' }}</th>
            <th style="width:50px; text-align:center;border:1px solid black;">{{ optional($attendance->trainee->company)->name_ar }}</th>
            <th style="width:50px; text-align:center;border:1px solid black;{{ $attendance->warnings_count >= 4 ? 'background-color:red;' : '' }}">{{ $attendance->warnings_count }}</th>
        </tr>
    @endforeach

    </thead>

    <tbody>

    </tbody>
</table>
