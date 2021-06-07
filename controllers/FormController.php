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
        //return [ 'payload' => $payload];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://v.gorko.ru/api/arendazala/inquiry/put');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $log = file_get_contents('/var/www/pmnetwork/log/arendazala.log');
        $log = json_decode($log, true);
        $log[time()] = [
            'response' => $response,
            'info' => $info,
            'payload' => $payload,
        ];
        $log = json_encode($log);
        file_put_contents('/var/www/pmnetwork/log/arendazala.log', $log);
        
        return [
            'response' => $response,
            'info' => $info,
            'payload' => $payload,
        ];
    }
}
