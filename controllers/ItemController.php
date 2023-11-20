<?php

namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Rooms;
use common\models\Seo;
use common\models\elastic\ItemsWidgetElastic;
use common\models\elastic\ItemsFilterElastic;
use app\modules\arenda\models\ItemSpecials;
use frontend\components\Declension;
use frontend\modules\arenda\models\ElasticItems;
use frontend\modules\arenda\components\Breadcrumbs;

class ItemController extends Controller
{
	public function actionIndex($slug)
	{
		$elastic_model = new ElasticItems;

		$item = ElasticItems::find()->query([
			'bool' => [
				'must' => [
					['match' => ['slug' => $slug]],
					['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
			]
		])->one();

		if (empty($item)) {
			throw new NotFoundHttpException();
		}

		$review_tags_str = $item->restaurant_review_tags;
        $review_tags_arr = [];

        if ($review_tags_str != '') {
            $review_tags_items = explode('||', $review_tags_str);
            foreach ($review_tags_items as $review_tags_item) {
                $parts = explode(':', $review_tags_item);
                $review_tags_arr[$parts[0]] = (int)$parts[1];
            }
        }

//      echo '<pre>';
//		print_r($review_tags_arr);
//		die;

//расчет новой цены
//        if($item['restaurant_payment_model'] == 0) {
//            $price_person = $item['restaurant_price'];
//            $rent_room_only = 0;
//        } elseif ($item['restaurant_payment_model'] == 1) {
//            $price_person = $item['banquet_price_person'];
//            $rent_room_only = 0;
//        } elseif ($item['restaurant_payment_model'] == 2) {
//            $price_person = $item['restaurant_price'];
//            $rent_room_only = $item['rent_room_only'];
//        } elseif ($item['restaurant_payment_model'] == 3) {
//            $price_person = 0;
//            $rent_room_only = $item['rent_room_only'];
//        }

//        echo '<pre>';
//        print_r($price_person);
//        die();

		$itemHaveSpecPrice = false;
		foreach ($item['room_prices'] as $room_price) {
			if (!empty($room_price['price']) && $room_price['price'] != $item['price']) {
				$itemHaveSpecPrice = true;
			}
		}

		// $seo = new Seo('item', 1, 0, $item);
		/* $seo = new Seo('item', 1,0, $item, 'room', null, $min_price = false, false, false, false, $max_price = false);
		$seo = $seo->seo;
		$this->setSeo($seo);

		$seo['h1'] = [0 => $item->name, 1 => $item->restaurant_name];
		$seo['breadcrumbs'] = Breadcrumbs::getItemCrumb($item);
		$seo['address'] = $item->restaurant_address;
		$seo['desc'] = $item->restaurant_name; */

		$changedStrings = ItemSpecials::getChangedStrings($item);

		$special_obj = new ItemSpecials($item->restaurant_special);
		$item->restaurant_special = $special_obj->special_arr;

		if ($item->restaurant_parking > 0) {
			$parking = $item->restaurant_parking . ' ' . Declension::get_num_ending($item->restaurant_parking, array('машина', 'машины', 'машин'));
		} else {
			$parking = null;
		}

		// $itemsWidget = new ItemsWidgetElastic;
		// $other_rooms = $itemsWidget->getOther($item->restaurant_id, $item->id, $elastic_model);
		if (isset($_COOKIE['prazdnik_spec_id'])) {
			$other_rooms = ElasticItems::find()->query([
				'bool' => [
					'must' => [
						['match' => ['restaurant_id' => $item->restaurant_id]],
						['nested' => ['path' => 'room_spec', 'query' => ['bool' => ['must' => ['match' => ['room_spec.id' => $_COOKIE['prazdnik_spec_id']]]]]]],
					],
					'must_not' => [
						['match' => ['slug' => $slug]],
					],

				]
			])->all();
		} else {
			$other_rooms = ElasticItems::find()->query([
				'bool' => [
					'must' => [
						['match' => ['restaurant_id' => $item->restaurant_id]],
					],
					'must_not' => [
						['match' => ['slug' => $slug]],
					],

				]
			])->all();
		}

		$similar_rooms = ElasticItems::find()->limit(50)->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_district' => $item->restaurant_district]],
					['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
				'must_not' => [
					['match' => ['restaurant_id' => $item->restaurant_id]]
				],
			],
		])->all();

		// устанавливаем свойства в зависимости от выбранного типа мероприятия в фильтре
		if (isset($_COOKIE['prazdnik_id'])) {
			//устанавливаем цену для основного зала
			$price_for_prazdnik = '';
			foreach ($item['room_prices'] as $room_price) {
				if ($room_price['prazdnik_id'] == $_COOKIE['prazdnik_id']) {
					$price_for_prazdnik = $room_price['price'];
					$prazdnik_name = $room_price['spec_name'];
					break;
				}
			}
			if (isset($price_for_prazdnik) && !empty($price_for_prazdnik)) {
				$item['price'] = $price_for_prazdnik;
			}

			//устанавливаем цену для других залов
			foreach ($other_rooms as $room) {
				foreach ($room['room_prices'] as $room_price) {
					if ($room_price['prazdnik_id'] == $_COOKIE['prazdnik_id']) {
						$price_room_for_prazdnik = $room_price['price'];
						break;
					}
				}
				if (isset($price_room_for_prazdnik) && !empty($price_room_for_prazdnik)) {
					$room['price'] = $price_room_for_prazdnik;
				}
			}

			// фильтруем похожие залы по типу мероприятия
			foreach ($similar_rooms as $key => $similar_room) {
				$is_relevant_room = false;
				foreach ($similar_room['restaurant_spec'] as $similar_room_spec) {
					if ($similar_room_spec['id'] == $_COOKIE['prazdnik_spec_id']) {
						$is_relevant_room = true;
						break;
					}
				}
				if (!$is_relevant_room) {
					$not_relevant_rooms[] = $key;
				}
			}

			if (isset($not_relevant_rooms) && !empty($not_relevant_rooms)) {
				foreach ($not_relevant_rooms as $not_relevant_room) {
					// если залов с подходящим типом мероприятия меньше 6, то добиваем до 6 другими залами
					if (count($similar_rooms) > 6) {
						unset($similar_rooms[$not_relevant_room]);
					} else {
						break;
					}
				}
			}

			//устанавливаем цену для похожих залов
			foreach ($similar_rooms as $room) {
				foreach ($room['room_prices'] as $room_price) {
					if ($room_price['prazdnik_id'] == $_COOKIE['prazdnik_id']) {
						$price_room_for_prazdnik = $room_price['price'];
						break;
					}
				}
				if (isset($price_room_for_prazdnik) && !empty($price_room_for_prazdnik)) {
					$room['price'] = $price_room_for_prazdnik;
				}
			}


			//устанавливаем ссылки на тип заведения в зависимости от выбранного типа мероприятия
			foreach ($item['room_spec'] as $room_spec) {
				if ($room_spec['prazdnik_id'] == $_COOKIE['prazdnik_id'] && isset($room_spec['types']) && !empty($room_spec['types'])) {
					$item['restaurant_types'] = $room_spec['types'];
					break;
				}
			}
		}


		// Получаем доступ к свойству объекта, содержащему массив
// $array = $object->getArray();

// // Удаляем элемент массива
// unset($array[$key]);

// // Сохраняем изменения
// $object->setArray($array);

		foreach ($item['room_spec'] as $key => $room_spec) {
			if (!isset($room_spec['link'])) {
				// $array = $item['room_spec']->getArray();
				// unset($array[$key]);
				// $item['room_spec']->setArray($array);
				// $element =& $item['room_spec'][$key];
				// unset($element);
				// unset($item['room_spec'][$key]);
			}
		}

		shuffle($similar_rooms);
		$similar_rooms = array_slice($similar_rooms, 0, 6);


		$test = ElasticItems::find()->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_district' => $item->restaurant_district]],
					['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
			],
		])->all();

		if ($item->restaurant_premium) Yii::$app->params['premium_rest'] = true;
		Yii::$app->params['rest_gorko_id'] = $item['restaurant_gorko_id'];
		Yii::$app->params['room_gorko_id'] = $item['gorko_id'];

		// ===== schemaOrg Product START =====
		$json_str = '';
		$json_str .= '{
				"@context": "https://schema.org",
				"@type": [
					"Apartment",
					"Product"
				],
				"name": "' . $item->name . ' ' . $item->restaurant_name . '"';

		// if ($max_price) {
		if ($item->max_room_price) {
			$json_str .= ',';
			$json_str .= '
				"offers": {
					"@type": "AggregateOffer",
					"priceCurrency": "RUB",
					"highPrice": "' . $item->max_room_price . '",
					"lowPrice": "' . $item->min_room_price . '"
				}';
		}

		if (isset($item->restaurant_rev_ya['count']) && $item->restaurant_rev_ya['count'] && $item->restaurant_rev_ya['rate']) {
			$json_str .= ',';
			$json_str .= '
				"aggregateRating": {
					"@type": "AggregateRating",
					"bestRating": "5",
					"reviewCount": "' . preg_replace('/[^0-9]/', '', $item->restaurant_rev_ya['count']) . '",
					"ratingValue": "' . $item->restaurant_rev_ya['rate'] . '"
				}';
		}
		$json_str .= '}';

		Yii::$app->params['schema_product'] = $json_str;
		// ===== schemaOrg Product END =====


		$seo = new Seo('item', 1,0, $item, 'room', null, $item->min_room_price, false, false, false, $item->max_room_price);
		$seo = $seo->seo;
		$this->setSeo($seo);

		$seo['h1'] = [0 => $item->name, 1 => $item->restaurant_name];
		$seo['breadcrumbs'] = Breadcrumbs::getItemCrumb($item);
		$seo['address'] = $item->restaurant_address;
		$seo['desc'] = $item->restaurant_name;

        $showReviewTags = false;
        $countShowTags = 0;
        if (!empty($review_tags_arr)) {
            foreach ($review_tags_arr as $tag) {
                if ($tag > 70) {
                    $showReviewTags = true;
                    $countShowTags++;
                }
            }
        }

        $review_tags_arr['isShow'] = $showReviewTags;
        $review_tags_arr['countShow'] = $countShowTags;

		// echo ('<pre>');
		// print_r($item);
		// exit;

		return $this->render('index.twig', array(
			'item' => $item,
			'queue_id' => $item->id,
			'seo' => $seo,
			'changedStrings' => $changedStrings,
			'parking' => $parking,
			'other_rooms' => $other_rooms,
			'similar_rooms' => $similar_rooms,
			'prazdnik_name' => $prazdnik_name ?? '',
			'itemHaveSpecPrice' => $itemHaveSpecPrice,
            'review_tags_arr' => $review_tags_arr,
//            'price_person' => $price_person,
//            'rent_room_only' => $rent_room_only,

		));
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
