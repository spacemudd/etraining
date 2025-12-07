@component($report->company->is_ptc_net ? 'vendor.mail.html.messageptcnet' : 'vendor.mail.html.message')

شكراً لكم،

مرفق لكم كشف الحضور والانصراف.

مع تحياتنا،

{{-- <a href="{{ url('/company-attendance-reports/approve/' . $report->id) }}" style="display:inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
    الاعتماد الآن
</a> --}}

@endcomponent
