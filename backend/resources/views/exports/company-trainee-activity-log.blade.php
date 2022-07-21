<table>
    <thead>
    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.joined-date') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.trainee') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.identity_number') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.blocked') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.reason') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong></strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.current-company-as-of-today') }}</strong></th>
    </tr>
    </thead>
    <tbody>
        @foreach ($activity_logs as $record)
            <tr>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->company->name_ar }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->created_at->format('Y-m-d') }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee->name }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee->identity_number }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee->phone }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee->deleted_at }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee->deleted_remark }}
                </td>
                <td></td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee->company->name_ar }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
