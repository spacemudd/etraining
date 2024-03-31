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
        .text {
            position: center;
            top: 0;
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
        #customers {
            border-collapse: collapse;
            border-radius: 18px;
            overflow: hidden;
            width: 100%;
        }

        #customers td, #customers th {
            border: 2px solid white;
            padding: 8px;
            margin-bottom: 100px;
        }

        #customers tr:nth-child(even){background-color: #f2f2f2;}

        #customers tr:hover {background-color: #ddd;}

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align:right;
            color: white;
        }
        @media only screen and (max-width: 600px) {
            body{
                background-color: #f0f0f0;
            }
            div{
                padding: 20px;
                margin-top: 10px;
                margin-left: 20px;
                margin-right: 20px;
                margin-bottom: 20px;
                background-color: white;
                border-radius: 5px;
            }
            img{
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 40%;
            }
            #customers {
                border-collapse: collapse;
                border-radius: 15px;
                overflow: hidden;
                width: 20%;
            }

            #customers td, #customers th {
                border: 2px solid white;
                padding: 8px;
                margin-bottom: 30px;
                width: 20%;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}

            #customers tr:hover {background-color: #ddd;}

            #customers th {
                padding-top: 4px;
                padding-bottom: 4px;
                text-align:right;
                color: white;
            }
        }
    </style>
</head>

<body>
<div class="content">
    <img id="page-cover" src="{{ public_path('/img/Certificate-new-bg.jpg') }}"/>

    <div class="text">
{{--        <img class="page-logo" src="{{ public_path('/img/logo-lg.png') }}">--}}

        <div class="row" style="margin-top: 2rem;">
            <h1 style="margin-top:8rem;">حضور دورة تدريبية</h1>
            <p style="margin-top:2.5rem;">بهذا نفيد أن المتدربة  "{{ $certificate->trainee->name }}"</p>
            <p style="margin-top:2.5rem;">وجنسيتها سعودية بموجب السجل المدني رقم "{{ $certificate->trainee->identity_number }}"</p>
            <p style="margin-top:2.5rem;">قـــد أتمت حضور دورة ({{ $certificate->course->name_ar }}) </p>
            <p style="margin-top:2.5rem;">حسب البيانات الموضحة أدناه</p>
            <table id="customers">
                <tr style="background-color: #ec5b5b;">
                    <th>مسمى الدورة</th>
                    <th>نسبة الحضور</th>
                </tr>
                <tr>
                    <td>{{ $certificate->course->name_ar }}</td>
                    <td>100%</td>
                </tr>
            </table>
            <p style="margin-top:5rem;">والله الموفق،،،</p>
            <div class="col-7">
                <p style="margin-bottom:0;padding-bottom: 0"><b> </b></p>
{{--                <img style="padding-top: 20px" id="page-stamp" src="{{ public_path('/img/certificate-stamp-new.png') }}">--}}
            </div>
            <div class="col-4" style="text-align: right;">
                <p>الإدارة <img  id="page-stamp" src="{{ public_path('/img/signature.png') }}"></p>
                <p class="small-text">
                    <b></b><br/>
                    <b></b>
                </p>
                <p class="small-text">ملاحظة: أي كشط او تعديل على الشهادة تعتبر لاغية</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

