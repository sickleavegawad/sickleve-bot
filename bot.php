<?php

$token = "AAG8pAjEWeZ9cRM3RdZ8-R3rq24FHhRCEiI";
$api = "https://api.telegram.org/bot$token/";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(isset($update[ message ])){

$chat_id = $update[ message ][ chat ][ id ];
$text = trim($update[ message ][ text ]);

$parts = explode("|",$text);

if(count($parts) < 3){

sendMessage($chat_id,
"ارسل البيانات بهذا الشكل:

الاسم|الهوية|المدة

مثال:
محمد احمد|1234567890|3");

exit;
}

$name = trim($parts[0]);
$idno = trim($parts[1]);
$days = trim($parts[2]);

$save_url = "https://اسم-موقعك.42web.io/save_leave.php";

$post = [
 name_ar =>$name,
 national_id =>$idno,
 leave_duration =>$days
];

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,$save_url);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

$response = curl_exec($ch);

curl_close($ch);

$report_id = trim($response);

$pdf = "https://اسم-موقعك.42web.io/leavereport.php?id=$report_id";

sendMessage($chat_id,"جاري تجهيز التقرير...");

sendDocument($chat_id,$pdf);

}

function sendMessage($chat_id,$text){

global $api;

file_get_contents(
$api."sendMessage?chat_id=$chat_id&text=".urlencode($text)
);

}
function sendDocument($chat_id,$document){

global $api;

$post = [
    'chat_id' => $chat_id,
    'document' => $document,
    'caption' => 'تقرير الاجازة المرضية'
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $api . "sendDocument");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_exec($ch);

curl_close($ch);

}

?>
