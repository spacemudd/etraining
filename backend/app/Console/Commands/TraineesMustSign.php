<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Back\Trainee; 

class TraineesMustSign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainees:must-sign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'assign must_sign field to true';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identityIds = [
            '1045501150',
            '1045619580',
            '1094519459',
            '1119061255',
            '1118384880',
            '1079775498',
            '1109298792',
            '1094189741',
            '1115220681',
            '1114414483',
            '1126452232',
            '1099412965',
            '1044671715',
            '1081911032',
            '١٠٦٧٥٣٣٦٧٧',
            '1106157520',
            '1078754767',
            '1130862186',
            '1142458262',
            '1094824206',
            '1117723278',
            '1104582851',
            '1092628005',
            '1120560675',
            '1115636043',
            '1085760823',
            '1101076550',
            '1037646302',
            '1046221592',
            '1113608028',
            '١١٠٩٥٩٧٣٥٩',
            '1075040772',
            '1086140033',
            '1074554815',
            '1117510170',
            '1090659929',
            '1070418486',
            '1120806656',
            '1108866771',
            '1087953590',
            '1068633112',
            '1098475278',
            '1106540030',
            '1093204392',
            '1114128562',
            '1117884906',
            '1059490969',
            '1098899998',
            '1111174445',
            '1097059560',
            '1121120768',
            '1071303224',
            '1106987488',
            '1092559911',
            '1096338163',
            '1051561882',
            '1098389750',
            '1094036561',
            '1123591578',
            '1084419330',
            '1035860491',
            '1120894439',
            '1035819265',
            '1034847002',
            '1097936957',
            '1080214875',
            '1075427367',
            '1111931216',
            '1120892581',
            '1000888287',
            '1133711463',
            '1110099536',
            '1131907113',
            '١١٠٣٩٥٥٥٠٤',
            '1067218402',
            '1051277000',
            '1083661270',
            '1123767244',
            '1076459187',
            '1094969548',
            '1101101168',
            '1022476111',
            '1069777256',
            '1105313488',
            '1049917287',
            '1060057906',
            '1115473512',
            '1100070919',
            '1081791186',
            '1100221785',
            '1114318460',
            '1131569830',
            '1067684207',
            '1085792123',
            '1127377701',
            '1101702122',
            '1092522877',
            '1090497064',
            '1142499852',
            '1105446932',
            '1067320331',
            '1130926643',
            '1070544984',
            '1078351143',
            '1066875517',
            '1114907767',
            '1075274371',
            '1122122417',
            '1107785238',
            '1074850866',
            '1122670845',
            '1098740838',
            '1122772575',
            '1080534165',
            '1090327998',
            '1099819912',
            '1059357432',
            '1084431657',
            '1101036869',
            '1104185515',
            '1094523477',
            '1081605857',
            '1112323090',
            '1079575179',
            '1081981845',
            '1080242256',
            '1076055910',
            '1084896800',
            '1025816412',
            '1109850774',
            '1059853646',
            '1106199498',
            '1106594953',
            '1119538104',
            '1124361153',
            '1107357301',
            '1120376270',
            '1120806649',
            '1082421718',
            '1125696631',
            '1092784550',
            '1072503582',
            '1096857394',
            '1065638684',
            '1098879214',
            '1102672209',
            '1121307795',
            '1117639078',
            '1120020191',
            '١٠٤٩٥٩٨٧٠٧',
            '1116504794',
            '1066967926',
            '1119215521',
            '1093525507',
            '1090432483',
            '1109746691',
            '1127018834',
            '1124610864',
            '1087890354',
            '1017026434',
            '1059909844',
            '1116953181',
            '1118393949',
            '1109513422',
            '1105087322',
            '1118199551',
            '1108804095',
            '1090122142',
            '1090786045',
            '1125128486',
            '1096966641',
            '1102280896',
            '1131485391',
            '1118616562',
            '1099115881',
            '١١١٤٣٧٠١١٥',
            '1137813638',
            '1011307210',
            '1059840270',
            '1091043289',
            '1111195770',
            '1106040817',
            '1054231939',
            '1113659666',
            '1095636765',
            '1095670640',
            '1083553048',
            '1084534641',
            '١٠٦٨١٧٠٤٣٨',
            '1092470325',
            '1081855163',
            '1055154247',
            '1083302859',
            '1023425919',
            '1083056984',
            '1114226267',
            '1088954662',
            '1187745185',
            '1095999296',
            '1068400231',
            '1108735307',
            '1056057365',
            '1106089160',
            '1091740462',
            '1105415739',
            '1113433732',
            '1091887479',
            '1126266186',
            '١٠٦٧٧٩٢٣٠٧',
            '1021586126',
            '1120073646',
            '1064452798',
            '1110572854',
            '1132117019',
            '1093811659',
            '1084456753',
            '1116254747',
            '1096537756',
            '1113574535',
            '1122992207',
            '1072364381',
            '1077479796',
            '1116796549',
            '1092918711',
            '1089182347',
            '1099384461',
            '1127570958',
            '1070767080',
            '1130696188',
            '1100609807',
            '1095618854',
            '1086813647',
            '1068196292',
            '1120587694',
            '1094485784',
            '1118402500',
            '1116763127',
            '1095701023',
            '1078968581',
            '1084219375',
            '1041256676',
            '1105999716',
            '1093569026',
            '1109864072',
            '1072284464',
            '1133155489',
            '1127456703',
            '1085495040',
            '1091582443',
            '1085171526',
            '1133045706',
            '1126083920',
            '1125883080',
            '1089715542',
            '1083294965',
            '1086920616',
            '1096996127',
            '1087318810',
            '1129501324',
            '1091400190',
            '1085193058',
            '1045121405',
            '1068455649',
            '1097150237',
            '1106616954',
            '1105681561',
            '1100314093',
            '1120407364',
            '1112673353',
            '1108609619',
            '1114915471',
            '1010098752',
            '1062045529',
            '1074878248',
            '1087975700',
            '1114948100',
            '1118044559',
            '1111073951',
            '1129368757',
            '1056951633',
            '1124276021',
            '1046554281',
            '1087959522',
            '1092637311',
            '1071542292',
            '1070674302',
            '1077709424',
            '1110648027',
            '1126060571',
            '1100335247',
            '1132895929',
            '1110884473',
            '1115771220',
            '1096015480',
            '1213812728',
            '1092514361',
            '1121228405',
            '1127656187',
            '1121228397',
            '1058715838',
            '١٠٢٨٢٩٢٦٠٣',
            '1136776331',
            '1125593325',
            '1092849270',
            '1086288022',
            '1098920927',
            '1073775965',
            '1125705234',
            '1060080767',
            '1097372179',
            '1088930829',
            '1062231285',
            '١٠٠٢٤٣٢٢٤١',
            '1123372409',
            '1091268191',
            '1089091282',
            '1130193749',
            '1076675923',
            '1128547534',
            '1102634456',
            '1135009312',
            '1057209445',
            '1108271378',
            '1091318376',
            '1091327161',
            '1137522106',
            '1135373635',
            '1116932102',
            '1114422296',
            '1136992086',
            '1074711373',
            '1076296928',
            '1059001626',
            '1111338024',
            '1087323299',
            '1097460560',
            '1068540648',
            '1125581551',
            '1026579886',
            '1037113105',
            '1073288381',
            '1076143211',
            '1075920999',
            '1077245460',
            '1045276464',
            '1135648952',
            '1122215997',
            '1057171561',
            '1099882910',
            '1098704610',
            '1055940694',
            '١٠٨٣٨٤٣٢٦٦',
            '1100728367',
            '1092605243',
            '1096872096',
            '1075658979',
            '1082265123',
            '1085132767',
            '1113311714',
            '1095185672',
            '1208545770',
            '١١١٠٥٣٤٥٨١',
            '1070290562',
            '1092696697',
            '1070560782',
            '1043213469',
            '1118067063',
            '1091982049',
            '1044693677',
            '1090969013',
            '1099039032',
            '1096525314',
            '1103214811',
            '1089546343',
            '1103708119',
            '1075191369',
            '1041880236',
            '1057313338',
            '1107269910',
            '1087637524',
            '1105786444',
            '1107588426',
            '1104264831',
            '1108426832',
            '1111546485',
            '1087280374',
            '1029562160',
            '1016085431',
            '1036292025',
            '1132121516',
            '1036742607',
            '1075446292',
            '1091974202',
            '1075867174',
            '1105205510',
            '1066835867',
            '1071756488',
            '1087830699',
            '1064154386',
            '1107417428',
            '1076375904',
            '1089182321',
            '1077208658',
            '١١٠١٦٧٠٣٩٤',
            '1074276989',
            '1054202492',
            '1123636076',
            '1097231474',
            '1063470262',
            '1064711060',
            '1028194510',
            '١٠٥٨٥٦٠٩١١',
            '1109663755',
            '1086058136',
            '1086029723',
            '1088958960',
            '1023278433',
            '١٠٨٦١٥١٤٤٤',
            '1101998001',
            '1075923142',
            '1054988553',
            '1087362685',
            '1100278678',
            '1100466703',
            '1035022670',
            '1060316740',
            '1085200317',
            '1111472153',
            '1125771855',
            '1065629923',
            '١٠٣٤٥٩٠٦٣٦',
            '1098806332',
            '1114781428',
            '1068821972',
            '1095894778',
            '1122748435',
            '1066709286',
            '1122567645',
            '1045271341',
            '1097987893',
            '1073463299',
            '1087919161',
            '1115288340',
            '1127581666',
            '1100853199',
            '1105078990',
            '1065146563',
            '1128824446',
            '1091667384',
            '1103236624',
            '1128464680',
            '1111973499',
            '1089888000',
            '1133311041',
            '1127387817',
            '1092256450',
            '1073958504',
            '1086180252',
            '1097194896',
            '1082652726',
            '1090745546',
            '1120442627',
            '1110380480',
            '1086943428',
            '1126306453',
            '1004203129',
            '1044983219',
            '1085592796',
            '1089743882',
            '1083317683',
            '1126589744',
            '1084939675',
            '1088196348',
            '1108130087',
            '1089534794',
            '1061936579',
            '1066148576',
            '1125417707',
            '١١٠٢٦٢٠٦٤٦',
            '1097203903',
            '1009661412',
            '1104158157',
            '1124618834',
            '1075765246',
            '1084830890',
            '1097134454',
            '1129974836',
            '1134833977',
            '1095757686',
            '1085116547',
            '1123860148',
            '1089461238',
            '1079181614',
            '1110473269',
            '1104708894',
            '1108934819',
            '1073286716',
            '1131523480',
            '1109469799',
            '1075224871',
            '1047825854',
            '1093232468',
            '1123215772',
            '1129354286',
            '1006117509',
            '1115099747',
            '١٠٧٥٦٦٩٨٢٨',
            '1121209124',
            '1091539443',
            '1088719594',
            '1002424388',
            '1099269290',
            '1079313647',
            '1086423702',
            '1066617596',
            '١٠٨٣١٩٣٥٤٨',
            '1132014448',
            '1113785305',
            '1080216219',
            '1085850665',
            '1121645343',
            '1077980868',
            '1114157256',
            '1120774235',
            '1113745002',
            '1117397404',
            '1103019772',
            '1118228863',
            '1098159989',
            '1048560799',
            '1085859849',
            '1058204924',
            '1070429996',
            '1077817581',
            '1100925773',
            '1130902685',
            '1106364662',
            '1099472258',
            '1093033296',
            '1103770093',
            '1024845404',
            '1096474703',
            '1060700729',
            '1001585080',
            '1020567416',
            '1066651595',
            '1090177443',
            '1090852466',
            '1090842327',
            '1100424850',
            '1088341076',
            '1082496827',
            '1072374778',
            '1122647306',
            '1071976326',
            '1014074551',
            '1122295627',
            '1039423304',
            '١٠٩١٨٨١١٩١',
            '1029787486',
            '1127374674',
            '1101834156',
            '1093562757',
            '1058065036',
            '1085793089',
            '1119717484',
            '١٠٦٩٠٠٨٤٤٧',
            '1097815821',
            '1109774727',
            '1095648570',
            '1050206778',
            '1070459951',
            '1087103253',
            '1126173432',
            '1113403909',
            '1089524308',
            '1108944180',
            '1094067160',
            '1111961858',
            '1122918160',
            '1123678771',
            '1090691898',
            '1058780899',
            '1088347578',
            '1020634984',
            '1117416303',
            '1114085523',
            '1097727117',
            '1119262259',
            '1110112834',
            '١٠٠١٩٤٩٨١٥',
            '1059828721',
            '1049374836',
            '1096998917',
        ];

        $updatedCount = Trainee::whereIn('identity_number', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");



    }
}
