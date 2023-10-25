<?php

namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;
use common\components\GorkoLeadApi;
use common\components\TelegramBot;

class FormController extends Controller
{
	public function beforeAction($action)
	{
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionSend()
	{
		$payload = [];

		if (!isset($_POST['phone']) || !isset($_POST['cityID']))
			return 1;

		if (isset($_POST['name']))
			$payload['name'] = $_POST['name'];
		if (isset($_POST['phone']))
			$payload['phone'] = $_POST['phone'];
		if (isset($_POST['guests']))
			$payload['guests'] = intval($_POST['guests']);
		if (isset($_POST['cityID']))
			$payload['city_id'] = $_POST['cityID'];
		if (isset($_POST['event_type']))
			$payload['event_type'] = $_POST['event_type'];
		if (isset($_POST['date_hidden']))
			$payload['date'] = $_POST['date_hidden'];
		if (isset($_POST['venue_id']) && $_POST['venue_id'])
			$payload['venue_id'] = $_POST['venue_id'];
		if (isset($_POST['count']))
			$payload['email'] = $_POST['count'];
		$payload['details'] = '';
		if (isset($_POST['coment_text'])){
			$payload['details'] .= $_POST['coment_text'] . '. ';
			$payload['coment_text'] = $_POST['coment_text'];
		}
		if (isset($_POST['url']))
			$payload['details'] .= 'Заявка отправлена с ' . $_POST['url'];
		if (isset($_POST['restName']))
			$payload['details'] .= ', название ресторана: ' . $_POST['restName'];
		if (isset($_POST['restUrl']))
			$payload['details'] .= ', url ресторана: ' . $_POST['restUrl'];
		if (isset($_POST['type-connect']))
			$payload['details'] .= ' Предпочтительный тип связи: ' . $_POST['type-connect'];

		$resp = GorkoLeadApi::send_lead('v.gorko.ru', 'arendazala', $payload);

		if (isset($_POST['premium'])){
			$telegram_bot = new TelegramBot();
			$telegram_bot->roomCallback($payload, 2, $_POST['room_id']);
		}

		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $resp;
	}

	public function actionFeedback()
	{
		if (!isset($_POST['find']) || !isset($_POST['url']))
			return 1;

		$to = ['arendazala.net@yandex.ru'];
		$subj = 'Заявка с сайта';
		$msg  = "";
		$post_string_array = [
			'url'							=>	'Адрес страницы, с которой отправлена форма',
			'find'						=>	'Отмеченная радио-кнопка',
			'comment_text'				=>	'Комментарий',
			'comment_other_places'	=>	'Комментарий',
		];

		foreach ($post_string_array as $key => $value) {
			if (isset($_POST[$key]) && $_POST[$key] != '') {
				$msg .= $value . ': ' . $_POST[$key] . '<br/>';
			}
		}

		$message = $this->sendMail($to, $subj, $msg);

		if ($message) {
			$responseMsg = empty($responseMsg) ? 'Успешно отправлено!' : $responseMsg;
			$resp = [
				'error' => 0,
				'msg' => $responseMsg,
				// 'name' => isset($_POST['name']) ? $_POST['name'] : '',
				// 'phone' => $_POST['phone'],
			];
		} else {
			$resp = ['error' => 1, 'msg' => 'Ошибка']; //.serialize($_POST)
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $resp;
	}

	public function actionAdvertisement()
	{
		if (!isset($_POST['name']) || !isset($_POST['phone']))
			return 1;

		$to = ['info@arendazala.net', 'ab@liderpoiska.ru'];
		$subj = 'Заявка с сайта';
		$msg  = "";
		$post_string_array = [
			'name'							=>	'Имя и фамилия',
			'position'						=>	'Должность',
			'phone'							=>	'Телефон',
			'email'							=>	'Электропочта',
			'rest_name'						=>	'Название площадки',
			'city'							=>	'Город',
		];

		foreach ($post_string_array as $key => $value) {
			if (isset($_POST[$key]) && $_POST[$key] != '') {
				$msg .= $value . ': ' . $_POST[$key] . '<br/>';
				$payload[$key] = $_POST[$key];
			}
		}

		$message = $this->sendMail($to, $subj, $msg);

		if ($message) {
			$responseMsg = empty($responseMsg) ? 'Успешно отправлено!' : $responseMsg;
			$resp = [
				'error' => 0,
				'msg' => $responseMsg,
				'payload' => $payload,
			];
		} else {
			$resp = ['error' => 1, 'msg' => 'Ошибка']; //.serialize($_POST)
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $resp;
	}

	public function sendMail($to, $subj, $msg)
	{
		$message = Yii::$app->mailer->compose()
			->setFrom(['info@arendazala.net' => 'Аренда залов'])
			->setTo($to)
			->setSubject($subj)
			->setCharset('utf-8')
			//->setTextBody('Plain text content')
			->setHtmlBody($msg);

		return $message->send();
	}
}
