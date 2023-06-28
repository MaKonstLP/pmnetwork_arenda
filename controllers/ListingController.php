<?php

namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\widgets\FilterWidget;
use frontend\widgets\PaginationWidget;
use frontend\modules\arenda\widgets\LoadmoreWidget;
use frontend\components\ParamsFromQuery;
// use frontend\components\QueryFromSlice;
use frontend\modules\arenda\components\QueryFromSlice;
use frontend\modules\arenda\components\Breadcrumbs;
use frontend\components\Declension;
use common\models\ItemsFilter;
use common\models\elastic\ItemsFilterElastic;
use frontend\components\PremiumMixer;
use frontend\modules\arenda\models\ElasticItems;
use backend\models\Filter;
use backend\models\Slices;
use common\models\GorkoApi;
use common\models\GorkoApiTest;
use common\models\Seo;
use backend\modules\arenda\models\blog\BlogPost;
use backend\modules\arenda\models\blog\BlogPostSlice;

class ListingController extends Controller
{
	protected $per_page = 30;

	public $filter_model,
		$slices_model,
		$mapPageExistList,
		$url;

	public function beforeAction($action)
	{
		// $this->filter_model = Filter::find()->with('items')->all();
		$this->filter_model = Filter::find()->with('items')->where(['active' => 1])->orderBy(['sort' => SORT_ASC])->all();
		$this->slices_model = Slices::find()->all();
		$this->mapPageExistList = array('kafe', 'loft', 'restorany', 'banketnyy-zal');

		return parent::beforeAction($action);
	}

	public function actionSlice($slice)
	{
		$slice_obj = new QueryFromSlice($slice);

		if ($slice_obj->flag) {
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, $this->filter_model, $this->slices_model);

			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];

			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			if ($params['page'] > 1) {
				// $canonical .= $params['canonical'];
			}

			$slices_top = !empty($slice_obj->slices_top) ? $slice_obj->slices_top : '';

