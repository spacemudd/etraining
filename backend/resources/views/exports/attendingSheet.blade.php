<table>
    <thead>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.course-name') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong><a href="{{ $course_batch->course->show_url }}">{{ $course_name }}</a></strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.instructor') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ optional($course_batch->course->instructor)->name }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.start-date') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $course_batch->starts_at_timezone }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.end-date') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $course_batch->ends_at_timezone }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.attendance') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $attendeesCount }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.absent') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $absenteesCount }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.attendance-rate') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $attendanceRate }}%</strong></th>
        </tr>
        <tr></tr>
        <tr>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.registration-date') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.name') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.email') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.identity_number') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.attendance') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.time') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.last-login-to-platform') }}</strong></th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td colspan="8" style="border:1px solid black;;text-align:center;background-color:#d4d4d4">{{ __('words.attendees-plus-late-attendees') }}</td>
        </tr>

        @foreach ($attendances as $attendanceRecord)
            <tr>
                <td style="width:50px; text-align:center;border:1px solid black;">
                    {{ $attendanceRecord->trainee->created_at->format('d-m-Y') }}
                </td>
                <td style="width:50px; text-align:center;border:1px solid black;{{ $attendanceRecord->trainee->deleted_at ? 'background-color:#f95d5d' : '' }}">
                    @if ($attendanceRecord->trainee->deleted_at)
                        <a href="{{ $attendanceRecord->trainee->show_url }}">
                            ({{ __('words.blocked') }}) {{ $attendanceRecord->trainee->name }}
                        </a>
                    @else
                        <a href="{{ $attendanceRecord->trainee->show_url }}">{{ $attendanceRecord->trainee->name }}</a>
                    @endif
                </td>
                <td style="border:1px solid black;">{{ $attendanceRecord->trainee->email }}</td>
                <td style="border:1px solid black;">{{ $attendanceRecord->trainee->identity_number }}</td>
                <td style="text-align:center;border:1px solid black;">
                    ="{{ $attendanceRecord->trainee->phone }}"
                </td>
                <td style="border:1px solid black;">{{ optional($attendanceRecord->trainee->company)->name_ar }}</td>
                <td style="width:50px; text-align:center; color:{{ $attendanceRecord->status_color }}; background-color:darkgrey;border:1px solid black;">
                    {{ __('words.'.$attendanceRecord->status_name) }}
                </td>
                <td style="border:1px solid black;">
                    {{ $attendanceRecord->attended_at_timezone }}
                </td>
                <td style="border:1px solid black;">
                    {{ optional($attendanceRecord->trainee->user)->last_login_at_timezone }}
                </td>
            </tr>
        @endforeach

        @if (count($users_who_didnt_attend))
            <tr>
                <td colspan="8" style="border:1px solid black;text-align:center;background-color:#d4d4d4">{{ __('words.didnt-attend-at-all') }}</td>
            </tr>
        @endif
        @foreach ($users_who_didnt_attend as $attendanceRecord)
            <tr>
                <td style="width:50px; text-align:center;border:1px solid black;">
                    {{ $attendanceRecord->trainee->created_at->format('d-m-Y') }}
                </td>
                <td style="width:50px; text-align:center;border:1px solid black;{{ $attendanceRecord->trainee->deleted_at ? 'background-color:#f95d5d' : '' }}">
                    @if ($attendanceRecord->trainee->deleted_at)
                        <a href="{{ $attendanceRecord->trainee->show_url }}">
                            ({{ __('words.blocked') }}) {{ $attendanceRecord->trainee->name }}
                        </a>
                    @else
                        <a href="{{ $attendanceRecord->trainee->show_url }}">{{ $attendanceRecord->trainee->name }}</a>
                    @endif
                </td>
                <td style="border:1px solid black;">{{ $attendanceRecord->trainee->email }}</td>
                <td style="text-align:center;border:1px solid black;">
                    ="{{ $attendanceRecord->trainee->phone }}"
                </td>
                <td style="border:1px solid black;">{{ optional($attendanceRecord->trainee->company)->name_ar }}</td>
                <td style="width:50px; text-align:center; color:{{ $attendanceRecord->status_color }}; background-color:darkgrey;border:1px solid black;">
                    {{ __('words.'.$attendanceRecord->status_name) }} @if ($attendanceRecord->absence_reason) ({{ $attendanceRecord->absence_reason }}) @endif
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;">
                    @if ($attendanceRecord->trainee->user)
                        {{ $attendanceRecord->trainee->user->last_login_at ?: 'لم يدخل إطلاقاً الى المنصة'}}
                    @else
                        لا يوجد حساب
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
