<table>
    <thead>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>رقم التقرير</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->number }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>من</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->date_from->format('Y-m-d') }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>الى</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->date_to->format('Y-m-d') }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>العدد</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->activeTraineesCount() }}</strong></th>
        </tr>
        <tr>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>الشركة</strong></th>
            <th style="border:1px solid black;width:50px; text-align:center;background-color:yellow;"><strong>{{ $report->company->name_ar }}</strong></th>
        </tr>
        <tr></tr>
        <tr style="height:120px;background:#e0e0e0;">
            <th style="border:1px solid black;background-color:#a0a0a0; text-align:center; width:50px; text-align:center">اسم الموظف</th>
            <th style="border:1px solid black;background-color:#a0a0a0; text-align:center; width:50px; text-align:center">السجل المدني</th>

        </tr>
    </thead>

    <tbody style="page-break-inside: avoid;">
    @foreach ($company_attendance as $attendance)
    <tr>
            <td style="border:1px solid black; text-align:center;">{{ $attendance->trainee->name }}</td>
            <td style="border:1px solid black; text-align:center;"> {{ $attendance->trainee->identity_number }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
