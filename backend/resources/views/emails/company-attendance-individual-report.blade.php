@component($report->company->is_ptc_net ? 'vendor.mail.html.messageptcnet' : 'vendor.mail.html.message')

تحية طيبة،

مرفق لكم كشف الحضور والانصراف للمتدربة.

**الاسم:** {{ $trainee->name }}

**الهوية:** {{ $trainee->identity_number }}


مع تحياتنا،

شركة مركز احترافية التدريب
@endcomponent
