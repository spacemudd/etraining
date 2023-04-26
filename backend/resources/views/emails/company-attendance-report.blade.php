@component($report->company->is_ptc_net ? 'vendor.mail.html.messageptcnet' : 'vendor.mail.html.message')

شكرا لكم،

مرفق لكم كشف الحضور والانصراف للمتدربين.


مع تحياتنا،


@if ($report->company->is_ptc_net)
شركة مركز احترافية التدريب
@else
شركة شركة مركز احترافية التدريب
@endif

@endcomponent
