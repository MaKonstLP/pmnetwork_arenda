<?php

namespace app\modules\arenda\controllers;

use Yii;
use backend\models\Filter;
use common\models\GorkoApiTest;
use common\models\Slices;
use common\models\SlicesExtended;
use common\models\Pages;
use common\models\RestaurantsTypes;
use common\models\Restaurants;
use common\models\RoomsSpec;
use common\models\RestaurantsSpec;
use common\models\Subdomen;
use frontend\components\PremiumMixer;
use frontend\modules\arenda\models\ElasticItems;
use frontend\modules\arenda\models\SlicesOption;
use frontend\modules\arenda\models\SlicesOptionVia;
use frontend\modules\arenda\models\SlicesExtraOption;
use frontend\modules\arenda\models\SlicesExtraOptionVia;
use frontend\modules\arenda\models\SubdomenFooterLinks;
use frontend\modules\arenda\models\SubdomenHeaderMenu;
use frontend\modules\arenda\models\SubdomenHeaderSubmenu;
use frontend\modules\arenda\components\QueryFromSlice;
use frontend\components\ParamsFromQuery;
use common\models\RestaurantsPremium;
use common\models\RoomsPremium;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use common\models\FilterItems;
use frontend\modules\pmnbd\models\SubdomenFilteritem;
use common\models\elastic\FilterQueryConstructorElastic;
use common\models\YamapInfo;


class TestController extends Controller
{
	public function actionSendmessange()
	{
		$to = ['zadrotstvo@gmail.com'];
		$subj = "Тестовая заявка";
		$msg = "Тестовая заявка";
		$message = $this->sendMail($to, $subj, $msg);
		var_dump($message);
		exit;
	}

