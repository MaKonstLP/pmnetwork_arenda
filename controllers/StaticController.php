<?php

namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use common\models\Pages;
use common\models\Seo;

class StaticController extends Controller
{

	public function actionPrivacy()
	{
		$page = Pages::find()
			->where([
				'type' => 'privacy',
			])
			->one();

		$seo = new Seo('privacy', 1);
		$this->setSeo($seo->seo);

		return $this->render('privacy.twig', [
			'page' => $page,
			'seo' => $seo->seo,
		]);
	}

	public function actionRequisites()
	{
		$page = Pages::find()
			->where([
				'type' => 'requisites',
			])
			->one();

		$seo = new Seo('requisites', 1);
		$this->setSeo($seo->seo);

		return $this->render('requisites.twig', [
			'page' => $page,
			'seo' => $seo->seo,
		]);
	}

	public function actionAdvertisement()
	{
		$page = Pages::find()
			->where([
				'type' => 'advertisement',
			])
			->one();

		$seo = new Seo('advertisement', 1);
		$this->setSeo($seo->seo);

		return $this->render('advertisement.twig', [
			'page' => $page,
			'seo' => $seo->seo,
		]);
	}

	public function actionAbout()
	{
		if (Yii::$app->params['subdomen_alias'] != '') {
			return $this->redirect('https://' . Yii::$app->params['subdomen_alias'] . '.arendazala.net/', 301);
		}

		$page = Pages::find()
			->where([
				'type' => 'about',
			])
			->one();

		$seo = new Seo('about', 1);
		$this->setSeo($seo->seo);

		return $this->render('about.twig', [
			'page' => $page,
			'seo' => $seo->seo,
		]);
	}

	public function actionRobots()
	{
		return 'User-agent: *
		Sitemap:  https://arenda.com/sitemap/  ';
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
