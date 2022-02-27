<table>
    <thead>
    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $excel_title }}</strong></th>
    </tr>
    <tr></tr>
    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.date') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ now()->format('Y-m-d') }}</strong></th>
    </tr>
    <tr></tr>
    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.name') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.email') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone_additional') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.identity_number') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.city') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.birthday') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.educational_level') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.marital_status') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.children_count') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.created-at') }}</strong></th>
        @if($archived)
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.delete-remark') }}</strong></th>
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach ($trainees as $trainee)
        <tr>
            <td style="width:50px;text-align:center;border:1px solid black;">
               {{ $trainee->name }}
            </td>
            <td style="border:1px solid black;">
                {{ $trainee->email }}
            </td>
            <td style="text-align:center;border:1px solid black;">
               ="{{ $trainee->phone }}"
            </td>
            <td style="text-align:center;border:1px solid black;">
               ="{{ $trainee->phone_additional }}"
            </td>
            <td style="text-align:center;border:1px solid black;">{{ optional($trainee->company)->name_ar }}</td>
            <td style="border:1px solid black;">{{ $trainee->identity_number }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->city)->name_ar }}</td>
            <td style="border:1px solid black;">{{ $trainee->birthday }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->educational_level)->name_ar }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->marital_status)->name_ar }}</td>
            <td style="border:1px solid black;">{{ $trainee->children_count ?: '' }}</td>
            <td style="border:1px solid black;">{{ $trainee->created_at_timezone }}</td>
            @if($archived)
                <td style="border:1px solid black;">{{ $trainee->deleted_remark }}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
