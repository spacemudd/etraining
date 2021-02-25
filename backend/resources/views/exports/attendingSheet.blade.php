<table>
    <thead>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.course-name') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong><a href="{{ $course_batch->course->show_url }}">{{ $course_name }}</a></strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.instructor') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $course_batch->course->instructor->name }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.start-date') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $course_batch->starts_at_timezone }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.end-date') }}:</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $course_batch->ends_at_timezone }}</strong></th>
        </tr>
        <tr></tr>
        <tr>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.name') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.email') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.attendance') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.time') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.last-login-to-platform') }}</strong></th>
        </tr>
    </thead>

    <tbody>
        @if (count($users_who_didnt_attend))
            <tr>
                <td colspan="7" style="border:1px solid black;text-align:center;background-color:#d4d4d4">{{ __('words.didnt-attend-at-all') }}</td>
            </tr>
        @endif
        @foreach ($users_who_didnt_attend as $trainee_profile)
            <tr>
                <td style="width:50px; text-align:center;border:1px solid black;">
                    <a href="{{ $trainee_profile->show_url }}">{{ $trainee_profile->name }}</a>
                </td>
                <td style="border:1px solid black;">{{ $trainee_profile->email }}</td>
                <td style="text-align:center;border:1px solid black;">
                    ="{{ $trainee_profile->phone }}"
                </td>
                <td style="border:1px solid black;">{{ optional($trainee_profile->company)->name_ar }}</td>
                <td style="width:50px; text-align:center; color:red; background-color:darkgrey;border:1px solid black;">
                    {{__('words.absent')}}
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;">
                    @if ($trainee_profile->user)
                        {{ $trainee_profile->user->last_login_at ?: 'لم يدخل إطلاقاً الى المنصة'}}
                    @else
                        No user
                    @endif
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="7" style="border:1px solid black;;text-align:center;background-color:#d4d4d4">{{ __('words.attendees-plus-late-attendees') }}</td>
        </tr>

        @foreach ($attendances as $attendance)
            <tr>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    <a href="{{ $attendance->trainee->show_url }}">{{ $attendance->trainee->name }}</a>
                </td>
                <td style="border:1px solid black;">{{ $attendance->trainee->email }}</td>
                <td style="text-align:center;border:1px solid black;">
                    ="{{ $attendance->trainee->phone }}"
                </td>
                <td style="border:1px solid black;">{{ optional($attendance->trainee->company)->name_ar }}</td>
                @if ($attendance->attended)
                    <td style="width:50px; text-align:center; color:blue; background-color:darkgrey;border:1px solid black;">
                        {{__('words.present')}}
                    </td>
                    @else
                    <td style="width:50px; text-align:center; color:red; background-color:darkgrey;border:1px solid black;">
                        {{__('words.absent')}}
                    </td>
                @endif
                <td style="border:1px solid black;">
                    {{ $attendance->attended_at_timezone }}
                </td>
                <td style="border:1px solid black;">
                    {{ optional($attendance->trainee->user)->last_login_at_timezone }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