			return $this->actionListing(
				$page 				=	$params['page'],
				$per_page			=	$this->per_page,
				$params_filter		=	$params['params_filter'],
				$breadcrumbs 		=	Breadcrumbs::get_breadcrumbs(2, $slice),
				$canonical 			=	$canonical,
				$type 				=	$slice,
				$sliceOnMapFlag	=	false,
				$feature				=	$slice_obj->seo['feature'],
				$slices_top			=	$slices_top,
				$static_tags		=	$params['static_tags'],
				$slice_id			=	$slice_obj->slice_model['id'],
			);
		} else {

			$item = ElasticItems::find()->query([
				'bool' => [
					'must' => [
						['match' => ['slug' => $slice]],
						['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
					],
				]
			])->one();

			if (empty($item)) {
				throw new \yii\web\NotFoundHttpException();
			} else {
				return $this->redirect(array('item/index', $slice));
			}
		}
	}

	public function actionSliceOnMap($slice)
	{
		$slice_obj = new QueryFromSlice($slice);

		if ($slice_obj->flag) {
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, $this->filter_model, $this->slices_model);

			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];

			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2, $slice),
				$canonical 		= 	$canonical,
				$type 			=	$slice,
				$sliceOnMapFlag = true,
			);
		} else {
			throw new \yii\web\NotFoundHttpException();
		}
	}

	public function actionIndex()
	{
		$getQuery = $_GET;
		unset($getQuery['q']);

		if (count($getQuery) > 0) {
			$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			if ($params['page'] > 1) {
				$canonical .= '?' . $params['canonical'];
			}

			substr($params['listing_url'], 0, 1) == '?' ?
				$breadcrumbs = Breadcrumbs::get_breadcrumbs(4, false, $params['params_filter'])
				: $breadcrumbs = Breadcrumbs::get_breadcrumbs(2, substr($params['listing_url'], 0, -1));

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	$breadcrumbs,
				$canonical 		= 	$canonical,
				$type 			=	false,
				$sliceOnMapFlag = false,
				$feature			=	false,
				$slices_top		=	false,
				$static_tags	=	$params['static_tags'],
			);
		} else {
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			return $this->actionListing(
				$page 			=	1,
				$per_page		=	$this->per_page,
				$params_filter	= 	[],
				$breadcrumbs 	= 	Breadcrumbs::get_breadcrumbs(1),
				$canonical 		= 	$canonical,
			);
		}
	}

	public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false, $sliceOnMapFlag = false, $feature = false, $slices_top = [], $static_tags = [], $slice_id = false)
	{
		$elastic_model = new ElasticItems;
		$items = PremiumMixer::getItemsWithPremium($params_filter, $per_page, $page, false, 'rooms', $elastic_model, false, false, false, false, false, true);
		//$items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'rooms', $elastic_model);

		if ($items->total == 0) {
			Yii::$app->params['noindex_global'] = true;
		}

		// вывод дополнительных свойств реста в заисимости от выбранного типа помещения или мероприятия
		$prazdnik_option_id = '';
		$rest_type_option_id = '';
		if (isset($params_filter['prazdnik']) && !empty($params_filter['prazdnik'])) {
			$prazdnik_option_id = $params_filter['prazdnik'][0];
		} else if (isset($params_filter['rest_type']) && !empty($params_filter['rest_type'])) {
			if (
				count($params_filter['rest_type']) == 1
				&& ($params_filter['rest_type'][0] == 7
					|| $params_filter['rest_type'][0] == 8
					|| $params_filter['rest_type'][0] == 9
					|| $params_filter['rest_type'][0] == 6
					|| $params_filter['rest_type'][0] == 21)
			) {
				$rest_type_option_id = $params_filter['rest_type'][0];
			} else {
				$rest_type_option_id = 99;
			}
		}

		//облако тегов
		$tags_list = [];
		if ($slices_top) {
			$tags_list = $this->getCloudTagsList($slices_top, $elastic_model, true);
		} else if ($static_tags) {
			$tags_list = $this->getCloudTagsList($static_tags, $elastic_model, false);
		}

		$itemsHaveSpecPrice = array();
		foreach ($items->items as $item) {
			$itemsHaveSpecPrice[$item['id']] = false;
			foreach ($item['room_prices'] as $room_price) {
				if (!empty($room_price['price']) && $room_price['price'] != $item['price']) {
					$itemsHaveSpecPrice[$item['id']] = true;
				}
			}
		}
		$prazdnik_type = '';
		if (isset($params_filter['prazdnik']) && !empty($params_filter['prazdnik'])) {
			switch ($params_filter['prazdnik'][0]) {
				case 1:
					$this->view->params['prazdnik_type'] = '_birthday';
					$prazdnik_type = '_birthday';
					break;
				case 2:
					$this->view->params['prazdnik_type'] = '_baby_birthday';
					$prazdnik_type = '_baby_birthday';
					break;
				case 3:
					$this->view->params['prazdnik_type'] = '_wedding';
					$prazdnik_type = '_wedding';
					break;
				case 4:
					$this->view->params['prazdnik_type'] = '_new-year';
					$prazdnik_type = '_new-year';
					break;
				case 5:
					$this->view->params['prazdnik_type'] = '_corporate';
					$prazdnik_type = '_corporate';
					break;
				case 6:
					$this->view->params['prazdnik_type'] = '_graduation';
					$prazdnik_type = '_graduation';
					break;
			}

			// устанавливаем цены в зависимости от выбранного типа мероприятия
			setcookie('prazdnik_id', $params_filter['prazdnik'][0], time() + 1800, '/'); //добавляем куку с id праздника - используется на странице зала
			//соотвествие id в таблице filter_items и restaurants_spec
			$prazdnik_arr = [
				'1' => '9', //День рождения
				'2' => '12', //Детский день рождения
				'3' => '1', //Свадьба
				'4' => '17', //Новый год
				'5' => '15', //Корпоратив
				'6' => '11', //Выпускной
			];
			setcookie('prazdnik_spec_id', $prazdnik_arr[$params_filter['prazdnik'][0]], time() + 1800, '/'); //добавляем куку с spec_id праздника - используется на странице зала

			foreach ($items->items as $item) {
				foreach ($item['room_prices'] as $room_price) {
					if ($room_price['prazdnik_id'] == $params_filter['prazdnik'][0]) {
						$price_for_prazdnik = $room_price['price'];
						break;
					}
				}
				if (isset($price_for_prazdnik) && !empty($price_for_prazdnik)) {
					$item['price'] = $price_for_prazdnik;
				}
			}
		} else {
			if (isset($_COOKIE['prazdnik_id'])) {
				setcookie('prazdnik_id', '', time() - 1, '/'); // удаляем куку
				setcookie('prazdnik_spec_id', '', time() - 1, '/'); // удаляем куку
			}
		}

		$filter = FilterWidget::widget([
			'filter_active' => $params_filter,
			'filter_model' => $this->filter_model
		]);

		$pagination = PaginationWidget::widget([
			'total' => $items->pages,
			'current' => $page,
		]);
		$seo_type = $type ? $type : 'listing';

		$seo = null;

		if (!$sliceOnMapFlag && !$type) {
			$seo = $this->getSeo($seo_type, $page, $items->total);
		} else if (!$type) {
			$seo = $this->getSeo($seo_type . "/map", $page, $items->total);
		}

		$prettyPhone = Yii::$app->params['subdomen_phone'];

		if ($prettyPhone !== '') {
			$prettyPhone = substr($prettyPhone, 0, 2)
				. ' ('
				. substr($prettyPhone, 2, 3)
				. ') '
				. substr($prettyPhone, 5, 3)
				. '-'
				. substr($prettyPhone, 8, 2)
				. '-'
				. substr($prettyPhone, 10, 2);
		} else {
			$prettyPhone = '';
		}

		$totalCount = $items->total
			. ' заведени'
			. Declension::get_num_ending($items->total, ['е', 'я', 'й']);

		$feature = $feature ? $feature : '';

		$loadMore = LoadmoreWidget::widget([
			'total' => $items->total,
			'current_page' => $page,
			'current' => $page * $per_page,
			'per_page' => $per_page,
		]);

		// ===== schemaOrg Product START =====
		$min_price = 99999;
		$max_price = 0;
		$review_count = 0;
		$total_rating = 0;
		$rest_with_rating = 0;
		$average_rating = 0;
		if ($type) {
			foreach ($items->items as $item) {
				if (isset($item['price']) && !empty($item['price'])) {
					if ($item['price'] < $min_price) {
						$min_price = $item['price'];
					}
					if ($item['price'] > $max_price) {
						$max_price = $item['price'];
					}
				}

				if (isset($item['restaurant_rev_ya']['count']) && !empty($item['restaurant_rev_ya']['count'])) {
					$review_count += preg_replace('/[^0-9]/', '', $item['restaurant_rev_ya']['count']);
				}

				if (isset($item['restaurant_rev_ya']['rate']) && !empty($item['restaurant_rev_ya']['rate'])) {
					$total_rating += $item['restaurant_rev_ya']['rate'];
					$rest_with_rating += 1;
				}
			}
			if ($total_rating != 0) {
				$average_rating = round($total_rating / $rest_with_rating, 1);
			}

			if (!$sliceOnMapFlag) {
				$seo = $this->getSeo($seo_type, $page, $items->total, $min_price, $max_price);
			} else {
				$seo = $this->getSeo($seo_type . "/map", $page, $items->total, $min_price, $max_price);
			}

			$json_str = '';
			$json_str .= '{
				"@context": "https://schema.org",
				"@type": [
					"Apartment",
					"Product"
				],
				"name": "' . $seo['h1'] . '",
				"description": "' . $seo['description'] . '"';

			if ($max_price) {
				$json_str .= ',';
				$json_str .= '
				"offers": {
					"@type": "AggregateOffer",
					"offerCount": "' . $items->total . '",
					"priceCurrency": "RUB",
					"highPrice": "' . $max_price . '",
					"lowPrice": "' . $min_price . '"
				}';
			}

			if ($review_count && $average_rating) {
				$json_str .= ',';
				$json_str .= '
				"aggregateRating": {
					"@type": "AggregateRating",
					"bestRating": "5",
					"reviewCount": "' . $review_count . '",
					"ratingValue": "' . $average_rating . '"
				}';
			}
			$json_str .= '}';

			Yii::$app->params['schema_product'] = $json_str;

			// ===== schemaOrg SaleEvent START =====
			$current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			Yii::$app->params['sale_event_first'] = '{
				"@context": "https://schema.org",
				"@type": "SaleEvent",
				"name": "🔥 Большой каталог",
				"startDate": "2022-12-26T03:00:00+00:00",
				"endDate": "2023-12-26T03:00:00+00:00",
				"url": "' . $current_url . '",
				"location": {
					"@type": "Place",
					"name": "arendazala.net",
					"address": {
						"@type": "PostalAddress",
						"addressLocality": "' . Yii::$app->params['subdomen_name'] . '"
					}
				},
				"about": "🔥 Большой каталог",
				"performer": "arendazala.net",
				"organizer": "arendazala.net",
				"offers": {
					"@type": "Offer",
					"price":"' . $min_price . '",
					"priceCurrency":"RUB",
					"availability":"https://schema.org/InStock",
					"url":"' . $current_url . '",
					"validFrom":"2022-12-26T03:00:00+00:00"
				}
			}';
			Yii::$app->params['sale_event_second'] = '{
				"@context": "https://schema.org",
				"@type": "SaleEvent",
				"name": "🎉 Лучшие места",
				"startDate": "2022-12-26T03:00:00+00:00",
				"endDate": "2023-12-26T03:00:00+00:00",
				"url": "' . $current_url . '",
				"location": {
					"@type": "Place",
					"name": "arendazala.net",
					"address": {
						"@type": "PostalAddress",
						"addressLocality": "' . Yii::$app->params['subdomen_name'] . '"
					}
				},
				"about": "🎉 Лучшие места",
				"performer": "arendazala.net",
				"organizer": "arendazala.net",
				"offers": {
					"@type": "Offer",
					"price":"' . $min_price . '",
					"priceCurrency":"RUB",
					"availability":"https://schema.org/InStock",
					"url":"' . $current_url . '",
					"validFrom":"2022-12-26T03:00:00+00:00"
				}
			}';
			Yii::$app->params['sale_event_third'] = '{
				"@context": "https://schema.org",
				"@type": "SaleEvent",
				"name": "💯 Проверенные арендодатели",
				"startDate": "2022-12-26T03:00:00+00:00",
				"endDate": "2023-12-26T03:00:00+00:00",
				"url": "' . $current_url . '",
				"location": {
					"@type": "Place",
					"name": "arendazala.net",
					"address": {
						"@type": "PostalAddress",
						"addressLocality": "' . Yii::$app->params['subdomen_name'] . '"
					}
				},
				"about": "💯 Проверенные арендодатели",
				"performer": "arendazala.net",
				"organizer": "arendazala.net",
				"offers": {
					"@type": "Offer",
					"price":"' . $min_price . '",
					"priceCurrency":"RUB",
					"availability":"https://schema.org/InStock",
					"url":"' . $current_url . '",
					"validFrom":"2022-12-26T03:00:00+00:00"
				}
			}';
			// ===== schemaOrg SaleEvent END =====
		}
		// ===== schemaOrg Product END =====

		// ===== вывод на срезах "Подборок ресторанов" START =====
		$collection_posts = '';
		if ($type) {
			$collection_posts = BlogPost::findWithMedia()
				->with('blogPostTags')
				->joinWith('blogPostSlices')
				->where(['published' => true])
				// ->andWhere(['collection' => true])
				->andWhere([BlogPostSlice::tableName() . '.slice_id' => $slice_id])
				->andWhere([BlogPostSlice::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
				->all();
		}
		// ===== вывод на срезах "Подборок ресторанов" END =====

		$seo['breadcrumbs'] = $breadcrumbs;
		$this->setSeo($seo, $page, $canonical, $items->items);

		if ($seo_type == 'listing' and count($params_filter) > 0) {
			$seo['text_top'] = '';
			$seo['text_bottom'] = '';
		}


		// echo ('<pre>');
		// print_r($items->items);
		// exit;

		return $this->render('index.twig', array(
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'count' => $items->total,
			'totalCount' => $totalCount,
			'phone' => Yii::$app->params['subdomen_phone'],
			'prettyPhone' => $prettyPhone,
			'sliceOnMapFlag' => $sliceOnMapFlag,
			'sliceAlias' => $type,
			'mapPageExistFlag' => in_array($type, $this->mapPageExistList),
			'feature' => $feature,
			'loadMore' => $loadMore,
			'itemsHaveSpecPrice' => $itemsHaveSpecPrice,
			'prazdnik_type' => $prazdnik_type,
			// 'slices_top' => $slices_top,
			// 'static_tags' => $static_tags,
			'tags_list' => $tags_list,
			'prazdnik_option_id' => $prazdnik_option_id,
			'rest_type_option_id' => $rest_type_option_id,
			'collection_posts' => $collection_posts,
		));
	}

	public function actionAjaxFilter()
	{
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

		$elastic_model = new ElasticItems;
		$items = PremiumMixer::getItemsWithPremium($params['params_filter'], $this->per_page, $params['page'], false, 'rooms', $elastic_model, false, false, false, false, false, true);
		//$items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'rooms', $elastic_model);


		// вывод дополнительных свойств реста в заисимости от выбранного типа помещения или мероприятия
		$prazdnik_option_id = '';
		$rest_type_option_id = '';
		if (isset($params['params_filter']['prazdnik']) && !empty($params['params_filter']['prazdnik'])) {
			$prazdnik_option_id = $params['params_filter']['prazdnik'][0];
		} else if (isset($params['params_filter']['rest_type']) && !empty($params['params_filter']['rest_type'])) {
			if (
				count($params['params_filter']['rest_type']) == 1
				&& ($params['params_filter']['rest_type'][0] == 7
					|| $params['params_filter']['rest_type'][0] == 8
					|| $params['params_filter']['rest_type'][0] == 9
					|| $params['params_filter']['rest_type'][0] == 6
					|| $params['params_filter']['rest_type'][0] == 21)
			) {
				$rest_type_option_id = $params['params_filter']['rest_type'][0];
			} else {
				$rest_type_option_id = 99;
			}
		}


		$itemsHaveSpecPrice = array();
		foreach ($items->items as $item) {
			$itemsHaveSpecPrice[$item['id']] = false;
			foreach ($item['room_prices'] as $room_price) {
				if (!empty($room_price['price']) && $room_price['price'] != $item['price']) {
					$itemsHaveSpecPrice[$item['id']] = true;
				}
			}
		}
		$prazdnik_type = '';
		if (isset($params['params_filter']['prazdnik']) && !empty($params['params_filter']['prazdnik'])) {
			switch ($params['params_filter']['prazdnik'][0]) {
				case 1:
					$this->view->params['prazdnik_type'] = '_birthday';
					$prazdnik_type = '_birthday';
					break;
				case 2:
					$this->view->params['prazdnik_type'] = '_baby_birthday';
					$prazdnik_type = '_baby_birthday';
					break;
				case 3:
					$this->view->params['prazdnik_type'] = '_wedding';
					$prazdnik_type = '_wedding';
					break;
				case 4:
					$this->view->params['prazdnik_type'] = '_new-year';
					$prazdnik_type = '_new-year';
					break;
				case 5:
					$this->view->params['prazdnik_type'] = '_corporate';
					$prazdnik_type = '_corporate';
					break;
				case 6:
					$this->view->params['prazdnik_type'] = '_graduation';
					$prazdnik_type = '_graduation';
					break;
			}

			// устанавливаем цены в зависимости от выбранного типа мероприятия
			foreach ($items->items as $item) {
				foreach ($item['room_prices'] as $room_price) {
					if (in_array($room_price['prazdnik_id'], $params['params_filter']['prazdnik'])) {
						$price_for_prazdnik = $room_price['price'];
					}
				}
				if (isset($price_for_prazdnik) && !empty($price_for_prazdnik)) {
					$item['price'] = $price_for_prazdnik;
				}
			}
		}

		$pagination = PaginationWidget::widget([
			'total' => $items->pages,
			'current' => $params['page'],
		]);

		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		$seo_type = $slice_url ? $slice_url : 'listing';
		// $seo = $this->getSeo($seo_type, $params['page'], $items->total);

		//вывод меток "Особенности" у залов на листинге
		$feature = '';

		//облако тегов
		$tags_list = [];
		$min_price = 99999;
		$max_price = 0;
		$collection_posts = '';
		// $static_tags = $params['static_tags'];
		if ($slice_url) {
			$slice_obj = new QueryFromSlice($slice_url);
			$slice_id = $slice_obj->slice_model['id'];

			$feature = $slice_obj->seo['feature'];

			if (!empty($slice_obj->slices_top)) {
				$tags_list = $this->getCloudTagsList($slice_obj->slices_top, $elastic_model, $slices_top = false);
			}

			//получение максимальной и минимальной цены
			foreach ($items->items as $item) {
				if (isset($item['price']) && !empty($item['price'])) {
					if ($item['price'] < $min_price) {
						$min_price = $item['price'];
					}
					if ($item['price'] > $max_price) {
						$max_price = $item['price'];
					}
				}
			}

			// ===== вывод на срезах "Подборок ресторанов" START =====
			$collection_posts = BlogPost::findWithMedia()
				->with('blogPostTags')
				->joinWith('blogPostSlices')
				->where(['published' => true])
				->andWhere([BlogPostSlice::tableName() . '.slice_id' => $slice_id])
				->andWhere([BlogPostSlice::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
				->all();
			// ===== вывод на срезах "Подборок ресторанов" END =====

			$seo = $this->getSeo($seo_type, $params['page'], $items->total, $min_price, $max_price);
		} else {
			$seo = $this->getSeo($seo_type, $params['page'], $items->total);
		}

		if (!$tags_list && $params['static_tags']) {
			$tags_list = $this->getCloudTagsList($params['static_tags'], $elastic_model, $slices_top = false);
		}
		$tags = $this->renderPartial('//components/generic/slices_top.twig', array('tags_top' => $tags_list));


		substr($params['listing_url'], 0, 1) == '?' ?
			$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(4, false, $params['params_filter'])
			: $seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(2, substr($params['listing_url'], 0, -1));

		$totalCount = $items->total
			. ' заведени'
			. Declension::get_num_ending($items->total, ['е', 'я', 'й']);

		$crumbs = $this->renderPartial('//components/generic/breadcrumbs.twig', array(
			'seo' => $seo,
			'count' => $totalCount,
		));

		$title = $this->renderPartial('//components/generic/title.twig', array(
			'seo' => $seo,
			'count' => $totalCount,
		));

		if ($params['page'] == 1) {
			$text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_top']));
			$text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_bottom']));
		} else {
			$text_top = '';
			$text_bottom = '';
		}

		if ($seo_type == 'listing' and count($params['params_filter']) > 0) {
			$text_top = '';
			$text_bottom = '';
		}

		$loadMore = LoadmoreWidget::widget([
			'total' => $items->total,
			'current_page' => $params['page'],
			'current' => $params['page'] * $this->per_page,
			'per_page' => $this->per_page,
		]);

		return json_encode([
			'listing' => $this->renderPartial('//components/generic/listing.twig', array(
				'items' => $items->items,
				'img_alt' => $seo['img_alt'],
				'itemsHaveSpecPrice' => $itemsHaveSpecPrice,
				'prazdnik_type' => $prazdnik_type,
				'page' => 'listing',
				'feature' => $feature,
				'prazdnik_option_id' => $prazdnik_option_id,
				'rest_type_option_id' => $rest_type_option_id,
			)),
			// 'collection_posts' => $collection_posts,
			'collection_posts' => $collection_posts ? $this->renderPartial('//components/generic/listing_collections.twig', array('collection_posts' => $collection_posts)) : '',
			'pagination' => $pagination,
			'url' => $params['listing_url'],
			'title' => $title,
			'crumbs' => $crumbs,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
			'seo_title' => $seo['title'],
			'params_filter' => $params['params_filter'],
			'mapPageExistFlag' => in_array(str_replace('/', '', $params['listing_url']), $this->mapPageExistList),
			'prazdnik_type' => $prazdnik_type,
			'loadMore' => $loadMore,
			'slices_top' => $slices_top,
			'tags' => $tags,
		]);
	}

	public function actionAjaxFilterSlice()
	{
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
	}

	/* получения списка облака тегов */
	private function getCloudTagsList($tags_arr, $elastic_model, $slices_top = false)
	{

		if ($slices_top) { //если в админке у среза заданы теги, то список формируется из них
			foreach ($tags_arr as $key => $slice) {
				$slice_obj_for_tag = new QueryFromSlice($slice['alias']);
				$params_for_tag = $this->parseGetQuery($slice_obj_for_tag->params, $this->filter_model, $this->slices_model);
				$items_for_tag = PremiumMixer::getItemsWithPremium($params_for_tag['params_filter'], $this->per_page, 1, false, 'rooms', $elastic_model, false, false, false, false, false, true);
				$tags_arr[$key]['count'] = $items_for_tag->total;
			}
		} else { //если теги у среза не заданы, то выводится "статичный" список тегов
			foreach ($tags_arr as $key => $static_tag) {
				if (isset($static_tag['alias']) && !empty($static_tag['alias'])) {
					$slice_obj_for_tag = new QueryFromSlice($static_tag['alias']);
					$params_for_tag = $this->parseGetQuery($slice_obj_for_tag->params, $this->filter_model, $this->slices_model);
				} else {
					parse_str(str_replace('?', '', $static_tag['url']), $query_arr);
					$params_for_tag = $this->parseGetQuery($query_arr, $this->filter_model, $this->slices_model);
				}
				$items_for_tag = PremiumMixer::getItemsWithPremium($params_for_tag['params_filter'], $this->per_page, 1, false, 'rooms', $elastic_model, false, false, false, false, false, true);
				if ($items_for_tag->total === 0) {
					unset($tags_arr[$key]);
				} else {
					$tags_arr[$key]['count'] = $items_for_tag->total;
				}
			}
		}

		return $tags_arr;
	}

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if (isset($getQuery['page'])) {
			$return['page'] = $getQuery['page'];
		} else {
			$return['page'] = 1;
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);

		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['canonical'] = $temp_params->canonical;


		//добавление статичных тегов для облака тегов
		$prazdnik_arr = [
			'Недорогие места' => 'price=1',
			'Дорогие места' => 'price=5',
			'Лофты' => 'rest_type=11',
			'Рестораны' => 'rest_type=13',
			'Кафе' => 'rest_type=14',
			'Со своим алкоголем' => 'alko=1',
			'Банкетные залы' => 'rest_type=5',
			'Небольшая компания' => 'chelovek=1',
			'До 20 человек' => 'chelovek=2',
			'До 30 человек' => 'chelovek=3',
			'До 100 человек' => 'chelovek=8',
			'На природе' => 'rest_type=18,19,20,21',
		];

		$types_arr = [
			'Недорогие места' => 'price=1',
			'Дорогие места' => 'price=5',
			'Со своим алкоголем' => 'alko=1',
			'Небольшая компания' => 'chelovek=1',
			'До 20 человек' => 'chelovek=2',
			'До 30 человек' => 'chelovek=3',
			'До 100 человек' => 'chelovek=8',
		];

		$static_tags = [];
		if (isset($temp_params->params_filter) && !empty($temp_params->params_filter)) {
			if (isset($temp_params->params_filter['prazdnik']) && !empty($temp_params->params_filter['prazdnik'])) {
				$i = 0;
				foreach ($prazdnik_arr as $key => $type) {
					$type_parts = explode('=', $type);
					$static_tag_query = [
						'prazdnik' => $temp_params->params_filter['prazdnik'][0],
						$type_parts[0] => $type_parts[1],
					];
					$slice_url = ParamsFromQuery::isSlice($static_tag_query);

					if ($slice_url) {
						$static_tags[$i]['alias'] = $slice_url;
					}
					$static_tags[$i]['url'] = '?prazdnik=' . $temp_params->params_filter['prazdnik'][0] . '&' . $type;
					$static_tags[$i]['h1'] = $key;
					$i++;
				}
			} elseif (isset($temp_params->params_filter['rest_type']) && !empty($temp_params->params_filter['rest_type'])) {
				$i = 0;
				foreach ($types_arr as $key => $type) {
					$type_parts = explode('=', $type);
					$static_tag_query = [
						'rest_type' => $temp_params->params_filter['rest_type'][0],
						$type_parts[0] => $type_parts[1],
					];
					$slice_url = ParamsFromQuery::isSlice($static_tag_query);

					if ($slice_url) {
						$static_tags[$i]['alias'] = $slice_url;
					}
					$static_tags[$i]['url'] = '?rest_type=' . $temp_params->params_filter['rest_type'][0] . '&' . $type;
					$static_tags[$i]['h1'] = $key;
					$i++;
				}
			}
		}

		$return['static_tags'] = $static_tags;

		return $return;
	}

	private function getSeo($type, $page, $count = 0, $min_price = false, $max_price = false)
	{
		// $seo = new Seo($type, $page, $count);
		$seo = new Seo($type, $page, $count, false, 'room', null, $min_price, false, false, false, $max_price);

		return $seo->seo;
	}

	private function setSeo($seo, $page, $canonical, $items)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
		$this->view->params['robots'] = false;
		$this->view->params['robots_2'] = false;

		if ($page != 1) {
			$this->view->params['canonical'] = $canonical;
			$this->view->params['robots'] = true;
		}

		if (count($items) === 0) {
			$this->view->params['robots_2'] = true;
		}
	}
}
