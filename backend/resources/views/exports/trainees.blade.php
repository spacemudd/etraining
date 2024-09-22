<table>
    <thead>
    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.date') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ now()->format('Y-m-d') }}</strong></th>
    </tr>
    @if (isset($trainee_status_id))
    <tr>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ __('words.status') }}:</strong></th>
        <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;">
            <strong>
                @if (!$trainee_status_id)
                    {{ __('words.all') }}
                @elseif ($trainee_status_id === \App\Models\Back\Trainee::STATUS_PENDING_UPLOADING_FILES)
                    {{ __('words.incomplete-application') }}
                @elseif ($trainee_status_id === \App\Models\Back\Trainee::STATUS_APPROVED)
                    {{ __('words.approved') }}
                @elseif ($trainee_status_id === \App\Models\Back\Trainee::STATUS_PENDING_APPROVAL)
                    {{ __('words.nominated-instructor') }}
                @endif
            </strong>
        </th>
    </tr>
    @endif
    <tr></tr>
    <tr>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center;"><strong>{{ __('words.name') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.email') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.phone_additional') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.company-register-date') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.identity_number') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.status') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.city') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.birthday') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.educational_level') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.marital_status') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.children_count') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.created-at') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.instructor') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.group') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.blocked') }}</strong></th>
        <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>{{ __('words.reason') }}</strong></th>
        <th style="border:1px solid red;background-color:red;width:50px; text-align:center"><strong>{{ __('words.caution') }}</strong></th>
    </tr>
    </thead>

    <tbody>
    @foreach ($trainees as $trainee)
        <tr>
            <td style="width:50px;text-align:center;border:1px solid black;{{ $trainee->deleted_at ? 'background-color:#f95d5d' : '' }}">
               {{ $trainee->name }}
            </td>
            <td style="border:1px solid black;">
                {{ $trainee->email }}
            </td>
            <td style="text-align:center;border:1px solid black;">
                ="{{ $trainee->clean_phone }}"
            </td>
            <td style="text-align:center;border:1px solid black;">
                ="{{ $trainee->clean_phone_additional }}"
            </td>
            <td style="text-align:center;border:1px solid black;">{{ optional($trainee->company)->name_ar }}</td>
            <td style="text-align:center;border:1px solid black;">
                {{ optional($trainee->audits()->where('new_values', 'LIKE', '%company_id%')->latest()->first())->created_at_timezone }}
            </td>
            <td style="border:1px solid black;">{{ $trainee->identity_number }}</td>
            <td style="border:1px solid black;">
                @if ((int) $trainee->status === \App\Models\Back\Trainee::STATUS_PENDING_UPLOADING_FILES)
                    {{ __('words.incomplete-application') }}
                @elseif ((int) $trainee->status === \App\Models\Back\Trainee::STATUS_APPROVED)
                    {{ __('words.approved') }}
                @elseif ((int) $trainee->status === \App\Models\Back\Trainee::STATUS_PENDING_APPROVAL)
                    {{ __('words.nominated-instructor') }}
                @endif
            </td>
            <td style="border:1px solid black;">{{ optional($trainee->city)->name_ar }}</td>
            <td style="border:1px solid black;">{{ $trainee->birthday }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->educational_level)->name_ar }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->marital_status)->name_ar }}</td>
            <td style="border:1px solid black;">{{ $trainee->children_count ?: '' }}</td>
            <td style="border:1px solid black;">{{ $trainee->created_at_timezone }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->instructor)->name }}</td>
            <td style="border:1px solid black;">{{ optional($trainee->trainee_group)->name }}</td>
            <td style="border:1px solid black;">{{ $trainee->deleted_at_timezone }}</td>
            <td style="border:1px solid black;">{{ $trainee->deleted_remark }}</td>
            @if($trainee->dont_edit_notice)
             <td style="border:1px solid red;color:red;">{{__('words.dont-take-action-against-this-account-without-admin-approval')}}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
