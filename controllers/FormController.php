<?php
namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;

class FormController extends Controller
{
    //public function getViewPath()
    //{
    //    return Yii::getAlias('@app/modules/svadbanaprirode/views/site');
    //}

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSend()
    {
        if ($_POST['type'] == 'main'){
            // $messageApi = $this->sendApi($_POST['name'], $_POST['phone'], $_POST['date'], $_POST['count']);
            //return json_encode($messageApi);
        } else {
            // $messageApi = $this->sendApi($_POST['name'], $_POST['phone'], $_POST['date'], $_POST['count']);
        }

        //$to   = ['martynov@liderpoiska.ru', 'sharapova@liderpoiska.ru', 'sites@plusmedia.ru'];
        $to   = ['artm@liderpoiska.ru'];

        if ($_POST['type'] == 'main'){
            $subj = "Заявка на выбор зала.";
        } else {
            $subj = "Заявка на бронирование зала.";
        }
        
        $msg  = "";

        // $post_string_array = [
        //     'name'  => 'Имя',
            // 'phone' => 'Телефон',
            // 'date'  => 'Дата',
            // 'count' => 'Количество гостей',
            // 'url'   => 'Страница отправки' 
        // ];

        // $post_checkbox_array = [
        //     'water'  => 'у воды',
        //     'tent' => 'с шатром',
        //     'country'  => 'за городом',
        //     'incity' => 'в черте города',
        // ];

        // foreach ($post_string_array as $key => $value) {
        //     if(isset($_POST[$key]) && $_POST[$key] != ''){
        //         $msg .= $value.': '.$_POST[$key].'<br/>';
        //     }
        // }   
        
        // if($_POST['type'] == 'main'){
        //     $checkbox_msg = '';
            // foreach ($post_checkbox_array as $key => $value) {
            //     if(isset($_POST[$key]) && $_POST[$key] != ''){
            //         $checkbox_msg .= $value.', ';
            //     }
            // }
            // if($checkbox_msg != '')
            //     $msg .= 'Зал должен быть: <br/>'.$checkbox_msg;
        // }
        

        $message = $this->sendMail($to,$subj,$msg);
        if ($message) {
            $responseMsg = empty($responseMsg) ? 'Успешно отправлено!' : $responseMsg;
            $resp = [
                'error' => 0,
                'msg' => $responseMsg,
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
            ];              
        } else {
            $resp = ['error'=>1, 'msg'=>'Ошибка'];//.serialize($_POST)
        }       
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }

    public function sendMail($to, $subj, $msg)
    {
        return true;
        $message = Yii::$app->mailer->compose()
            ->setFrom(['impl-stom@yandex.ru' => 'Аренда зала'])
            ->setTo('artm@liderpoiska.ru')
            ->setSubject($subj)
            ->setCharset('utf-8')
            //->setTextBody('Plain text content')
            ->setHtmlBody($msg.'.');
        if (count($_FILES) > 0) {
            foreach ($_FILES['files']['tmp_name'] as $k => $v) {
                $message->attach($v, ['fileName' => $_FILES['files']['name'][$k]]);
            }
        }
        return $message->send();
    }

    public function sendApi($name, $phone, $date, $count) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/venues/all/request?model_type=restaurants&model_id=1&city_id=4400');
        $payload = json_encode([
            "name"      => $name,
            "phone"     => $phone,
            "date"      => $date,
            "guests"    => $count,
            'budget' => null,
            'details' => null,
            'drinks' => null,
            'event_type' => "1",
            'food' => null,
            'line' => null,
            'page_type' => null,
            'telegram' => null,
            'viaLine' => null,
            'viaPhone' => 1,
            'viaTelegram' => null,
            'viaViber' => null,
            'viaWhatsApp' => null,
            'viber' => null,
            'whatsapp' => null,
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);



        return $response;
    }
}
