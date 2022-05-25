<table>
    <thead>
    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.joined-date') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.trainee') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.identity_number') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.leave-date') }}</strong></th>
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
                    {{ $record->in_date_ksa }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee_name }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee_identity_number }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->trainee_phone_number }}
                </td>
                <td style="border:1px solid black;">
                    {{ $record->out_date_ksa }}
                </td>
                <td style="border:1px solid black;">

                </td>
                <td style="border:1px solid black;">
                    {{ $record->trainee->company->name_ar }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
