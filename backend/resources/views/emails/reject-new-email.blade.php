<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700&display=swap" rel="stylesheet">
    <style>
        h3 {
            color: #363636;
            font-family: "Tajawal Medium";
            font-size: 100%;
            line-height: 1.15;
            position: relative;
            text-align: center;
        }
        h4 {
            font-family: "Tajawal Medium";
        }
        body{
            background-color: #e7ecee;
            font-family: "Tajawal Medium";
        }
        div{
            padding: 60px;
            margin-top: 30px;
            margin-left: 60px;
            margin-right: 60px;
            margin-bottom: 60px;
            background-color: white;
            border-radius: 18px;
        }
        img{
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 20%;
        }
        #customers {
            border-collapse: collapse;
            border-radius: 18px;
            overflow: hidden;
            width: 100%;
        }

        #customers td, #customers th {
            font-family: "Tajawal Medium";
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
            h3 {
                color: #363636;
                font-family: "Tajawal Medium";
                font-size: x-small;
                line-height: 1.15;
                position: relative;
                text-align: center;
            }
            h4 {
                font-family: "Tajawal Medium";
                font-size: x-small;
            }
            body{
                background-color: #e7ecee;
                font-family: "Tajawal Medium";
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
            }

            #customers td, #customers th {
                font-family: "Tajawal Medium";
                font-size: xx-small;
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

    <!-- HTML https://img.icons8.com/?size=512&id=63688&format=png
    style="font-weight: bold; width:100px; color: white; background-color: #f54040; align-content: center; margin-right: 60px; margin-left: 60px; border-radius: 10px;"!-->


    <meta charset="utf-8">
    <!-- <img src="https://app.ptc-ksa.net/img/logo.png" alt="PTC KSA"> -->
</head>
<body style="text-align:right;">
<div>
    <img style="width: 80px;" src="https://i.ibb.co/8jgfW7M/icons8-cancel-500.png" alt="Rejected" class="w-56">
    <h3> تم رفض الطلب رقم <p style="color: rgba(86,86,86,0.56); font-size: large" >{{ $email->number }}</p></h3>
</div>
<div>
    <h4>   تفاصيل الطلب:  </h4>
    <br>
    <table id="customers">
        <tr style="background-color: #ec5b5b;">
            <th>مقدم الطلب</th>
            <th>البريد الالكتروني الجديد</th>
        </tr>
        <tr>
            <td>{{ $email->applicant }}</td>
            <td>{{ $email->new_email }}</td>
        </tr>
    </table>
</div>
</body>
</html>
