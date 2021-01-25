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
                @endif
            </tr>
        @endforeach

        @foreach ($users_who_didnt_attend as $user)
            <tr>
                <td style="width:50px; text-align:center; ">
                    {{ $user->trainee->name }}
                </td>
                <td></td>
                <td></td>
                @if ($user->attended)
                    <td style="width:50px; text-align:center; color:blue; background-color:darkgrey">
                        {{__('words.present')}}----
                    </td>
                @else
                    <td style="width:50px; text-align:center; color:red; background-color:darkgrey">
                        {{__('words.absent')}}
                    </td>
                @endif
            </tr>
        @endforeach



    </tbody>

</table>
