@component('mail::message')
# @lang('words.hi-there')!

تم اصدار تقرير الحضور للمتدربات ({{ optional($report->approved_at)->setTimezone('Asia/Riyadh')->toDateString() }})، ويرجى الإطلاع على المرفق

@lang('words.with-regards')
@endcomponent
