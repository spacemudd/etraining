<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="{{ public_path('pdf.css') }}">
    <title>شهادة حضور دورة تطويرية</title>
    <style>
        * {
            direction: rtl;
        }
        #page-cover {
            position: absolute;
            top:0;
            /*display:block;*/
            width:100% !important;
            background-repeat: no-repeat;
            left: 0;
            height: 1017px;
            /*z-index: 1;*/
        }
        .content {
            text-align: center;
            padding: 2rem;
        }
        h1 {
            font-size: 72px;
            color: rgb(192, 0, 0);
        }
        p {
            font-size: 30px;
            direction: rtl;
            font-weight: bold;
        }
        #page-stamp {
            max-width: 270px;
            margin-top: -35px;
        }
        .small-text {
            font-size: 20px;
        }
        .page-logo {
            margin-top: 30px;
            margin-left: 30px;
            max-width: 230px;
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="content">
        <img class="page-logo" src="{{ public_path('/img/logo-lg.png') }}">
        <h1 style="margin-top:8rem;">شهادة حضور دورة تطويرية</h1>
        <p style="margin-top:2.5rem;">يشهد مركز احترافية المدرب للتدريب بأن المتدربة  "{{ $certificate->trainee->name }}"</p>
        <p style="margin-top:2.5rem;">وجنسيتها سعودية بموجب السجل المدني رقم "{{ $certificate->trainee->identity_number }}"</p>
        <p style="margin-top:2.5rem;">قـــد حضرت دورة ({{ $certificate->course->name_ar }}) عدد ساعاتها التدريبية 16 ساعة</p>
        <p style="margin-top:2.5rem;">وعدد أيامها التدريبية (8) أيام والمنعقدة في الفترة من </p>
        <p style="margin-top:2.5rem;">(14 - 1 - 1443هـ  إلى 22 - 2 - 1443هـ  )</p>
        <p style="margin-top:2.5rem;">والمعتمدة من المؤسسة العامة للتدريب التقني والمهني برقم </p>
        <p style="margin-top:2.5rem;">(192295832)</p>
        <p style="margin-top:5rem;">والله الموفق،،،</p>
        <div class="row" style="margin-top: 2rem;">
            <div class="col-7">
                <p style="margin-bottom:0;padding-bottom: 0"><b>ختم المنشأة التدريبية</b></p>
                <img id="page-stamp" src="{{ public_path('/img/certificate-stamp.png') }}">
            </div>
            <div class="col-4" style="text-align: right;">
                <p class="small-text">
                    <b>حررت بتاريخ:    22 / 2  /1443  هـ </b><br/>
                    <b>الموافق:            29 /  9 /2021 م</b>
                </p>
                <p class="small-text">ملاحظة: أي كشط او تعديل على الشهادة تعتبر لاغية</p>
            </div>
        </div>
    </div>
    <img id="page-cover" src="{{ public_path('/img/certificate-bg.png') }}"/>
</body>
</html>
