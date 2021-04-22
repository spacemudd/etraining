<table>

    @foreach ($companies as $company)

    <thead>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.company') }}:</strong></th>
            @if (app()->getLocale() === 'ar')
                <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong><a href="{{ $company->show_url }}">{{ $company->name_ar }} Arabic</a></strong></th>
            @else
                 <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong><a href="{{ $company->show_url }}">{{ $company->name_en }} English</a></strong></th>
            @endif
        </tr>

        <tr></tr>

        <tr>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.reference_number') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.contract-start-date') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.contract-end-date') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.contract_period_in_months') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.auto_renewal') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.trainees_count') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.trainee_salary') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.instructor_cost') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company_reimbursement') }}</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.notes') }}</strong></th>

        </tr>
    </thead>

    <tbody>

        @foreach ($company->contracts as $contract)

            <tr>

                <td style="width:50px;text-align:center;border:1px solid black;">
                    {{ $contract->reference_number }}
                </td>

                <td style="border:1px solid black;">{{ $contract->contract_starts_at }}</td>

                <td style="text-align:center;border:1px solid black;">
                    {{ $contract->contract_ends_at }}
                </td>

                <td style="border:1px solid black;">
                    {{ $contract->contract_period_in_months }}
                </td>

                @if ($contract->auto_renewal)
                    <td style="border:1px solid black;">
                        {{ __('words.activated') }}
                    </td>
                @else
                    <td style="border:1px solid black;">
                        {{ __('words.deactivated') }}
                    </td>
                @endif

                <td style="border:1px solid black;">
                    {{ $contract->trainees_count }}
                </td>

                <td style="border:1px solid black;">
                    {{ $contract->trainee_salary }}
                </td>

                <td style="border:1px solid black;">
                    {{ $contract->instructor_cost }}
                </td>

                <td style="border:1px solid black;">
                    {{ $contract->company_reimbursement }}
                </td>

                <td style="border:1px solid black;">
                    {{ $contract->notes }}
                </td>

            </tr>
        <tr></tr>
        @endforeach

    </tbody>

    @endforeach
</table>
