<table>
    <thead>

        <tr>
            <th style="width:50px; text-align:center"> <strong>  {{ __('words.course-name') }}: </strong> </th>
            <th></th>
            <th></th>
            <th style="width:50px; text-align:center">  <strong> {{ $course_name }} </strong></th>
        </tr>

        <tr></tr>

        <tr>
            <th style="width:50px; text-align:center"> <strong>  {{ __('words.name') }} </strong> </th>
            <th></th>
            <th></th>
            <th style="width:50px; text-align:center">  <strong> {{ __('words.email') }} </strong></th>
            <th style="width:50px; text-align:center">  <strong> {{ __('words.attendance') }} </strong></th>
            <th style="width:50px; text-align:center">  <strong> {{ __('words.time') }} </strong></th>
            <th style="width:50px; text-align:center">  <strong> {{ __('words.last-login-at') }} </strong></th>
        </tr>

    </thead>

    <tbody>

        @foreach ($users_who_didnt_attend as $trainee_profile)
            <tr>
                <td style="width:50px; text-align:center; ">
                    {{ $trainee_profile->name }}
                </td>
                <td>{{ $trainee_profile->email }}</td>
                <td>{{ $trainee_profile->phone }}</td>
                <td style="width:50px; text-align:center; color:red; background-color:darkgrey">
                    {{__('words.absent')}}
                </td>
                <td></td>
                <td>
                    @if ($trainee_profile->user)
                        {{ $trainee_profile->user->last_login_at ?: 'لم يدخل إطلاقاً الى المنصة'}}
                    @else
                        No user
                    @endif
                </td>
            </tr>
        @endforeach

        @foreach ($attendances as $attendance)
            <tr>
                <td style="width:50px; text-align:center; ">
                    {{ $attendance->trainee->name }}
                </td>
                <td>{{ $attendance->trainee->email }}</td>
                <td>{{ $attendance->trainee->phone }}</td>
                @if ($attendance->attended)
                    <td style="width:50px; text-align:center; color:blue; background-color:darkgrey">
                        {{__('words.present')}}
                    </td>
                    @else
                    <td style="width:50px; text-align:center; color:red; background-color:darkgrey">
                        {{__('words.absent')}}
                    </td>
                @endif
                <td>
                    {{ $attendance->attended_at_timezone }}
                </td>
                <td>
                    {{ $attendance->trainee->user->last_login_at_timezone }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td>------</td>
        </tr>



    </tbody>

</table>
