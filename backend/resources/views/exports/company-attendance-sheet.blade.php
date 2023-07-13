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
        <tr>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>SI #</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>Emp. #</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>Status</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>Employee<br/> name</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><strong>ID</strong></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"colspan="1"></th>
            <th style="border:1px solid black;background-color:#a0a0a0;width:50px; text-align:center"><div class="vertical-text" style="position:absolute;white-space:nowrap;height:55px;">عدد الغياب</div></th>
        </tr>
        <tr style="height:120px;background:#e0e0e0;">
            <th class="vertical-text">م</th>
            <th class="vertical-text" style="white-space: nowrap">الرقم الوظيفي</th>
            <th class="vertical-text">الحالة</th>
            <th class="vertical-text" style="width:500px;">اسم الموظف</th>
            <th class="vertical-text">السجل المدني</th>
            <th class="vertical-text" style="white-space: nowrap">
                <span>عدد ايام الدوام حسب</span>
                <br/>
                <span>الالتحاق بالتأمينات</span>
            </th>
        </tr>
    </thead>

    <tbody style="page-break-inside: avoid;">
    <tr>
        <td>مسجل</td>
        <td>فعال</td>
{{--        <td>{{ $report->trainees()->name }}</td>--}}
{{--        <td>{{ $trainee->id }}</td>--}}
{{--        <td>{{ $trainee->identity_number }}</td>--}}
{{--        <td>{{ $trainee->email }}</td>--}}
    </tr>
    </tbody>
</table>