	public function actionIndex()
	{
		// GorkoApiTest::renewAllData([
		// 	[
		// 		'params' => 'city_id=4400&type_id=1&type=30,11,17,14',
		// 		'watermark' => '/var/www/pmnetwork/pmnetwork/frontend/web/img/watermark.png'
		// 	]			
		// ]);

		// $connection = new \yii\db\Connection($params['main_connection_config']);
		// $connection->open();
		// Yii::$app->set('db', $connection);

		/* $connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);

			$restaurants = Restaurants::find()
			->with('rooms')
			->with('imagesext')
			->with('subdomen')
			->with('yandexReview')
			->where(['active' => 1, 'commission' => 2])
			->limit(10000)
			->all();

		echo ('<pre>');
		print_r($restaurants[0]->special);
		exit; */

		// $room_images1 = []; //266201 - Ресторан
		// $room_images2 = []; //266203 - Банкетный зал
		// $room_images3 = []; //269893 - Веранда
		// foreach ($restaurants[0]->imagesext as $key => $value) {
		// 	if ($value['room_id'] == 266203) {
		// 		$room_images2[] = $value['path'];
		// 	} elseif ($value['room_id'] == 266201) {
		// 		$room_images1[] = $value['path'];
		// 	}
		// }


		// echo ('<pre>');
		// print_r($room_images1);
		// exit;

		/* $images = [];
		$group = array();
		foreach ($restaurants[0]->imagesext as $value) {
			$group[$value['room_id']][] = $value;
		}
		$images_sorted = array();
		$room_ids = array();
		foreach ($group as $room_id => $images_ext) {
			$room_ids[] = $room_id;
			foreach ($images_ext as $image) {
				$images_sorted[$room_id][$image['event_id']][] = $image;

				if ($room_id == 266201) {
					ArrayHelper::multisort($images_sorted[$room_id][$image['event_id']], ['sort'], [SORT_ASC]);
				}
			}
		}


		// echo ('<pre>');
		// print_r($images_sorted);
		// exit;

		$specs = [1, 0];
		$image_flag = false;
		foreach ($specs as $spec) {
			for ($i = 0; $i < 20; $i++) {
				// if (isset($images_sorted[$room->gorko_id]) && isset($images_sorted[$room->gorko_id][$spec]) && isset($images_sorted[$room->gorko_id][$spec][$i])) {
				if (isset($images_sorted[266201]) && isset($images_sorted[266201][$spec]) && isset($images_sorted[266201][$spec][$i])) {
					// $image = $images_sorted[$room->gorko_id][$spec][$i];
					$image = $images_sorted[266201][$spec][$i];
					
					$image_arr = [];
					$image_arr['id'] = $image['gorko_id'];
					$image_arr['sort'] = $image['sort'];
					$search = ['lh3.googleusercontent.com', 'nocdn.gorko.ru'];
					$image_arr['realpath'] = str_replace($search, 'img.arendazala.net', $image['path']);

					// if (stristr($image['path'], 'nocdn.gorko.ru')) {
					// 	$image_arr['realpath'] = str_replace('nocdn.gorko.ru', 'img.arendazala.net', $image['path']);
					// } elseif (stristr($image['path'], 'nocdn.gorko.ru')) {
					// 	$image_arr['realpath'] = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $image['path']);
					// }
					if (isset($images_module[$image['gorko_id']])) {
						// if (stristr($image['path'], 'nocdn.gorko.ru')) {
							$image_arr['subpath']   = str_replace($search, 'img.arendazala.net', $images_module[$image['gorko_id']]['subpath']);
							$image_arr['waterpath'] = str_replace($search, 'img.arendazala.net', $images_module[$image['gorko_id']]['waterpath']);
							$image_arr['timestamp'] = str_replace($search, 'img.arendazala.net', $images_module[$image['gorko_id']]['timestamp']);
						// } elseif (stristr($image['path'], 'lh3.googleusercontent.com')) {
						// 	$image_arr['subpath']   = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $images_module[$image['gorko_id']]['subpath']);
						// 	$image_arr['waterpath'] = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $images_module[$image['gorko_id']]['waterpath']);
						// 	$image_arr['timestamp'] = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $images_module[$image['gorko_id']]['timestamp']);
						// }
					} else {
						// $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
						// 	'gorko_id'      => $image['gorko_id'],
						// 	'params'        => $params,
						// 	'rest_flag'     => false,
						// 	'rest_gorko_id' => $restaurant->gorko_id,
						// 	'room_gorko_id' => $room->gorko_id,
						// 	'elastic_index' => static::index(),
						// 	'elastic_type'  => 'room',
						// ]));
					}
					array_push($images, $image_arr);
				}
				if (count($images) > 19) {
					$image_flag = true;
					break;
				}
			}
			if ($image_flag) break;
		} */


		// echo ('<pre>');
		// print_r($images);
		// exit;






		/* $restaurant_spec_white_list = [1, 9, 11, 12, 15, 17];
		$restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);
		if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
			return 'Неподходящий тип мероприятия';
		}

		//Тип мероприятия
		$restaurant_spec = [];

		foreach ($restaurant_spec_rest as $key => $value) {
			$restaurant_spec_arr = [];
			$restaurant_spec_arr['id'] = $value;
			$restaurant_spec_arr['name'] = isset($restaurants_spec[$value]['name']) ? $restaurants_spec[$value]['name'] : '';
			array_push($restaurant_spec, $restaurant_spec_arr);
		}

		$record->restaurant_spec = $restaurant_spec;



		$room_spec = [];

		foreach ($room->specs as $key => $value) {
			$room_spec_arr = [];
			$room_spec_arr['id'] = $value['id'];
			$room_spec_arr['name'] = isset($value['name']) ? $value['name'] : '';
			array_push($room_spec, $room_spec_arr);
		}

		$record->room_spec = $room_spec; */




		// $test = [];

		// foreach ($restaurants[0]->rooms as $key => $room) {

		// 	$room_specs_model = RoomsSpec::find()
		// 		->limit(100000)
		// 		->where(['room_id' => $room->id])
		// 		->all();

		// 	// echo ('<pre>');
		// 	// print_r($room_specs_model[0]->spec['name']);
		// 	// exit;

		// 	$room_spec = [];

		// 	foreach ($room_specs_model as $key => $room_spec_model) {
		// 		$room_spec_arr = [];
		// 		$room_spec_arr['id'] = $room_spec_model->spec['id'];
		// 		$room_spec_arr['name'] = isset($room_spec_model->spec['name']) ? $room_spec_model->spec['name'] : '';
		// 		array_push($room_spec, $room_spec_arr);
		// 	}


		// 	// echo ('<pre>');
		// 	// print_r($room->gorko_id);
		// 	// echo ('<pre>');
		// 	// print_r($room->specs);
		// 	// exit;

		// 	// $room_spec = [];

		// 	// foreach ($room->specs as $key => $value) {
		// 	// 	$room_spec_arr = [];
		// 	// 	$room_spec_arr['id'] = $value['id'];
		// 	// 	$room_spec_arr['name'] = isset($value['name']) ? $value['name'] : '';
		// 	// 	array_push($room_spec, $room_spec_arr);
		// 	// }

		// 	$test[] = $room_spec;
		// }
		// echo ('<pre>');
		// print_r($test);
		// exit;



		/* // Цены для разных типов мероприятий
		$rooms_id = [];
		$room_prices_arr = [];
		$i = 0;
		$j = 0;
		$k = 0;
		foreach ($restaurants as $key => $restaurant) {
			$j++;

			foreach ($restaurant->rooms as $key => $room) {
				$k++;
				$rooms_id[] = $room->id;
				// echo ('<pre>');
				// print_r($room->id);
				// exit;

				$room_specs_price_model = RoomsSpec::find()
					->limit(100000)
					->where(['room_id' => $room->id])
					// ->where(['room_id' => 100])
					->all();
				
				if (isset($room_specs_price_model) && !empty($room_specs_price_model)) {
					$i++;
					foreach ($room_specs_price_model as $key => $spec_price) {
						$spec_model = RestaurantsSpec::find()->where(['id' => $spec_price['spec_id']])->one();
						$spec_name = $spec_model['name'];
						$room_prices = [];
						$room_prices['id'] = $spec_price['room_id'];
						$room_prices['spec_id'] = $spec_price['spec_id'];
						$room_prices['spec_name'] = $spec_name;
						$room_prices['price'] = $spec_price['price'];

						array_push($room_prices_arr, $room_prices);
					}
				}
			}
		}
		echo ('<pre>');
		print_r($i);
		echo ('<pre> ресторанов: ');
		print_r($j);
		echo ('<pre> залов: ');
		print_r($k);
		echo ('<pre>');
		print_r($room_prices_arr);
		echo ('<pre>');
		print_r($rooms_id);
		exit; */
		// $record->room_prices = $room_prices_arr;
		// startDate":"2022-12-14T03:00:00+00:00","endDate":"2023-12-14T03:00:00+00:00"

		/* $current_date = date('c', mktime(3, 0, 0, date("m"), date("d"), date("Y")));
		$next_year = date('c', mktime(3, 0, 0, date("m"), date("d"), date("Y") + 1));

		echo ('<pre>');
		print_r($current_date);
		echo ('<pre>');
		print_r($next_year);
		echo ('<pre>');
		print_r(Yii::$app->params['subdomen_name']);
		echo ('<pre>');
		print_r('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		echo ('<pre>');
		print_r($_SERVER['REQUEST_URI']);
		exit; */


		// $slice_extra_options = SlicesExtraOptionVia::find()
		// 	->with('options')
		// 	->all();
		// $test = ArrayHelper::index($test, 'slice_id');


		// $connection = new \yii\db\Connection([
		// 	'username' => 'root',
		// 	'password' => 'GxU25UseYmeVcsn5Xhzy',
		// 	'charset'  => 'utf8mb4',
		// 	'dsn' => 'mysql:host=localhost;dbname=pmn'
		// ]);
		// $connection->open();
		// Yii::$app->set('db', $connection);

		// $restaurants = Restaurants::find()
		// 	->with('rooms')
		// 	->with('imagesext')
		// 	->with('subdomen')
		// 	->with('yandexReview')
		// 	->where(['active' => 1, 'commission' => 2, 'city_id' => 4682])
		// 	->limit(10000)
		// 	->all();

		// // $connection = new \yii\db\Connection($params['site_connection_config']);
		// $connection = new \yii\db\Connection([
		// 	'username' => 'root',
		// 	'password' => 'GxU25UseYmeVcsn5Xhzy',
		// 	'charset'  => 'utf8mb4',
		// 	'dsn' => 'mysql:host=localhost;dbname=pmn_arenda'
		// ]);
		// $connection->open();
		// Yii::$app->set('db', $connection);


		// $restaurants_premium = RestaurantsPremium::find()
		// 	->where(['>', 'finish', time()])
		// 	->limit(100000)
		// 	->asArray()
		// 	->all();
		// $restaurants_premium = ArrayHelper::index($restaurants_premium, 'gorko_id');

		// $rooms_premium = RoomsPremium::find()
		// 	->where(['>', 'finish', time()])
		// 	->limit(100000)
		// 	->asArray()
		// 	->all();
		// $rooms_premium = ArrayHelper::index($rooms_premium, 'gorko_id');

		// echo ('<pre>');
		// print_r($restaurants);
		// exit;

		// $premium = isset($restaurants_premium[$restaurant->gorko_id]);

		// $room_premiun = isset($rooms_premium[$room->gorko_id]);

		// foreach ($restaurants as $key => $rest) {
		// 	foreach ($rest->rooms as $key => $room) {
		// 		$premium = isset($restaurants_premium[$rest->gorko_id]); //false

		// 		$room_premium = isset($rooms_premium[$room->gorko_id]);  // true

		// 		if (!$premium && !$room_premium ) {
		// 			echo ('<pre>');
		// 			print_r('not premium');
		// 		} else {
		// 			echo ('<pre>');
		// 			print_r($room);
		// 		}
		// 	}
		// }



		// $extra_options_prazdnik_ids = [
		// 	1 => 'День рождения', // 1
		// 	5 => 'Корпоратив', // 5
		// 	4 => 'Новый год', // 4
		// 	3 => 'Свадьбу', // 3
		// 	6 => 'Выпускной', // 6
		// 	2 => 'Детский день рождения', // 2
		// ];

		// $restaurant_extra_services = 'Фотограф, Видеограф, Торт, Dj, Живая музыка, Ведущий, Оформление';
		// $rest_extra_serv_explode = array_map('trim', explode(',', $restaurant_extra_services));

		// $restaurant_special = 'Можно свои б/а напитки, Выездная регистрация, Велком зона, Wi-Fi / интернет, Сцена, Проектор, TV экраны, Музыкальное оборудование, Детское меню';
		// $rest_special_explode = array_map('trim', explode(',', $restaurant_special));


		// $restuarant_welcome_zone = ''; //Велком зона
		// $restuarant_scene = ''; //Сцена
		// $restuarant_wi_fi = ''; //Wi-Fi / интернет
		// $restuarant_outside_registration = ''; //Выездная регистрация
		// $restuarant_music_equipment = ''; //Музыкальное оборудование
		// $restuarant_projector = ''; //Проектор
		// $restuarant_tv_screens = ''; //TV экраны
		// foreach ($rest_special_explode as $key => $rest_special) {
		// 	switch ($rest_special) {
		// 		case 'Велком зона':
		// 			$restuarant_welcome_zone = 1;
		// 			break;
		// 		case 'Сцена':
		// 			$restuarant_scene = 1;
		// 			break;
		// 		case 'Wi-Fi / интернет':
		// 			$restuarant_wi_fi = 1;
		// 			break;
		// 		case 'Выездная регистрация':
		// 			$restuarant_outside_registration = 1;
		// 			break;
		// 		case 'Музыкальное оборудование':
		// 			$restuarant_music_equipment = 1;
		// 			break;
		// 		case 'Проектор':
		// 			$restuarant_projector = 1;
		// 			break;
		// 		case 'TV экраны':
		// 			$restuarant_tv_screens = 1;
		// 			break;
		// 	}
		// }


		// $restuarant_photographer = ''; //Фотограф
		// $restuarant_videographer = ''; //Видеограф
		// $restuarant_leading = ''; //Ведущий
		// $restuarant_dj = ''; //Dj
		// $restuarant_live_music = ''; //Живая музыка
		// $restuarant_cake = ''; //Торт
		// $restuarant_decor = ''; //Оформление
		// foreach ($rest_extra_serv_explode as $key => $rest_extra_serv) {
		// 	switch ($rest_extra_serv) {
		// 		case 'Фотограф':
		// 			$restuarant_photographer = 1;
		// 			break;
		// 		case 'Видеограф':
		// 			$restuarant_videographer = 1;
		// 			break;
		// 		case 'Ведущий':
		// 			$restuarant_leading = 1;
		// 			break;
		// 		case 'Dj':
		// 			$restuarant_dj = 1;
		// 			break;
		// 		case 'Живая музыка':
		// 			$restuarant_live_music = 1;
		// 			break;
		// 		case 'Торт':
		// 			$restuarant_cake = 1;
		// 			break;
		// 		case 'Оформление':
		// 			$restuarant_decor = 1;
		// 			break;
		// 	}
		// }


		/* $cusines = [
			'европейская',
			'кавказская',
			'русская',
			'итальянская',
			'грузинская',
			'азербайджанская',
			'смешанная',
			'авторская',
			'американская',
			'английская',
			'австрийская',
			'японская',
			'узбекская',
			'китайская',
			'паназиатская',
			'восточная',
			'альпийская',
			'фьюжн',
		];

		// $rest_cuisine_explode = array_map('trim', explode(',', $restaurant->extra_services));
		foreach ($cusines as $key => $rest_cuisine) {
			switch (mb_strtolower($rest_cuisine)) {
				case 'европейская':
					$rest_cuisine_eu = 1;
					break;
				case 'кавказская':
					$rest_cuisine_caucas = 1;
					break;
				case 'русская':
					$rest_cuisine_ru = 1;
					break;
				case 'итальянская':
					$rest_cuisine_ital = 1;
					break;
				case 'грузинская':
					$rest_cuisine_georgian = 1;
					break;
				case 'азербайджанская':
					$rest_cuisine_azerbaij = 1;
					break;
				case 'смешанная':
					$rest_cuisine_mixed = 1;
					break;
				case 'авторская':
					$rest_cuisine_author = 1;
					break;
				case 'американская':
					$rest_cuisine_american = 1;
					break;
				case 'английская':
					$rest_cuisine_eng = 1;
					break;
				case 'австрийская':
					$rest_cuisine_austrian = 1;
					break;
				case 'японская':
					$rest_cuisine_japan = 1;
					break;
				case 'узбекская':
					$rest_cuisine_uzbek = 1;
					break;
				case 'китайская':
					$rest_cuisine_china = 1;
					break;
				case 'паназиатская':
					$rest_cuisine_panasian = 1;
					break;
				case 'восточная':
					$rest_cuisine_east = 1;
					break;
				case 'альпийская':
					$rest_cuisine_alpine = 1;
					break;
				case 'фьюжн':
					$rest_cuisine_fusion = 1;
					break;
			}
		}
		$restaurant_cuisine_eu = isset($rest_cuisine_eu) ? $rest_cuisine_eu : '';
		$restaurant_cuisine_caucas = isset($rest_cuisine_caucas) ? $rest_cuisine_caucas : '';
		$restaurant_cuisine_ru = isset($rest_cuisine_ru) ? $rest_cuisine_ru : '';
		$restaurant_cuisine_ital = isset($rest_cuisine_ital) ? $rest_cuisine_ital : '';
		$restaurant_cuisine_azerbaij = isset($rest_cuisine_azerbaij) ? $rest_cuisine_azerbaij : '';
		$restaurant_cuisine_mixed = isset($rest_cuisine_mixed) ? $rest_cuisine_mixed : '';
		$restaurant_cuisine_author = isset($rest_cuisine_author) ? $rest_cuisine_author : '';
		$restaurant_cuisine_american = isset($rest_cuisine_american) ? $rest_cuisine_american : '';
		$restaurant_cuisine_eng = isset($rest_cuisine_eng) ? $rest_cuisine_eng : '';
		$restaurant_cuisine_austrian = isset($rest_cuisine_austrian) ? $rest_cuisine_austrian : '';
		$restaurant_cuisine_japan = isset($rest_cuisine_japan) ? $rest_cuisine_japan : '';
		$restaurant_cuisine_uzbek = isset($rest_cuisine_uzbek) ? $rest_cuisine_uzbek : '';
		$restaurant_cuisine_fusion = isset($rest_cuisine_fusion) ? $rest_cuisine_fusion : '';

		echo ('<pre>');
		print_r($rest_cuisine_alpine);
		exit; */




		/* // ===== Особенности START =====
		$features_list = [
			1 => 'Свой алкоголь',
			2 => 'Алкоголь в наличии',
			3 => 'У воды',
			4 => 'Фуршет',
			5 => 'Велком зона',
			6 => 'Сцена',
			7 => 'Караоке',
			8 => 'Выездная регистрация',
			9 => 'Wi-Fi / интернет',
			10 => 'С подарком',
		];
		$room_features = [];

		//Свой алкоголь
		if ($restaurant->alcohol == 1 || $restaurant->alcohol == 2) {
			$room_features_arr = [];
			$room_features_arr['id'] = 1;
			$room_features_arr['name'] = $features_list[1];
			array_push($room_features, $room_features_arr);
		}
		//Алкоголь в наличии
		if ($restaurant->alcohol_stock == 1) {
			$room_features_arr = [];
			$room_features_arr['id'] = 2;
			$room_features_arr['name'] = $features_list[2];
			array_push($room_features, $room_features_arr);
		}
		//У воды
		foreach ($restaurant_location_rest as $key => $value) {
			$room_features_arr = [];
			if ($value == 1 || $value == 2 || $value == 3) {
				$room_features_arr['id'] = 3;
				$room_features_arr['name'] = $features_list[3];
				array_push($room_features, $room_features_arr);

				break;
			}
		}
		//Фуршет
		if (isset($room_specs_model) && !empty($room_specs_model)) {
			foreach ($room_specs_model as $room_spec_model) {
				$room_features_arr = [];
				if ($room_spec_model->spec['id'] == 14) {
					$room_features_arr['id'] = 4;
					$room_features_arr['name'] = $features_list[4];
					array_push($room_features, $room_features_arr);

					break;
				}
			}
		}

		foreach ($rest_special_explode as $key => $rest_special) {
			$room_features_arr = [];
			switch ($rest_special) {
				case 'Велком зона':
					$room_features_arr['id'] = 5;
					$room_features_arr['name'] = $features_list[5];
					array_push($room_features, $room_features_arr);
					break;
				case 'Сцена':
					$room_features_arr['id'] = 6;
					$room_features_arr['name'] = $features_list[6];
					array_push($room_features, $room_features_arr);
					break;
				case 'Выездная регистрация':
					$room_features_arr['id'] = 8;
					$room_features_arr['name'] = $features_list[8];
					array_push($room_features, $room_features_arr);
					break;
				case 'Wi-Fi / интернет':
					$room_features_arr['id'] = 9;
					$room_features_arr['name'] = $features_list[9];
					array_push($room_features, $room_features_arr);
					break;
			}
		}
		//Караоке
		if (str_contains(mb_strtolower($room->name), 'караоке')) {
			$room_features_arr = [];
			$room_features_arr['id'] = 7;
			$room_features_arr['name'] = $features_list[7];
			array_push($room_features, $room_features_arr);
		}
		//С подарком
		if ($restaurant->commission == 2) {
			$room_features_arr = [];
			$room_features_arr['id'] = 10;
			$room_features_arr['name'] = $features_list[10];
			array_push($room_features, $room_features_arr);
		}
		$record->features = $room_features;
		// ===== Особенности END ===== */






		// $options_assoc_arr = [
		// 	'сцена' => 'Сцена',
		// 	// 'новогоднее меню' => '',
		// 	'выездная регистрация' => 'Выездная регистрация',
		// 	'велком зона' => 'Велком зона',
		// 	'проектор' => 'Проектор',
		// 	'TV экраны' => 'TV экраны',
		// 	'Wi-Fi / интернет' => 'Wi-Fi / интернет',
		// 	'музыкальное оборудование' => 'Музыкальное оборудование',
		// 	'детское меню' => 'Детское меню',
		// ];

		// $extra_options_assoc_arr = [
		// 	'ведущий (шоу-программа)' => 'Ведущий',
		// 	'живая музыка' => 'Живая музыка',
		// 	'торт' => 'Торт',
		// 	'оформление зала на *Тип мероприятия*' => 'Оформление',
		// 	'фотограф (фотосессия)' => 'Фотограф',
		// 	'видеограф' => 'Видеограф',
		// 	'DJ' => 'Dj',
		// 	// 'караоке' => '',
		// 	// 'фуршет' => '',
		// 	// 'у воды' => '',
		// ];

		// $prazdnik_extra_options = [];
		// $prazdnik_options = [];

		// foreach ($extra_options_prazdnik_ids as $prazdnik_id => $prazdnik_name) {
		// 	// $i = 0;
		// 	$prazdnik_extra_options_model = SlicesExtraOptionVia::find()
		// 		->where(['prazdnik_id' => $prazdnik_id])
		// 		->with('options')
		// 		->all();

		// foreach ($prazdnik_extra_options_model as $key => $option) {
		// 	foreach ($rest_extra_serv_explode as $key => $rest_extra_service) {
		// 		if (
		// 			isset($extra_options_assoc_arr[$option['options']['name']])
		// 			&& $extra_options_assoc_arr[$option['options']['name']] == $rest_extra_service
		// 		) {
		// 			// $prazdnik_extra_options[$prazdnik_id][$i]['prazdnik_id'] = $prazdnik_id;
		// 			/* if ($option['options']['name'] == 'оформление зала на *Тип мероприятия*') {
		// 				$prazdnik_extra_options[$prazdnik_id][$i]['name'] = 'оформление зала на ' . $prazdnik_name;
		// 			} else {
		// 				$prazdnik_extra_options[$prazdnik_id][$i]['name'] = $option['options']['name'];
		// 			}
		// 			$i++; */

		// 			if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
		// 				if ($option['options']['name'] == 'оформление зала на *Тип мероприятия*') {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] .= ', оформление зала на ' . $prazdnik_name;
		// 				} else {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
		// 				}
		// 			} else {
		// 				if ($option['options']['name'] == 'оформление зала на *Тип мероприятия*') {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] = 'оформление зала на ' . $prazdnik_name;
		// 				} else {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
		// 				}
		// 			}
		// 		}
		// 	}

		// 	$room_name = '- зал караоке ';

		// 	//караоке
		// 	// if($option['extra_option_id'] == 3 && str_contains(mb_strtolower($room->name), 'караоке')) {
		// 	if ($option['extra_option_id'] == 3 && str_contains(mb_strtolower($room_name), 'караоке')) {
		// 		// $prazdnik_extra_options[$prazdnik_id][$i]['prazdnik_id'] = $prazdnik_id;
		// 		/* $prazdnik_extra_options[$prazdnik_id][$i]['name'] = $option['options']['name'];
		// 		$i++; */

		// 		if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
		// 			$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
		// 		} else {
		// 			$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
		// 		}
		// 	}


		// 	$room_spec = [
		// 		[
		// 			'name' => 'Фуршет'
		// 		]
		// 	];
		// 	//фуршет
		// 	if ($option['extra_option_id'] == 5 && isset($room_spec) && !empty($room_spec)) {
		// 		foreach ($room_spec as $key => $r_spec) {
		// 			if ($r_spec['name'] == 'Фуршет') {
		// 				// $prazdnik_extra_options[$prazdnik_id][$i]['prazdnik_id'] = $prazdnik_id;
		// 				/* $prazdnik_extra_options[$prazdnik_id][$i]['name'] = $option['options']['name'];
		// 				$i++; */

		// 				if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
		// 				} else {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
		// 				}

		// 				break;
		// 			}
		// 		}
		// 	}

		// 	$restaurant_location_rest = [
		// 		2, 3, 4
		// 	];

		// 	//у воды
		// 	if ($option['extra_option_id'] == 7 && isset($restaurant_location_rest) && !empty($restaurant_location_rest)) {
		// 		foreach ($restaurant_location_rest as $rest_location_id) {
		// 			if ($rest_location_id == 1 || $rest_location_id == 2 || $rest_location_id == 7) {
		// 				// $prazdnik_extra_options[$prazdnik_id][$i]['prazdnik_id'] = $prazdnik_id;
		// 				/* $prazdnik_extra_options[$prazdnik_id][$i]['name'] = $option['options']['name'];
		// 				$i++; */

		// 				if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
		// 				} else {
		// 					$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
		// 				}

		// 				break;
		// 			}
		// 		}
		// 	}
		// }

		/* $prazdnik_options_model = SlicesOptionVia::find()
				->where(['prazdnik_id' => $prazdnik_id])
				->with('options')
				->all();

			foreach ($prazdnik_options_model as $option) {
				foreach ($rest_special_explode as $rest_special) {
					if (
						isset($options_assoc_arr[$option['options']['name']])
						&& $options_assoc_arr[$option['options']['name']] == $rest_special
					) {
						if (isset($prazdnik_options[$prazdnik_id]['options']) && !empty($prazdnik_options[$prazdnik_id]['options'])) {
							$prazdnik_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
						} else {
							$prazdnik_options[$prazdnik_id]['options'] = $option['options']['name'];
						}
					}
				}
				// 'новогоднее меню'
				if ($option['option_id'] == 2) {
					if (isset($prazdnik_options[$prazdnik_id]['options']) && !empty($prazdnik_options[$prazdnik_id]['options'])) {
						$prazdnik_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];;
					} else {
						$prazdnik_options[$prazdnik_id]['options'] = $option['options']['name'];
					}
				}
			}
		}


		$rest_type_ids = [
			7 => 'Конференц-зал',
			9 => 'Актовый зал',
			8 => 'Кинозал',
			6 => 'Танцевальный зал',
			21 => 'Коттеджи',
			99 => 'Другие типы площадок',
		];


		$rest_type_extra_options = [];
		$rest_type_options = [];

		foreach ($rest_type_ids as $rest_type_id => $type_name) {
			$rest_type_extra_options_model = SlicesExtraOptionVia::find()
				->where(['rest_type_id' => $rest_type_id])
				->with('options')
				->all();

			foreach ($rest_type_extra_options_model as $option) {
				foreach ($rest_extra_serv_explode as $rest_extra_service) {
					if (
						isset($extra_options_assoc_arr[$option['options']['name']])
						&& $extra_options_assoc_arr[$option['options']['name']] == $rest_extra_service
					) {
						if (isset($rest_type_extra_options[$rest_type_id]['options']) && !empty($rest_type_extra_options[$rest_type_id]['options'])) {
							$rest_type_extra_options[$rest_type_id]['options'] .= ', ' . $option['options']['name'];
						} else {
							$rest_type_extra_options[$rest_type_id]['options'] = $option['options']['name'];
						}
					}
				}

				$room_name = '- зал караоке ';

				//караоке
				// if($option['extra_option_id'] == 3 && str_contains(mb_strtolower($room->name), 'караоке')) {
				if ($option['extra_option_id'] == 3 && str_contains(mb_strtolower($room_name), 'караоке')) {
					if (isset($rest_type_extra_options[$rest_type_id]['options']) && !empty($rest_type_extra_options[$rest_type_id]['options'])) {
						$rest_type_extra_options[$rest_type_id]['options'] .= ', ' . $option['options']['name'];
					} else {
						$rest_type_extra_options[$rest_type_id]['options'] = $option['options']['name'];
					}
				}

				$room_spec = [
					[
						'name' => 'Фуршет'
					]
				];
				//фуршет
				if ($option['extra_option_id'] == 5 && isset($room_spec) && !empty($room_spec)) {
					foreach ($room_spec as $key => $r_spec) {
						if ($r_spec['name'] == 'Фуршет') {
							if (isset($rest_type_extra_options[$rest_type_id]['options']) && !empty($rest_type_extra_options[$rest_type_id]['options'])) {
								$rest_type_extra_options[$rest_type_id]['options'] .= ', ' . $option['options']['name'];
							} else {
								$rest_type_extra_options[$rest_type_id]['options'] = $option['options']['name'];
							}

							break;
						}
					}
				}
			}


			$rest_type_options_model = SlicesOptionVia::find()
				->where(['rest_type_id' => $rest_type_id])
				->with('options')
				->all();

			foreach ($rest_type_options_model as $option) {
				foreach ($rest_special_explode as $rest_special) {
					if (
						isset($options_assoc_arr[$option['options']['name']])
						&& $options_assoc_arr[$option['options']['name']] == $rest_special
					) {
						if (isset($rest_type_options[$rest_type_id]['options']) && !empty($rest_type_options[$rest_type_id]['options'])) {
							$rest_type_options[$rest_type_id]['options'] .= ', ' . $option['options']['name'];
						} else {
							$rest_type_options[$rest_type_id]['options'] = $option['options']['name'];
						}
					}
				}
			}
		} */



		/* $header_menu = SubdomenHeaderMenu::find()
			->with(['submenus' => function ($query) {
				$query->andWhere(['active' => 1]);
				$query->andWhere(['city_id' => Yii::$app->params['subdomen_id']]);
			}])
			->all(); */


		//* ======== обновление таблицы "yamap_info" в общей БД START ========
		$connection = new \yii\db\Connection([
			'username' => 'root',
			'password' => 'GxU25UseYmeVcsn5Xhzy',
			'charset'  => 'utf8mb4',
			'dsn' => 'mysql:host=localhost;dbname=pmn'
		]);
		$connection->open();
		Yii::$app->set('db', $connection);


		// $yamap_model = YamapInfo::find()->all();

		$restaurants = Restaurants::find()
			->with('yandexReview')
			// ->where(['>', 'id', 5000])
			// ->where(['>', 'id', 7000])
			// ->where(['>', 'id', 9000])
			// ->where(['>', 'id', 11000])
			// ->where(['>', 'id', 12000])
			->where(['>', 'id', 14000])
			->limit(20000)
			->all();

		//* добавление новых полей в таблицу "yamap_info"
		foreach ($restaurants as $rest) {
			$yamap_model = YamapInfo::find()->where(['gorko_id' => $rest->gorko_id])->one();
			if (empty($yamap_model)) {
				$model = new YamapInfo();
				$model->gorko_id = $rest->gorko_id;
				$model->name = $rest->name;
				$model->latitude = $rest->latitude;
				$model->longitude = $rest->longitude;
				$model->save();
			}
		}
		//* ======== обновление таблицы "yamap_info" в общей БД END ========








		echo 1111;
	}

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
		$return['params_filter'] = $temp_params->params_filter;

