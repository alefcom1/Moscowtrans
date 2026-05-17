<?php
/*
 * Template name: get_acts
 */
function send_telegram($token,$chat_id, $mes)
{
if( $curl = curl_init() ) {
  curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$chat_id.'&text='.urlencode($mes));
curl_setopt($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, '');
    $out = curl_exec($curl);
    $viv=$out;
    curl_close($curl);
}
return true;
}
function telegram_case($email)
{
$viv = '';
if($email == 'krasnodar1@remarka.biz')
{
$viv = array("token" => '5845388073:AAF2qnyMvwCF0VKxawQkJsWUXsndNXUdHSs', "chat_id" => '-1001910595486');
}
elseif($email == 'krasnodar2@remarka.biz')
{
$viv = array("token" => '5845388073:AAF2qnyMvwCF0VKxawQkJsWUXsndNXUdHSs', "chat_id" => '-957820430');
}
elseif($email == 'krasnodar3@remarka.biz')
{
$viv = array("token" => '5845388073:AAF2qnyMvwCF0VKxawQkJsWUXsndNXUdHSs', "chat_id" => '-1001824556961');
}
return $viv;
}
session_start();

if(!isset($_POST['act']))
{
die('bad request');
}
$act = $_POST['act'];

if($act == 'form1')
{
$fl = true;
$error_arr = array();
$nam = $_POST['your-name'];
$eml = $_POST['your-email'];
$tel = $_POST['tel'];
$office = $_POST['office'];
$perevod_na = $_POST['perevd'];


if($nam == '')
{
$fl = false;
$error_arr[]= array("inner" => 'your_name_error_area', "val" => 'Недопустимое значение');
}
$eml_fl = filter_var($eml, FILTER_VALIDATE_EMAIL);
if($eml == '' || $eml_fl == false)
{
$fl = false;
$error_arr[]= array("inner" => 'your_email_error_area', "val" => 'Недопустимое значение');
}
if($tel == '')
{
$fl = false;
$error_arr[]= array("inner" => 'tel_error_area', "val" => 'Недопустимое значение');
}
if($office == '')
{
$fl = false;
$error_arr[]= array("inner" => 'office_error_area', "val" => 'Недопустимое значение');
}
if($_FILES['file-103']['name'] == '')
{
$fl = false;
$error_arr[]= array("inner" => 'file-103_error_area', "val" => 'Недопустимое значение');
}
if($perevod_na == '')
{
$fl = false;
$error_arr[]= array("inner" => 'perevd_error_area', "val" => 'Недопустимое значение');
}
if($_POST['capcha'] == '' || $_POST['capcha'] != $_SESSION['1_rand_code'])
{
$fl = false;
$error_arr[]= array("inner" => 'capcha_error_area', "val" => 'Неправильный код');
}





if($fl == true)
{
	
$total_files = count($_FILES['file-103']['name']);
$file_link_cont_for_eml = '';
$file_link_cont_for_tlg = '';
$schet = 1;
for($key = 0; $key < $total_files; $key++) {
$schet = $schet + $key;
if(isset($_FILES['file-103']['name'][$key])&&$_FILES['file-103']['size'][$key] > 0) {	
$original_filename = $_FILES['file-103']['name'][$key];
$ext = strtolower(pathinfo($_FILES["file-103"]["name"][$key], PATHINFO_EXTENSION));
$original_filename=time().rand(1,10).rand(1,10).rand(1,10).'.'.$ext;
$target = $_SERVER['DOCUMENT_ROOT'].'/perevod_loads/'.$original_filename;
$tmp  = $_FILES['file-103']['tmp_name'][$key];
move_uploaded_file($tmp, $target);
$file_link_cont_for_eml .= '<a href="https://xn--e1aa2af.xn--p1ai/perevod_loads/'.$original_filename.'">Документ №'.$schet.'</a><br>';
$file_link_cont_for_tlg .= 'https://xn--e1aa2af.xn--p1ai/perevod_loads/'.$original_filename.' '."\r\n";
}
}
$fh = fopen($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/perevod/prim.txt', "w");
fwrite($fh, $file_link_cont);
fclose($fh);
$tip = 'succes';
$content = 'Ваше сообщение успешно отправлено!!!';
$mes = 'Имя: '.$nam.'<br>Email: '.$eml.'<br>Телефон: '.$tel.'<br>Перевод на '.$perevod_na.'<br>'.$file_link_cont_for_eml;
$telegram_mes = 'Запрос стоимости перевода Имя: '.$nam.' Email: '.$eml.' Телефон: '.$tel.' Перевод на '.$perevod_na.' '.$file_link_cont_for_tlg;
$headers = array(
	'From: Ремарка <admin@remarka.biz>',
	'content-type: text/html'
);
$telegram = telegram_case($office);
send_telegram($telegram["token"],$telegram["chat_id"], $telegram_mes);
wp_mail( $office, 'Запрос стоимости перевода', $mes, $headers );

}
elseif($fl == false)
{
$tip = 'error';
$content = $error_arr;
}
$data = array("tip" => $tip, "content" => $content);
echo '<script>parent.form1_controller('."'".json_encode($data)."'".')</script>';

}
elseif($act == 'form2')
{
$fl = true;
$error_arr = array();
$nam = $_POST['your-name'];
$tel = $_POST['tel'];
$office = $_POST['office'];
if($nam == '')
{
$fl = false;
$error_arr[]= array("inner" => 'frm2_your_name_error_area', "val" => 'Недопустимое значение');
}
if($tel == '')
{
$fl = false;
$error_arr[]= array("inner" => 'frm2_tel_error_area', "val" => 'Недопустимое значение');
}
if($office == '')
{
$fl = false;
$error_arr[]= array("inner" => 'frm2_office_error_area', "val" => 'Недопустимое значение');
}
if($_POST['capcha'] == '' || $_POST['capcha'] != $_SESSION['2_rand_code'])
{
$fl = false;
$error_arr[]= array("inner" => 'frm2_capcha_error_area', "val" => 'Неправильный код');
}

if($fl == true)
{
$tip = 'succes';
$telegram = telegram_case($office);
$telegram_mes = 'Запрос обратного звонка Имя: '.$nam.' Телефон: '.$tel;
//$tm = file_get_contents('https://xn--e1aa2af.xn--p1ai/perevod_loads/ame.php?token='.urlencode($telegram["token"]).'&chat_id='.urlencode($telegram["chat_id"]).'&mes='.urlencode($telegram_mes));
$content = 'Ваше сообщение успешно отправлено!!!';
$mes = 'Имя: '.$nam.'<br>Телефон: '.$tel.'<br>';
$headers = array(
	'From: Ремарка <admin@remarka.biz>',
	'content-type: text/html'
);

$str = send_telegram($telegram["token"],$telegram["chat_id"], $telegram_mes);
wp_mail( $office, 'Запрос обратного звонка', $mes, $headers );
}
elseif($fl == false)
{
$tip = 'error';
$content = $error_arr;
}
$data = array("tip" => $tip, "content" => $content);
echo '<script>parent.form2_controller('."'".json_encode($data)."'".')</script>';
}
 ?>