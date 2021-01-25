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
            <th style="width:50px; text-align:center">  <strong> {{ __('words.attendance') }} </strong></th>
            <th style="width:50px; text-align:center">  <strong> {{ __('words.time') }} </strong></th>
        </tr>

    </thead>

    <tbody>

        @foreach ($users as $user)
            <tr>
                <td style="width:50px; text-align:center; ">
                    {{ $user->trainee->name }}
                </td>
                <td></td>
                <td></td>
                @if ($user->attended)
                    <td style="width:50px; text-align:center; color:blue; background-color:darkgrey">
                        {{__('words.present')}}
                    </td>
                    @else
                    <td style="width:50px; text-align:center; color:red; background-color:darkgrey">
                        {{__('words.absent')}}
                    </td>
                    <td>
                        {{ $trainee->attended_at }}
                    </td>
                @endif
            </tr>
        @endforeach

        <tr>
            <td>------</td>
        </tr>

        @foreach ($users_who_didnt_attend as $trainee)
            <tr>
                <td style="width:50px; text-align:center; ">
                    {{ $trainee->name }}
                </td>
                <td></td>
                <td></td>
                @if ($trainee->attended)
                    <td style="width:50px; text-align:center; color:blue; background-color:darkgrey">
                        {{__('words.present')}}----
                    </td>
                @else
                    <td style="width:50px; text-align:center; color:red; background-color:darkgrey">
                        {{__('words.absent')}}
                    </td>
                @endif
{{--                <td>--}}
{{--                    {{ $trainee->attended_at }}--}}
{{--                </td>--}}
            </tr>
        @endforeach



    </tbody>

</table>
