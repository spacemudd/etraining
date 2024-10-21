<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقد تدريب متدرب</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        p, li {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        .section-title {
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
        }
        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .course-table th, .course-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .course-table th {
            background-color: #f2f2f2;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signatures div {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .button-group {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .btn {
        padding: 10px 20px; 
        font-size: 18px; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer;
        transition: background-color 0.3s ease; 
        margin: 20px 10px;
        padding: 10px 50px;
    }

    .btn-success {
        background-color: #28a745; 
        color: white; 
    }

    .btn-danger {
        background-color: #dc3545;
        color: white; 
    }

    .btn-success:hover {
        background-color: #218838; 
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
    .accepted-message {
        font-size: 22px; 
        font-weight: bold; 
        color: #28a745; 
        margin-top: 10px; 
    }
    .approved{
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
 

    
    </style>
</head>
<body>
    @if(!$trainee->traineeAgreement)
    <div class="container">
        @php
            $centerName=$trainee->company->center->name;
            $traineeName=$trainee->name;
            $identityNumber=$trainee->identity_number;
            $traineePhone=$trainee->phone;
            $traineeAdditionalPhone=$trainee->phone_additional;
            $traineeEmail=$trainee->email;

            if($trainee->company->center->name=='معهد جسر للتدريب'){
                $centerOrInstitute='المعهد';
                $commericalRegistrationNumber='1010973587';
            }elseif($trainee->company->center->name=='مركز جسارة'){
                $centerOrInstitute='المركز';
                $commericalRegistrationNumber='4030593971';

            }
        @endphp
    
    
        <h1>عقد تدريب متدرب</h1>
        

        <p>أنه في يوم الخميس الموافق: 1 / 8 / 2024 م تم الاتفاق بين كلاَ من:</p>
        <ul>
            <li><strong>الطرف الأول:</strong> 
            
                {{ $centerName }}

                ، سجل تجاري رقم ({{ $commericalRegistrationNumber }})</li>
            <li><strong>الطرف الثاني:</strong> « {{ $traineeName }} »، هوية وطنية رقم: «{{ $identityNumber }}»، جوال رقم: «{{ $traineePhone }}»</li>
            <li><strong>رقم الجوال البديل:</strong> «{{$traineeAdditionalPhone  }}»، البريد الالكتروني: «{{ $traineeEmail }}»</li>
        </ul>

        <p>بناءَ على رغبة الطرف الثاني بالحصول على عدد من الدورات التدريبية، فقد تم الاتفاق بين الطرفين على أن يقوم الطرف الأول بتقديم مجموعة من البرامج التدريبية وذلك حسب خبرته في هذا المجال وفق الترخيص المعتمد من قبل المؤسسة العامة للتدريب التقني و المهني  يعتبر هذا العقد موثقاً و سنداً تنفيذياً حال التعثر بالسداد حسب الجدول وقد يحدث تغيير في البرنامج التدريبي دون إشعار مسبق في حال حدوث طارئ أدناه .</p>

        <div class="section-title">البرامج التدريبية</div>
        <table class="course-table">
            <thead>
                <tr>
                    <th>م</th>
                    <th>الدورة التدريبية</th>
                </tr>
            </thead>
            @if ($trainee->company->center->name=='معهد جسر للتدريب')
                
            <tbody>
                <tr><td>1</td><td>المالية لغير الماليين</td></tr>
                <tr><td>2</td><td>إدارة مبيعات التجزئة</td></tr>
                <tr><td>3</td><td>مهارات الإشراف الإداري</td></tr>
                <tr><td>4</td><td>خدمة العملاء</td></tr>
                <tr><td>5</td><td>إدارة الموارد البشرية</td></tr>
                <tr><td>6</td><td>إدارة المشاريع الاحترافية</td></tr>
                <tr><td>7</td><td>إدارة الأزمات</td></tr>
                <tr><td>8</td><td>أساسيات الأعمال المكتبية</td></tr>
                <tr><td>9</td><td>تطوير مهارات أخصائي مبيعات</td></tr>
                <tr><td>10</td><td>القيادة الإدارية</td></tr>
                <tr><td>11</td><td>المسؤولية الإدارية والفعالية بالعمل</td></tr>
            </tbody>
            @elseif ($trainee->company->center->name=='مركز جسارة')
            <tbody>
                <tr><td>1</td><td>إدارة شؤون الموظفين والتطوير الإداري</td></tr>
                <tr><td>2</td><td>إدارة سلاسل الإمداد والخدمات اللوجستية</td></tr>
                <tr><td>3</td><td>Business English Skills</td></tr>
                <tr><td>4</td><td>إدارة الموارد البشرية والعقود</td></tr>
                <tr><td>5</td><td>إدارة وتخطيط العلاقات العامة</td></tr>
                <tr><td>6</td><td>مهارات أخصائي العلاقات العامة</td></tr>
                <tr><td>7</td><td>استراتيجيات الإدارة الحديثة</td></tr>
                <tr><td>8</td><td>الأمن السيبراني</td></tr>
                <tr><td>9</td><td>الإدارة الذكية للموارد البشرية</td></tr>
                <tr><td>10</td><td>المساعد الإداري وبناء فريق العمل</td></tr>
                <tr><td>11</td><td>تدريب مدربين</td></tr>
                <tr><td>11</td><td>كتابة التقارير والخطابات الإدارية</td></tr>
            </tbody>
            @endif


        </table>

        <div class="section-title">التزامات الطرف الأول ({{ $centerName }})</div>
        <ul>
            <li>1- تقديم كافة المستلزمات والوسائل والمتطلبات التدريبية الخاصة بالتدريب من قاعات التدريب (تدريب إلكتروني أو تدريب داخل مقر {{ $centerOrInstitute }}  )  ومدربات مؤهلات وأدوات التدريب المسموعة والمقروءة والمرئية , وذلك حسب متطلبات البرنامج التدريبي</li>
            <li>2- رفع التقارير بشكل شهري للمؤسسة العامة للتدريب التقني والمهنيو التي توضح التزام المتدربة بالدورة التدريبية و نسبة استيعاب المتدربة بناءً على نماذج تقييم المدربة</li>
            <li>3- بعد إنهاء المتدربة لمتطلبات البرنامج التدريبي بنجاح يحصل الطرف الثاني على شهادة حضور معتمدة من قبل المؤسسة العامة للتدريب التقني والمهني لكامل البرنامج التدريبي و ذلك عن طريق منصة منار</li>
        </ul>

        <div class="section-title">التزامات الطرف الثاني (المتدربة)</div>
        <ul>
            <li>1- المحافظة على مقتنيات {{ $centerOrInstitute }} والالتزام بجميع الأنظمة والتعليمات. </li>
            <li>2- التزام الطرف الثاني بالزي الرسمي والسلوكيات التربوية الإسلامية داخل {{ $centerOrInstitute }}.</li>
            <li>3- حضور الدورات حسب الجدول المعتمد من {{ $centerOrInstitute }} (حسب الوقت المحدد من قبل {{ $centerOrInstitute }}) مع العلم بأن الطرف الثاني يحرم  من الشهادة في حال عدم الالتزام بمواعيد الدورة التدريبية .</li>
            <li>4- التقيد باستخدام البصمة في الحضور والانصراف أو أي بيان يعتمده الطرف الأول .</li>
            <li>5- التقيد بالتعليمات الصادرة من {{ $centerOrInstitute }}  .</li>
            <li>6- تسديد الرسوم التدريبية مع نهاية كل شهر ميلادي وقدرها (2300) ألفان وثلاثمائة ريال فقط لا غير, اعتباراً من تاريخ العقد ولمدة اثنا عشر شهراً و يعتبر الطرف الثاني مسؤول مسؤولية كاملة عن سداد رسوم حصته من التدريب شهرياَ و يحرم الطرف الثاني من الشهادة  والاستفادة من البرنامج التدريبي وحظر الحساب من المنصة التدريبية في حال عدم سداد المستحقات المالية .
            </li>
            <li>7- في حال التعثر عن سداد رسوم التدريب المستحقة على الطرف الثاني لمدة أقصاها 30 يوم من تاريخ الاستحقاق و نظراً لكون العقد سنداً تنفيذياً واجب السداد يعتبر سداد كامل قيمة العقد للفترة المتبقية واجبة الاستحقاق و السداد مقدماً حتى تاريخ انتهاء العقد .
            </li>
            <li>8- سداد الرسوم التدريبية المتفق عليها أعلاه شهرياً حتى في حال الغياب عن حضور المحاضرات أو الرسوب بالمواد، كما لا يحق للطرف الثاني مطالبة {{ $centerOrInstitute }} باسترداد الرسوم التدريبية في أي حال من الأحوال.
            </li>
            <li>9- لا يحق للطرف الثاني مطالبة {{ $centerOrInstitute }} بتسليم أي شهادة تدريبية الا بعد تسديد جميع المستحقات المالية المترتبة عليه .
            </li>
            <li>10- الالتزام بالتعليمات الصادرة من {{ $centerOrInstitute }} بطرق التواصل المرفقة والتقيد بها.
            </li>
            <li>11- الالتزام بحضور الجلسة التدريبية كاملة سواء كانت حضورية ام عن بعد مع العلم بأن الحد الأقصى للغيابات بعذر هو 15 جلسة تدريبية و 3 جلسات بدون عذر مع مراعاة التواصل قبل موعد جلسة التدريب في القنوات المحددة والإشعار بعدم الحضور.    
            </li>
            <li>12- الالتزام باليوم التدريبي كامل وفي حال وجود خلل تقني لا يسمح بالخروج بعد مضي 30 دقيقة من جلسة التدريب علماً بأن تسجيل الدخول والحضور يتم إلكترونيا عبر النظام.
            </li>
            <li>13- الالتزام باحترام الأنظمة والقوانين المتبعة إلكترونياً وعدم الاستخدام السلبي للردود في القاعة التدريبية ويحق إلى {{ $centerOrInstitute }} مقاضاة المتجاوزين قانونيا.  
            </li>
        </ul>

        <div class="section-title">شروط العقد</div>
        <p>
            يسري هذا العقد لمدة 12 شهر ميلادية قابلة للتمديد تلقائيا باستكمال برامج التدريب كما يخضع هذا العقد من كافة جوانبه دون استثناء إلى القوانين والتشريعات السارية والمعمول بها في المملكة العربية السعودية , ويأخذ حيز التنفيذ كعقد داخل المملكة العربية السعودية
            </p>

        <div class="signatures">
            <div>
                <p>الطرف الأول</p>
                <div class="signature-line">{{ $centerName }} </div>
            </div>
            <div>
                <p>الطرف الثاني</p>
                <div class="signature-line">« {{ $traineeName }} »</div>
            </div>
        </div>
        
        <div class="button-group">

            <form action="{{ route('agreement.accept') }}" method="POST"  style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success">قبول</button>
            </form>
        
            <form  style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger">رفض</button>
            </form>
        </div>
    </div>



    @else
    <div class="approved">
        <div class="container ">
            <p class="accepted-message">   تم توثيق العقد الكترونيا والإعتماد بطريقة إلكترونية من خلال أبشر بتاريخ : {{$trainee->traineeAgreement->accepted_at}} </p> 
          </div>
    </div>
    
    @endif

</body>
</html>
