<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <style>
        body{
            background-color: #f0f0f0;
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
    <meta charset="utf-8">
    <img src="https://app.ptc-ksa.com/img/logo-lg.png" alt="PTC KSA">
</head>
<body style="text-align:right;">
<div>
    <h3 style="font-weight: bold;"> نود اشعاركم بأنه تم حذف المتدربة {{ $trainee->name }}.</h3>
    <br>
    <h5>بيانات المتدربة:</h5>
    <br>
    <table id="customers">
        <tr style="background-color: #ec5b5b;">
            <th>الإسم</th>
            <th>الهوية</th>
            <th>الشركة</th>
            <th>سبب الإيقاف</th>
            <th>تاريخ الحذف</th>
            <th>بواسطة</th>
        </tr>
        <tr>
            <td>{{ $trainee->name }}</td>
            <td>{{ $trainee->identity_number }}</td>
            <td>{{ $trainee->company->name_ar }}</td>
            <td>{{ $trainee->deleted_remark }}</td>
            <td>{{ $trainee->deleted_at }}</td>
            <td>{{ $email }}</td>
        </tr>
    </table>
    <br>
</div>
</body>
</html>