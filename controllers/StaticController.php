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

		// echo '<pre>';
		// print_r($seo);
		// exit;

		return $this->render('privacy.twig', [
			'page' => $page,
			'seo' => $seo->seo,
		]);
	}

	public function actionRobots()
	{
		return 'User-agent: *
		Sitemap:  https://arenda.com/sitemap/  ';
	}

	private function setSeo($seo){
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}