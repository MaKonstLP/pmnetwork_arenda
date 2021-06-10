<?php
namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;
use common\models\elastic\LeadLogElastic;

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
        $payload = [];

        if(isset($_POST['name']))
            $payload['name'] = $_POST['name'];
        if(isset($_POST['phone']))
            $payload['phone'] = $_POST['phone'];
        if(isset($_POST['cityID']))
            $payload['city_id'] = $_POST['cityID'];
        if(isset($_POST['date_hidden']))
            $payload['date'] = $_POST['date_hidden'];
        if(isset($_POST['count']))
            $payload['email'] = $_POST['count'];
        $payload['details'] = '';
        if(isset($_POST['coment_text']))
            $payload['details'] .= $_POST['coment_text'].'';
        if(isset($_POST['url']))
            $payload['details'] .= 'Заявка отправлена с '.$_POST['url'];

        $resp = $this->sendApi($payload);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }

    public function sendApi($payload) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://v.gorko.ru/api/arendazala/inquiry/put');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        $response = json_decode(curl_exec($curl), true);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $log_arr = [];
        $log_arr['source']      = 'arendazala';
        $log_arr['payload']     = isset($response['payload']) ? json_encode($response['payload']) : 'Нет $response[payload]';
        $log_arr['raw_payload'] = json_encode($payload);
        $log_arr['response']    = json_encode($response);
        $log_arr['timestamp']   = time();
        $log_arr['code']        = isset($response['result']) ? json_encode($response['result']) : 'Нет $response[result]';
        $log_arr['name']        = isset($payload['name']) ? $payload['name'] : 'Нет имени';
        $log_arr['phone']       = isset($payload['phone']) ? json_encode($payload['phone']) : 'Нет телефона';
        $log_arr['city_id']     = isset($payload['city_id']) ? json_encode($payload['city_id']) : 'Нет city_id';

        $leadLog = new LeadLogElastic();
        $leadLog::addRecord($log_arr);
        
        return [
            'response' => $response,
            'info' => $info,
            'payload' => $payload,
        ];
    }
}