		return $return;
	}


	public function actionTest()
	{
		GorkoApiTest::showOne([
			[
				'params' => 'city_id=4400&type_id=1&type=30,11,17,14',
				'watermark' => '/var/www/pmnetwork/pmnetwork/frontend/web/img/watermark.png'
			]
		]);
	}

	public function actionRenewelastic()
	{
		ElasticItems::refreshIndex();
	}

	public function actionSoftrenewelastic()
	{
		ElasticItems::softRefreshIndex();
	}

	public function actionCreateindex()
	{
		ElasticItems::softRefreshIndex();
	}

	public function actionImgload()
	{
		//header("Access-Control-Allow-Origin: *");
		$curl = curl_init();
		$file = '/var/www/pmnetwork/pmnetwork_konst/frontend/web/img/favicon.png';
		$mime = mime_content_type($file);
		$info = pathinfo($file);
		$name = $info['basename'];
		$output = curl_file_create($file, $mime, $name);
		$params = [
			//'mediaId' => 55510697,
			'url' => 'https://lh3.googleusercontent.com/XKtdffkbiqLWhJAWeYmDXoRbX51qNGOkr65kMMrvhFAr8QBBEGO__abuA_Fu6hHLWGnWq-9Jvi8QtAGFvsRNwqiC',
			'token' => '4aD9u94jvXsxpDYzjQz0NFMCpvrFQJ1k',
			'watermark' => $output,
			'hash_key' => 'svadbanaprirode'
		];
		curl_setopt($curl, CURLOPT_URL, 'https://api.gorko.ru/api/v2/tools/mediaToSatellite');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, '');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);


		echo '<pre>';
		$response = curl_exec($curl);

		print_r(json_decode($response));
		curl_close($curl);

		//echo '<pre>';

		//echo '<pre>';
	}


	public function actionCustom()
	{
		// $filter_model = Filter::find()->with('items')->asArray()->all();

		// foreach ($filter_model[1]['items'] as $key => $value) {
		// echo 'rest_type ' . $this->translit($value['text']) . ' ' . $value['text'] . ' ' . '{rest_type:' . $value['value'] . '}<br/>';
		// 	echo $this->translit($value['text']) . ' ' . $value['text'] . '<br/>';
		// }

		// echo '<pre>';
		// print_r($filter_model[0]['items']);

		// $slices = Slices::find()->all();

		// foreach ($slices as $slice){
		// $extended = new SlicesExtended();
		// $extended->alias = $slice->alias;
		// $extended->name = $slice->h1;

		// $extended = SlicesExtended::find()->where(['alias' => $slice->alias])->one();
		// $extended->type = $slice->type;

		// $extended->save();
		// }

		// Pages::createSiteObjects();

		$model = RestaurantsTypes::find()->asArray()->all();
		$filter_model = Filter::find()->with('items')->asArray()->all();

		foreach ($model as $value) {
			// echo '[' . $value['value'] . ',' . $value['text'] . '],<br/>';

		}
		print_r($filter_model[1]);

		exit;
	}

	private function translit($s)
	{
		$s = (string) $s; // преобразуем в строковое значение
		$s = strip_tags($s); // убираем HTML-теги
		$s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
		$s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
		$s = trim($s); // убираем пробелы в начале и конце строки
		$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
		$s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
		$s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
		$s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
		return $s; // возвращаем результат
	}

	private function sendMail($to, $subj, $msg)
	{
		$message = Yii::$app->mailer->compose()
			->setFrom(['svadbanaprirode@yandex.ru' => 'Свадьба на природе'])
			->setTo($to)
			->setSubject($subj)
			//->setTextBody('Plain text content')
			->setHtmlBody($msg);
		//echo '<pre>';
		//print_r($message);
		//exit;
		if (count($_FILES) > 0) {
			foreach ($_FILES['files']['tmp_name'] as $k => $v) {
				$message->attach($v, ['fileName' => $_FILES['files']['name'][$k]]);
			}
		}
		return $message->send();
	}

	//*Обновление активных ссылок для меню в футере
	public function actionUpdateActiveFooterLinks()
	// /test/update-active-footer-links
	{
		$links = [
			'Танцевальные залы' => 'tancevalnyy-zal',
			'Отели/гостиницы' => 'oteli-i-gostinicy',
			'Актовые залы' => 'aktovye-zaly',
			'Веранды' => 'veranda',
			'Летние площадки' => 'letnyaya-ploshchadka',
			'Конференц-залы' => 'konferenc-zal',
			'Кинозалы' => 'kinozaly',
			'Рестораны' => 'restorany',
			'Природа' => 'priroda',
			'Арт-площадки' => 'art-ploshchadki',
			'Банкетные залы' => 'banketnyy-zal',
			'Лофты' => 'loft',
			'Бары/пабы' => 'barypaby',
			'Террасы' => 'terrasa',
			'Коттеджи' => 'kottedji',
			'Залы' => 'zaly',
			'Кафе' => 'kafe',
			'Клубы' => 'kluby',
			'Шатры' => 'shatry',
		];

		$subdomen_list = Subdomen::find()
			->where(['active' => 1])
			->orderBy(['name' => SORT_ASC])
			->all();

		$filter_model = Filter::find()->with('items')->where(['active' => 1])->orderBy(['sort' => SORT_ASC])->all();
		$slices_model = Slices::find()->all();

		foreach ($subdomen_list as $key => $subdomen) {
			$sort_key = 0;
			foreach ($links as $link_key => $link) {
				$slice_obj = new QueryFromSlice($link);

				if ($slice_obj->flag) {
					Yii::$app->params['subdomen_id'] = $subdomen['city_id'];
					$params = $this->parseGetQuery($slice_obj->params, $filter_model, $slices_model);
					$elastic_model = new ElasticItems;
					$items = PremiumMixer::getItemsWithPremium($params['params_filter'], 30, 1, false, 'rooms', $elastic_model, false, false, false, false, false, true);

					$subdomen_footer_link = SubdomenFooterLinks::find()->where(['city_id' => $subdomen['city_id']])->andWhere(['link' => $link])->one();
					if (!$subdomen_footer_link) {
						$subdomen_link = new SubdomenFooterLinks();
						$subdomen_link['city_id'] = $subdomen['city_id'];
						$subdomen_link['name'] = $link_key;
						$subdomen_link['link'] = $link;
						$subdomen_link['sort'] = $sort_key;
						if ($items->total == 0) {
							$subdomen_link['active'] = 0;
						} else {
							$subdomen_link['active'] = 1;
						}
						$subdomen_link->save();
					}
				}

				$sort_key++;
			}
		}
		echo 'ссылки обновлены';
	}


	//* обновление активных ссылок в хедере в зависимости от поддомена
	public function actionUpdateActiveHeaderLinks()
	// /test/update-active-header-links
	{
		$header_menu = [
			'svadba' => [
				'id' => 1,
				'text' => '💍Свадьба',
				'submenu' => [
					[
						'name' => 'Все площадки',
						'link' => 'svadba',
					],
					[
						'name' => 'Банкетные залы',
						'link' => 'arenda-banketnogo-zala-dlya-svadby'
					],
					[
						'name' => 'Рестораны',
						'link' => 'arenda-restorana-na-svadbu',
					],
					[
						'name' => 'Кафе',
						'link' => 'arenda-kafe-dlya-svadby',
					],
					[
						'name' => 'Лофты',
						'link' => 'arenda-lofta-dlya-svadby',
					],
					[
						'name' => 'На природе',
						'link' => 'svadba-na-prirode',
					],
					[
						'name' => 'Шатры',
						'link' => 'arenda-shatrov-na-svadbu',
					],
					[
						'name' => 'Веранды',
						'link' => 'arenda-verandy-dlya-svadby',
					],
					[
						'name' => 'У воды',
						'link' => 'svadba-y-vody',
					],
					[
						'name' => 'Коттеджи',
						'link' => 'arenda-kottedzha-dlya-svadby',
					],
					[
						'name' => 'Отели',
						'link' => 'svadba-v-otele',
					]
				]
			],
			'den-rojdeniya' => [
				'id' => 2,
				'text' => '🎂День рождения',
				'submenu' => [
					[
						'name' => 'Все площадки',
						'link' => 'den-rojdeniya',
					],
					[
						'name' => 'Банкетные залы',
						'link' => 'arenda-banketnogo-zala-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Рестораны',
						'link' => 'arenda-restorana-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Кафе',
						'link' => 'arenda-kafe-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Лофты',
						'link' => 'arenda-lofta-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'На природе',
						'link' => 'den-rozhdeniya-na-prirode',
					],
					[
						'name' => 'Шатры',
						'link' => 'arenda-terrasy-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Веранды',
						'link' => 'arenda-verandy-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Коттеджи',
						'link' => 'arenda-kottedzha-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Клубы',
						'link' => 'arenda-cluba-dlya-dnya-rozhdeniya',
					],
					[
						'name' => 'Бары',
						'link' => 'arenda-bara-dlya-dnya-rozhdeniya',
					],
				]
			],
			'korporativ' => [
				'id' => 3,
				'text' => '🤟Корпоратив',
				'submenu' => [
					[
						'name' => 'Все площадки',
						'link' => 'korporativ',
					],
					[
						'name' => 'Площадки',
						'link' => 'ploshchadki-dlya-korporativa',
					],
					[
						'name' => 'Лофты',
						'link' => 'loft-dlya-korporativa',
					],
					[
						'name' => 'На природе',
						'link' => 'zagorodnyye-ploshchadki-dlya-korporativa',
					],
					[
						'name' => 'Рестораны',
						'link' => 'restorany-dlya-korporativa',
					],
					[
						'name' => 'Кафе',
						'link' => 'kafe-dlya-korporativa',
					],
				]
			],
			'vypusknoy' => [
				'id' => 4,
				'text' => '🎓Выпускной',
				'submenu' => [
					[
						'name' => 'Все площадки',
						'link' => 'vypusknoy',
					],
					[
						'name' => 'На природе',
						'link' => 'vypusknoy-za-gorodom-na-prirode',
					],
				]
			],
			'novyy-god' => [
				'id' => 5,
				'text' => '⛄Новый год',
				'submenu' => [
					[
						'name' => 'Все площадки',
						'link' => 'novyy-god',
					],
				]
			]
		];

		//сохраняем в БД основные пункты header menu
		//расскомментировать если довляются новые пункты в меню
		/* foreach ($header_menu as $key => $menu) {
			$menu_item = SubdomenHeaderMenu::find()->where(['name' => $key])->one();
			if (!$menu_item) {
				$subdomen_menu = new SubdomenHeaderMenu();
				$subdomen_menu['name'] = $key;
				$subdomen_menu['text'] = $menu['text'];
				$subdomen_menu->save();
			}
		} */


		//выбираем сначала первую половину поддоменов и затем выбираем вторую половину (иначе может не хватить времени обработки скрипта)
		$subdomen_list = Subdomen::find()
			->where(['active' => 1])
			// ->andWhere(['<=', 'id', 26])
			->andWhere(['>=', 'id', 25])
			->orderBy(['name' => SORT_ASC])
			->all();

		$filter_model = Filter::find()->with('items')->where(['active' => 1])->orderBy(['sort' => SORT_ASC])->all();
		$slices_model = Slices::find()->all();

		foreach ($subdomen_list as $subdomen) {
			foreach ($header_menu as $menu_key => $menu) {
				$sort_key = 0;
				$menu_item = SubdomenHeaderMenu::find()->where(['name' => $menu_key])->one();
				foreach ($menu['submenu'] as $submenu) {
					$slice_obj = new QueryFromSlice($submenu['link']);

					if ($slice_obj->flag) {
						Yii::$app->params['subdomen_id'] = $subdomen['city_id'];
						$params = $this->parseGetQuery($slice_obj->params, $filter_model, $slices_model);
						$elastic_model = new ElasticItems;
						$items = PremiumMixer::getItemsWithPremium($params['params_filter'], 30, 1, false, 'rooms', $elastic_model, false, false, false, false, false, true);

						$subdomen_submenu = SubdomenHeaderSubmenu::find()
							->where(['city_id' => $subdomen['city_id']])
							->andWhere(['link' => $submenu['link']])
							->one();
						if (!$subdomen_submenu) {
							$subdomen_submenu = new SubdomenHeaderSubmenu();
							$subdomen_submenu['menu_id'] = $menu_item['id'];
							$subdomen_submenu['city_id'] = $subdomen['city_id'];
							$subdomen_submenu['name'] = $submenu['name'];
							$subdomen_submenu['link'] = $submenu['link'];
							$subdomen_submenu['sort'] = $sort_key;
						}
						if ($items->total == 0) {
							$subdomen_submenu['active'] = 0;
						} else {
							$subdomen_submenu['active'] = 1;
						}
						$subdomen_submenu->save();
					}
					$sort_key++;
				}
			}
		}

		echo 'ссылки обновлены';
	}
}
