<?php

namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\modules\arenda\models\ElasticItems;
use common\widgets\FilterWidget;
use common\models\elastic\ItemsWidgetElastic;
use common\models\Seo;
use common\models\Filter;
use common\models\Pages;
use common\models\Slices;
use common\models\SlicesExtended;
use common\models\RestaurantsSpec;
use common\models\elastic\ItemsFilterElastic;
use frontend\components\PremiumMixer;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{

	public function actionIndex()
	{
		$elastic_model = new ElasticItems;
		$filter_model = Filter::find()->with('items')->all();
		$slices_model = Slices::find()->all();

		$seo = (new Seo('index'))->seo;
		$this->setSeo($seo);

		$filter = FilterWidget::widget([
			'filter_active' => [],
			'filter_model' => $filter_model
		]);

		$aggs = ElasticItems::find()->limit(0)->query(
			['bool' => ['must' => ['match' => ['restaurant_city_id' => Yii::$app->params['subdomen_id']]]]]
		)
			->addAggregate('specs', [
				'nested' => [
					'path' => 'restaurant_spec',
				],
				'aggs' => [
					'ids' => [
						'terms' => [
							'field' => 'restaurant_spec.id',
							'size' => 10000,
						]
					]
				]
			])->search();

		// echo ('<pre>');
		// print_r($aggs);
		// exit;

		$slicesForTag = array_reduce($aggs['aggregations']['specs']['ids']['buckets'], function ($acc, $item) {
			if (
				$item['doc_count'] > 3/* && count($acc) < 5*/
				&& ($restTypeSlice = RestaurantsSpec::find()->with('slice')->where(['id' => intval($item['key'])])->one())
				&& ($sliceObj = $restTypeSlice->slice)
			) {
				$order = $item['key'] + 100;
				switch ($sliceObj->alias) {
					case 'svadba':
						$order = 1;
						break;
					case 'den-rojdeniya':
						$order = 2;
						break;
					case 'novyy-god':
						$order = 3;
						break;
					case 'detskiy-den-rojdeniya':
						$order = 4;
						break;
					case 'korporativ':
						$order = 6;
						break;
					case 'vypusknoy':
						$order = 7;
						break;
				}

				$acc[] = [
					'alias' => $sliceObj->alias,
					'text' => $sliceObj->h1,
					'count' => $item['doc_count'],
					'order' => $order
				];
			}
			return $acc;
		}, []);

		$static_slices_for_tag = [
			[
				'alias' => 'gde-otmetit-8-marta',
				'text' => 'Отметить 🌼8 марта',
				'order' => 5
			],
			[
				'alias' => 'gde-otmetit-den-svyatogo-valentina',
				'text' => 'Отметить ❤️14 февраля',
				'order' => 8
			],
			[
				'alias' => 'gde-otmetit-23-fevralya',
				'text' => 'Отметить 🧦23 февраля',
				'order' => 9
			],
		];

		foreach ($static_slices_for_tag as $key => $value) {
			$slicesForTag[] = $value;
		}
		$slicesForTagForMobile = ArrayHelper::index($slicesForTag, 'order');
		ksort($slicesForTagForMobile);

		// echo ('<pre>');
		// print_r($slicesForTag);
		// exit;


		$slicesForListing = SlicesExtended::find()
			->where([
				'alias' => [
					'banketnyy-zal',
					'konferenc-zal',
					'restorany',
					'den-rojdeniya',
					'vypusknoy',
					'kafe',
					'svadba',
					'korporativ'
				]
			])->all();

		// echo '<pre>';
		// print_r($aggs);
		// exit;

		// $items = new ItemsFilterElastic([], 30, 1, false, 'rooms', $elastic_model);

		$items = PremiumMixer::getItemsWithPremium([], 30, 1, false, 'rooms', $elastic_model, false, false, false, false, false, true);

		$items = $items->items;


		$feature = false;

		$feature = $feature ? $feature : '';

		// echo ('<pre>');
		// print_r($similar_rooms);
		// exit;

		return $this->render('index.twig', [
			'filter' => $filter,
			'seo' => $seo,
			'slices_for_tag' => $slicesForTag,
			'slices_for_tag_for_mobile' => $slicesForTagForMobile,
			'slices_for_listing' => $slicesForListing,
			'items' => $items,
			'feature' => $feature,
		]);
	}

	public function actionError()
	{
		$slicesForListing = SlicesExtended::find()
			->where([
				'alias' => [
					'banketnyy-zal',
					'konferenc-zal',
					'restorany',
					'den-rojdeniya',
					'vypusknoy',
					'kafe',
					'svadba',
					'korporativ'
				]
			])->all();

		return $this->render('404.twig', [
			'slices_for_listing' => $slicesForListing,
		]);
	}

	public function actionRobots()
	{
		header('Content-type: text/plain');
		if (Yii::$app->params['subdomen_alias']) {
			$subdomen_alias = Yii::$app->params['subdomen_alias'] . '.';
		} else {
			$subdomen_alias = '';
		}
		echo 'User-agent: *
Disallow: *?*
Allow: /catalog/?page=
Allow: */css/
Allow: */js/
Disallow: */api/map_all/
Disallow:  /*/*.svg 
Disallow: *=w*
Disallow: *rest_type=
Disallow: *prazdnik=
Sitemap: https://' . $subdomen_alias . 'arendazala.net/sitemap/';
		exit;
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
