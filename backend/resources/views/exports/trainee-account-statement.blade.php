<table>
    <thead>
    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.reference_number') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.transaction-date') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.account-name') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.description') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.debit') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.credit') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.balance') }}</strong></th>
    </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $record)
            <tr>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->id }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->date }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->account_name }}
                </td>
                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $record->description }}
                </td>
                <td style="border:1px solid black;">
                    {{ $record->debit }}
                </td>
                <td style="border:1px solid black;">
                    {{ $record->credit }}
                </td>
                <td style="border:1px solid black;">
                    {{ $record->balance }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
