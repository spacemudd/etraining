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
        @foreach ($courseBatchSessions as $session)
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center">
                <strong>{{ $session->starts_at_timezone }}</strong>
            </th>
        @endforeach
    </tr>
    </thead>

    <tbody>

    </tbody>
</table>
