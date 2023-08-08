<?php

namespace frontend\modules\arenda\models;

use Yii;
use common\models\Restaurants;
use common\models\RestaurantsTypes;
use yii\helpers\ArrayHelper;
use common\models\Subdomen;
use common\models\RestaurantsSpec;
use common\models\RestaurantsSpecial;
use common\models\RestaurantsExtra;
use common\models\RestaurantsLocation;
use common\models\ImagesModule;
use common\models\RoomsSpec;
use common\models\RestaurantsPremium;
use common\models\RoomsPremium;
use common\models\Slices;
use common\components\AsyncRenewImages;
use common\widgets\ProgressWidget;

class ElasticItems extends \yii\elasticsearch\ActiveRecord
{
	public function attributes()
	{
		return [
			'restaurant_id',
			'restaurant_city_id',
			'restaurant_gorko_id',
			'restaurant_price',
			'restaurant_min_capacity',
			'restaurant_max_capacity',
			'restaurant_district',
			'restaurant_parent_district',
			'restaurant_alcohol',
			'restaurant_alcohol_stock',
			'restaurant_firework',
			'restaurant_name',
			'restaurant_slug',
			'restaurant_address',
			'restaurant_cover_url',
			'restaurant_latitude',
			'restaurant_longitude',
			'restaurant_own_alcohol',
			'restaurant_cuisine',
			'restaurant_cuisine_child',
			'restaurant_cuisine_eu',
			'restaurant_cuisine_caucas',
			'restaurant_cuisine_ru',
			'restaurant_cuisine_ital',
			'restaurant_cuisine_georgian',
			'restaurant_cuisine_azerbaij',
			'restaurant_cuisine_mixed',
			'restaurant_cuisine_author',
			'restaurant_cuisine_american',
			'restaurant_cuisine_eng',
			'restaurant_cuisine_austrian',
			'restaurant_cuisine_japan',
			'restaurant_cuisine_uzbek',
			'restaurant_cuisine_china',
			'restaurant_cuisine_panasian',
			'restaurant_cuisine_east',
			'restaurant_cuisine_alpine',
			'restaurant_cuisine_fusion',
			'restaurant_parking',
			'restaurant_extra_services',
			'restaurant_photographer',
			'restaurant_videographer',
			'restaurant_leading',
			'restaurant_dj',
			'restaurant_live_music',
			'restaurant_cake',
			'restaurant_decor',
			'restaurant_payment',
			'restaurant_payment_model',
			'restaurant_special',
			'restaurant_welcome_zone',
			'restaurant_scene',
			'restaurant_wi_fi',
			'restaurant_outside_registration',
			'restaurant_music_equipment',
			'restaurant_projector',
			'restaurant_tv_screens',
			'restaurant_phone',
			'restaurant_location',
			'restaurant_types',
			'restaurant_spec',
			'restaurant_commission',
			'restaurant_rating',
			'restaurant_premium',
			'id',
			'gorko_id',
			'restaurant_id',
			'price',
			'capacity_reception',
			'capacity',
			'capacity_min',
			'type',
			'rent_only',
			'banquet_price',
			'banquet_price_min',
			'banquet_price_person',
			'bright_room',
			'separate_entrance',
			'type_name',
			'name',
			'slug',
			'features',
			'karaoke',
			'cover_url',
			'images',
			'description',
			'room_prices',
			'rent_room_only',
			'rent_only',
			'min_room_price',
			'max_room_price',
			'room_spec',
			'restaurant_rev_ya',
			'prazdnik_extra_options',
			'prazdnik_options',
			'rest_type_extra_options',
			'rest_type_options',
			'price_only_banket',
			'price_only_arenda',
		];
	}

	public static function index()
	{
		return 'pmn_arenda_rooms';
	}

	public static function type()
	{
		return 'items';
	}

	/**
	 * @return array This model's mapping
	 */
	public static function mapping()
	{
		return [
			static::type() => [
				'properties' => [
					'restaurant_id'                    => ['type' => 'integer'],
					'restaurant_gorko_id'              => ['type' => 'integer'],
					'restaurant_city_id'               => ['type' => 'integer'],
					'restaurant_price'                 => ['type' => 'integer'],
					'price_only_banket'                => ['type' => 'integer'],
					'price_only_arenda'                => ['type' => 'integer'],
					'restaurant_min_capacity'          => ['type' => 'integer'],
					'restaurant_max_capacity'          => ['type' => 'integer'],
					'restaurant_district'              => ['type' => 'integer'],
					'restaurant_parent_district'       => ['type' => 'integer'],
					'restaurant_rating'                => ['type' => 'integer'],
					'restaurant_alcohol'               => ['type' => 'integer'],
					'restaurant_alcohol_stock'         => ['type' => 'integer'],
					'restaurant_firework'              => ['type' => 'integer'],
					'restaurant_premium'               => ['type' => 'integer'],
					'restaurant_name'                  => ['type' => 'text'],
					'restaurant_address'               => ['type' => 'text'],
					'restaurant_slug'                  => ['type' => 'keyword'],
					'restaurant_cover_url'             => ['type' => 'text'],
					'restaurant_latitude'              => ['type' => 'text'],
					'restaurant_longitude'             => ['type' => 'text'],
					'restaurant_own_alcohol'           => ['type' => 'text'],
					'restaurant_cuisine'               => ['type' => 'text'],

					'restaurant_cuisine_child'         => ['type' => 'integer'],
					'restaurant_cuisine_eu'            => ['type' => 'integer'],
					'restaurant_cuisine_caucas'        => ['type' => 'integer'],
					'restaurant_cuisine_ru'            => ['type' => 'integer'],
					'restaurant_cuisine_ital'          => ['type' => 'integer'],
					'restaurant_cuisine_georgian'      => ['type' => 'integer'],
					'restaurant_cuisine_azerbaij'      => ['type' => 'integer'],
					'restaurant_cuisine_mixed'         => ['type' => 'integer'],
					'restaurant_cuisine_author'        => ['type' => 'integer'],
					'restaurant_cuisine_american'      => ['type' => 'integer'],
					'restaurant_cuisine_eng'           => ['type' => 'integer'],
					'restaurant_cuisine_austrian'      => ['type' => 'integer'],
					'restaurant_cuisine_japan'         => ['type' => 'integer'],
					'restaurant_cuisine_uzbek'         => ['type' => 'integer'],
					'restaurant_cuisine_china'         => ['type' => 'integer'],
					'restaurant_cuisine_panasian'      => ['type' => 'integer'],
					'restaurant_cuisine_east'          => ['type' => 'integer'],
					'restaurant_cuisine_alpine'        => ['type' => 'integer'],
					'restaurant_cuisine_fusion'        => ['type' => 'integer'],


					'restaurant_parking'               => ['type' => 'text'],
					'restaurant_extra_services'        => ['type' => 'text'],
					'restaurant_photographer'          => ['type' => 'integer'],
					'restaurant_videographer'          => ['type' => 'integer'],
					'restaurant_leading'               => ['type' => 'integer'],
					'restaurant_dj'                    => ['type' => 'integer'],
					'restaurant_live_music'            => ['type' => 'integer'],
					'restaurant_cake'                  => ['type' => 'integer'],
					'restaurant_decor'                 => ['type' => 'integer'],
					'restaurant_payment'               => ['type' => 'text'],
					'restaurant_payment_model'         => ['type' => 'integer'],
					'restaurant_special'               => ['type' => 'text'],
					'restaurant_welcome_zone'          => ['type' => 'integer'],
					'restaurant_scene'                 => ['type' => 'integer'],
					'restaurant_wi_fi'                 => ['type' => 'integer'],
					'restaurant_outside_registration'  => ['type' => 'integer'],
					'restaurant_music_equipment'       => ['type' => 'integer'],
					'restaurant_projector'             => ['type' => 'integer'],
					'restaurant_tv_screens'            => ['type' => 'integer'],
					'restaurant_phone'                 => ['type' => 'text'],
					'restaurant_types'                 => ['type' => 'nested', 'properties' => [
						'id'                               => ['type' => 'integer'],
						'name'                             => ['type' => 'text'],
						'link'                             => ['type' => 'text'],
					]],
					'restaurant_spec'                  => ['type' => 'nested', 'properties' => [
						'id'                                => ['type' => 'integer'],
						'name'                              => ['type' => 'text'],
					]],
					'restaurant_location'              => ['type' => 'nested', 'properties' => [
						'id'                               => ['type' => 'integer'],
					]],
					'restaurant_commission'            => ['type' => 'integer'],
					'id'                               => ['type' => 'integer'],
					'gorko_id'                         => ['type' => 'integer'],
					'restaurant_id'                    => ['type' => 'integer'],
					'price'                            => ['type' => 'integer'],
					'capacity_reception'               => ['type' => 'integer'],
					'capacity'                         => ['type' => 'integer'],
					'capacity_min'                         => ['type' => 'integer'],
					'type'                             => ['type' => 'integer'],
					'rent_only'                        => ['type' => 'integer'],
					'banquet_price'                    => ['type' => 'integer'],
					'banquet_price_min'                => ['type' => 'integer'],
					'banquet_price_person'             => ['type' => 'integer'],
					'bright_room'                      => ['type' => 'integer'],
					'separate_entrance'                => ['type' => 'integer'],
					'type_name'                        => ['type' => 'text'],
					'name'                             => ['type' => 'text'],
					'slug'                             => ['type' => 'keyword'],
					'features'                         => ['type' => 'text'],
					'karaoke'                          => ['type' => 'integer'],
					'cover_url'                        => ['type' => 'text'],
					'description'                      => ['type' => 'text'],
					'images'                           => ['type' => 'nested', 'properties' => [
						'id'                               => ['type' => 'integer'],
						'sort'                             => ['type' => 'integer'],
						'realpath'                         => ['type' => 'text'],
						'subpath'                          => ['type' => 'text'],
						'waterpath'                        => ['type' => 'text'],
						'timestamp'                        => ['type' => 'text'],
					]],
					'room_prices'                      => ['type' => 'nested', 'properties' => [
						'spec_id'                          => ['type' => 'integer'],
						'spec_name'                        => ['type' => 'text'],
						'price'                            => ['type' => 'integer'],
					]],
                    'rent_room_only'                   => ['type' => 'integer'],
                    'rent_only'                   => ['type' => 'integer'],
					'min_room_price'                   => ['type' => 'integer'],
					'max_room_price'                   => ['type' => 'integer'],
					'room_spec'                        => ['type' => 'nested', 'properties' => [
						'id'                               => ['type' => 'integer'],
						'prazdnik_id'                      => ['type' => 'integer'],
						'name'                             => ['type' => 'text'],
						'link'                             => ['type' => 'text'],
						'types'                            => ['type' => 'nested', 'properties' => [
							'id'                               => ['type' => 'integer'],
							'name'                             => ['type' => 'text'],
							'link'                             => ['type' => 'text'],
						]],
					]],
					'restaurant_rev_ya'                => ['type' => 'nested', 'properties' => [
						'id'                               => ['type' => 'long'],
						'rate'                             => ['type' => 'text'],
						'count'                            => ['type' => 'text'],
					]],
					'prazdnik_extra_options'           => ['type' => 'nested', 'properties' => [
						'options'                          => ['type' => 'text'],
					]],
					'prazdnik_options'                 => ['type' => 'nested', 'properties' => [
						'options'                          => ['type' => 'text'],
					]],
					'rest_type_extra_options'          => ['type' => 'nested', 'properties' => [
						'options'                          => ['type' => 'text'],
					]],
					'rest_type_options'                => ['type' => 'nested', 'properties' => [
						'options'                          => ['type' => 'text'],
					]],
				]
			],
		];
	}

	/**
	 * Set (update) mappings for this model
	 */
	public static function updateMapping()
	{
		$db = static::getDb();
		$command = $db->createCommand();
		$command->setMapping(static::index(), static::type(), static::mapping());
	}

	/**
	 * Create this model's index
	 */
	public static function createIndex()
	{
		$db = static::getDb();
		$command = $db->createCommand();
		$command->createIndex(static::index(), [
			'settings' => [
				'number_of_replicas' => 0,
				'number_of_shards' => 1,
			],
			'mappings' => static::mapping(),
		]);
	}

	/**
	 * Delete this model's index
	 */
	public static function deleteIndex()
	{
		$db = static::getDb();
		$command = $db->createCommand();
		$command->deleteIndex(static::index(), static::type());
	}

	public static function refreshIndex($params)
	{
		$res = self::deleteIndex();
		$res = self::updateMapping();
		$res = self::createIndex();
		$res = self::updateIndex($params);
	}

	public static function updateIndex($params)
	{
		$connection = new \yii\db\Connection($params['main_connection_config']);
		$connection->open();
		Yii::$app->set('db', $connection);

		$restaurants_types = RestaurantsTypes::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_types = ArrayHelper::index($restaurants_types, 'value');

		$restaurants_specials = RestaurantsSpecial::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_specials = ArrayHelper::index($restaurants_specials, 'value');

		$restaurants_extra = RestaurantsExtra::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_extra = ArrayHelper::index($restaurants_extra, 'value');

		$restaurants_spec = RestaurantsSpec::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_spec = ArrayHelper::index($restaurants_spec, 'id');

		$restaurants_location = RestaurantsLocation::find()
			->limit(100000)
			->asArray()
			->all();
		$restaurants_location = ArrayHelper::index($restaurants_location, 'value');

		$restaurants = Restaurants::find()
			->with('rooms')
			->with('imagesext')
			->with('subdomen')
			->with('yandexReview')
			->where(['active' => 1])
			// ->andWhere(['gorko_id' => 455577])
			->limit(100000);

//		echo '<pre>';
//		print_r($params);
//		die();

		if($params['gorko_id']){
			$restaurants->andWhere(['gorko_id' => $params['gorko_id']]);
		}

		$restaurants = $restaurants->all();

		$connection = new \yii\db\Connection($params['site_connection_config']);
		$connection->open();
		Yii::$app->set('db', $connection);

		$images_module = ImagesModule::find()
			->limit(500000)
			->asArray()
			->all();
		$images_module = ArrayHelper::index($images_module, 'gorko_id');

		$restaurants_premium = RestaurantsPremium::find()
			->where(['>', 'finish', time()])
			->limit(100000)
			->asArray()
			->all();
		$restaurants_premium = ArrayHelper::index($restaurants_premium, 'gorko_id');

		$rooms_premium = RoomsPremium::find()
			->where(['>', 'finish', time()])
			->limit(100000)
			->asArray()
			->all();
		$rooms_premium = ArrayHelper::index($rooms_premium, 'gorko_id');

		$slices = Slices::find()->all();

		$rest_count = count($restaurants);
		$rest_iter = 0;
		foreach ($restaurants as $restaurant) {
			$rooms_slug = [];
			$iterator = 1;
			foreach ($restaurant->rooms as $room) {
				$res = self::addRecord($room, $restaurant, $restaurants_types, $restaurants_spec, $restaurants_specials, $restaurants_extra, $restaurants_location, $images_module, $restaurants_premium, $rooms_premium, $params, $rooms_slug, $iterator, $slices);
				$rooms_slug = $res['rooms_slug'];
				$iterator = $res['iterator'];
			}
			echo ProgressWidget::widget(['done' => $rest_iter++, 'total' => $rest_count]);
		}
		echo 'Обновление индекса ' . self::index() . ' ' . self::type() . ' завершено'."\n";
	}

	public static function softRefreshIndex()
	{
		$restaurants = Restaurants::find()
			->with('rooms')
			->limit(100000)
			->where(['in_elastic' => 0, 'active' => 1])
			->all($connection);

		foreach ($restaurants as $restaurant) {
			foreach ($restaurant->rooms as $room) {
				$res = self::addRecord($room, $restaurant);
			}

			$restaurant->in_elastic = 1;
			$restaurant->save();
		}
		echo 'Обновление индекса ' . self::index() . ' ' . self::type() . ' завершено<br>';
	}

	public static function getTransliterationForUrl($name)
	{
		$latin = array('-', "Sch", "sch", 'Yo', 'Zh', 'Kh', 'Ts', 'Ch', 'Sh', 'Yu', 'ya', 'yo', 'zh', 'kh', 'ts', 'ch', 'sh', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', '', 'Y', '', 'E', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', '', 'y', '', 'e');
		$cyrillic = array(' ', "Щ", "щ", 'Ё', 'Ж', 'Х', 'Ц', 'Ч', 'Ш', 'Ю', 'я', 'ё', 'ж', 'х', 'ц', 'ч', 'ш', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Ь', 'Ы', 'Ъ', 'Э', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'ь', 'ы', 'ъ', 'э');
		return trim(
			preg_replace(
				"/(.)\\1+/",
				"$1",
				strtolower(
					preg_replace(
						"/[^a-zA-Z0-9-]/",
						'',
						str_replace($cyrillic, $latin, $name)
					)
				)
			),
			'-'
		);
	}

	public static function addRecord($room, $restaurant, $restaurants_types, $restaurants_spec, $restaurants_specials, $restaurants_extra, $restaurants_location, $images_module, $restaurants_premium, $rooms_premium, $params, $rooms_slug, $iterator, $slices)
	{
		$premium = isset($restaurants_premium[$restaurant->gorko_id]);


		$room_premium = isset($rooms_premium[$room->gorko_id]);

		$restaurant_spec_white_list = [1, 9, 11, 12, 15, 17];
		$restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);

		$restaurant_type_white_list = [25, 31, 17, 2, 37, 34, 33, 27, 36, 1, 3, 4, 16, 30, 14, 15];
		$restaurant_type = explode(',', $restaurant->type);

		$result = [
			'iterator' => $iterator,
			'rooms_slug' => $rooms_slug
		];

		if (!$premium && !$room_premium) {
			if (!$restaurant->commission) {
				return $result;
			}

			if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
				return $result;
			}

			if (count(array_intersect($restaurant_type_white_list, $restaurant_type)) === 0) {
				return $result;
			}
		}

		$isExist = false;

		try {
			$record = self::get($room->gorko_id);
			if (!$record) {
				$record = new self();
				$record->setPrimaryKey($room->gorko_id);
			} else {
				$isExist = true;
			}
		} catch (\Exception $e) {
			$record = new self();
			$record->setPrimaryKey($room->gorko_id);
		}

		$record->id  = $room->id;
		$record->restaurant_id = $restaurant->id;
		$record->restaurant_city_id = $restaurant->city_id;
		$record->restaurant_gorko_id = $restaurant->gorko_id;
		$record->restaurant_price = $restaurant->price;
		$record->restaurant_min_capacity = $restaurant->min_capacity;
		$record->restaurant_max_capacity = $restaurant->max_capacity;
		$record->restaurant_district = $restaurant->district;
		$record->restaurant_parent_district = $restaurant->parent_district;
		$record->restaurant_alcohol = $restaurant->alcohol;
		$record->restaurant_alcohol_stock = $restaurant->alcohol_stock;
		$record->restaurant_firework = $restaurant->firework;
		$record->restaurant_name = $restaurant->name;
		$record->restaurant_address = $restaurant->address;
		$record->restaurant_cover_url = $restaurant->cover_url;
		$record->restaurant_latitude = $restaurant->latitude;
		$record->restaurant_longitude = $restaurant->longitude;
		$record->restaurant_own_alcohol = $restaurant->own_alcohol;
		$record->restaurant_cuisine = $restaurant->cuisine;

		$rest_special_explode = array_map('trim', explode(',', $restaurant->special));
		foreach ($rest_special_explode as $key => $rest_special) {
			if (mb_strtolower($rest_special) == 'детское меню') {
				$rest_cuisine_child = 1;
			}
		}
		$record->restaurant_cuisine_child = isset($rest_cuisine_child) ? $rest_cuisine_child : '';

		$rest_cuisine_explode = array_map('trim', explode(',', $restaurant->cuisine));
		foreach ($rest_cuisine_explode as $key => $rest_cuisine) {
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
		$record->restaurant_cuisine_eu = isset($rest_cuisine_eu) ? $rest_cuisine_eu : '';
		$record->restaurant_cuisine_caucas = isset($rest_cuisine_caucas) ? $rest_cuisine_caucas : '';
		$record->restaurant_cuisine_ru = isset($rest_cuisine_ru) ? $rest_cuisine_ru : '';
		$record->restaurant_cuisine_ital = isset($rest_cuisine_ital) ? $rest_cuisine_ital : '';
		$record->restaurant_cuisine_georgian = isset($rest_cuisine_georgian) ? $rest_cuisine_georgian : '';
		$record->restaurant_cuisine_azerbaij = isset($rest_cuisine_azerbaij) ? $rest_cuisine_azerbaij : '';
		$record->restaurant_cuisine_mixed = isset($rest_cuisine_mixed) ? $rest_cuisine_mixed : '';
		$record->restaurant_cuisine_author = isset($rest_cuisine_author) ? $rest_cuisine_author : '';
		$record->restaurant_cuisine_american = isset($rest_cuisine_american) ? $rest_cuisine_american : '';
		$record->restaurant_cuisine_eng = isset($rest_cuisine_eng) ? $rest_cuisine_eng : '';
		$record->restaurant_cuisine_austrian = isset($rest_cuisine_austrian) ? $rest_cuisine_austrian : '';
		$record->restaurant_cuisine_japan = isset($rest_cuisine_japan) ? $rest_cuisine_japan : '';
		$record->restaurant_cuisine_uzbek = isset($rest_cuisine_uzbek) ? $rest_cuisine_uzbek : '';
		$record->restaurant_cuisine_china = isset($rest_cuisine_china) ? $rest_cuisine_china : '';
		$record->restaurant_cuisine_panasian = isset($rest_cuisine_panasian) ? $rest_cuisine_panasian : '';
		$record->restaurant_cuisine_east = isset($rest_cuisine_east) ? $rest_cuisine_east : '';
		$record->restaurant_cuisine_alpine = isset($rest_cuisine_alpine) ? $rest_cuisine_alpine : '';
		$record->restaurant_cuisine_fusion = isset($rest_cuisine_fusion) ? $rest_cuisine_fusion : '';

		$record->restaurant_parking = $restaurant->parking;
		$record->restaurant_extra_services = $restaurant->extra_services;

		$rest_extra_serv_explode = array_map('trim', explode(',', $restaurant->extra_services));
		$rest_photographer = ''; //Фотограф
		$rest_videographer = ''; //Видеограф
		$rest_leading = ''; //Ведущий
		$rest_dj = ''; //Dj
		$rest_live_music = ''; //Живая музыка
		$rest_cake = ''; //Торт
		$rest_decor = ''; //Оформление
		foreach ($rest_extra_serv_explode as $key => $rest_extra_serv) {
			switch ($rest_extra_serv) {
				case 'Фотограф':
					$rest_photographer = 1;
					break;
				case 'Видеограф':
					$rest_videographer = 1;
					break;
				case 'Ведущий':
					$rest_leading = 1;
					break;
				case 'Dj':
					$rest_dj = 1;
					break;
				case 'Живая музыка':
					$rest_live_music = 1;
					break;
				case 'Торт':
					$rest_cake = 1;
					break;
				case 'Оформление':
					$rest_decor = 1;
					break;
			}
		}
		$record->restaurant_photographer = $rest_photographer;
		$record->restaurant_videographer = $rest_videographer;
		$record->restaurant_leading = $rest_leading;
		$record->restaurant_dj = $rest_dj;
		$record->restaurant_live_music = $rest_live_music;
		$record->restaurant_cake = $rest_cake;
		$record->restaurant_decor = $rest_decor;

		$record->restaurant_payment = $restaurant->payment;
		$record->restaurant_payment_model = $room->payment_model;
		$record->restaurant_special = $restaurant->special;

		$rest_special_explode = array_map('trim', explode(',', $restaurant->special));
		$rest_welcome_zone = ''; //Велком зона
		$rest_scene = ''; //Сцена
		$rest_wi_fi = ''; //Wi-Fi / интернет
		$rest_outside_registration = ''; //Выездная регистрация
		$rest_music_equipment = ''; //Музыкальное оборудование
		$rest_projector = ''; //Проектор
		$rest_tv_screens = ''; //TV экраны
		foreach ($rest_special_explode as $key => $rest_special) {
			switch ($rest_special) {
				case 'Велком зона':
					$rest_welcome_zone = 1;
					break;
				case 'Сцена':
					$rest_scene = 1;
					break;
				case 'Wi-Fi / интернет':
					$rest_wi_fi = 1;
					break;
				case 'Выездная регистрация':
					$rest_outside_registration = 1;
					break;
				case 'Музыкальное оборудование':
					$rest_music_equipment = 1;
					break;
				case 'Проектор':
					$rest_projector = 1;
					break;
				case 'TV экраны':
					$rest_tv_screens = 1;
					break;
			}
		}
		$record->restaurant_welcome_zone = $rest_welcome_zone;
		$record->restaurant_scene = $rest_scene;
		$record->restaurant_wi_fi = $rest_wi_fi;
		$record->restaurant_outside_registration = $rest_outside_registration;
		$record->restaurant_music_equipment = $rest_music_equipment;
		$record->restaurant_projector = $rest_projector;
		$record->restaurant_tv_screens = $rest_tv_screens;

		switch ($restaurant->gorko_id) {
			case 479393:
				$record->restaurant_phone = '+7 995 697-32-60';
				break;
			case 476597:
				$record->restaurant_phone = '+7 927 795-63-63';
				//$record->restaurant_phone = '+7 960 810-73-70';
				break;
			case 441099:
				$record->restaurant_phone = '+7 930 036-84-71';
				//$record->restaurant_phone = '+7 964 264-89-04';
				break;
			case 449941:
				$record->restaurant_phone = '+7 915 612-15-15';
				break;
			case 479915:
				$record->restaurant_phone = '+7 904 078-77-55';
				break;
			case 425325:
				$record->restaurant_phone = '+7 993 007-79-37';
				break;
			case 477247:
				$record->restaurant_phone = '+7 382 241-11-33';
				break;
			case 455577:
				$record->restaurant_phone = '+7 923 775-08-03';
				break;
			case 467041:
				$record->restaurant_phone = '+7 978 770-83-95';
				break;
			case 483343:
				$record->restaurant_phone = '+7 963 716-59-17';
				break;
			case 25631:
				$record->restaurant_phone = '+7 964 102-64-81';
				break;
			default:
				$record->restaurant_phone = $restaurant->phone;
				break;
		}
		$record->restaurant_commission = $restaurant->commission;
		$restaurant->rating ? $record->restaurant_rating = $restaurant->rating : $record->restaurant_rating = 90;

		//Отзывы с Яндекса из общей базы
		$reviews = [];
		if (isset($restaurant->yandexReview)) {
			$reviews['id'] = $restaurant->yandexReview['rev_ya_id'];
			$reviews['rate'] = $restaurant->yandexReview['rev_ya_rate'];
			$reviews['count'] = $restaurant->yandexReview['rev_ya_count'];
		}
		$record->restaurant_rev_ya = $reviews;


		//Локальный премиум
		$record->restaurant_premium = 0;
		if ($premium || $room_premium)
			$record->restaurant_premium = 1;

		//соотвествие id в таблице filter_items и restaurants_types
		$type_arr = [
			'2' => '5', //Банкетный зал
			'1' => '13', //Ресторан
			'30' => '18', //Терраса
			'34' => '7', //Конференц-зал
			'30' => '19', //Веранда
			'25' => '10', //Гостиница / Отель
			'14' => '20', //Шатер
			'17' => '3', //Летняя площадка
			'15' => '21', //Коттедж
			'16' => '16', //Ночной клуб
			'3' => '14', //Кафе
			'4' => '15', //Бар
			'36' => '11', //Лофт
			'31' => '2', //Арт-пространство
			'27' => '9', //Актовый зал
			'33' => '8', //Кинозал
			'37' => '6', //Танцевальный зал
		];
		//Тип помещения
		$premium_types = [
			1 => 'Ресторан',
			2 => 'Банкетный зал'
		];
		$restaurant_types = [];
		$restaurant_types_rest = explode(',', $restaurant->type);
		foreach ($restaurant_types_rest as $key => $value) {
			$restaurant_types_arr = [];
			$restaurant_types_arr['id'] = $value;
			$restaurant_types_arr['name'] = isset($restaurants_types[$value]['text']) ? $restaurants_types[$value]['text'] : '';

			foreach ($slices as $slice) {
				$slice_params = json_decode($slice['params'], true);
				$rest_type_id = isset($type_arr[$value]) ? $type_arr[$value] : '';

				if (count($slice_params) == 1 && isset($slice_params['rest_type']) && $slice_params['rest_type'] == $rest_type_id) {
					$restaurant_types_arr['link'] = $slice['alias'];
				}
			}

			array_push($restaurant_types, $restaurant_types_arr);
		}
		if ($premium) {
			foreach ($premium_types as $premium_type => $premium_type_text) {
				if (!in_array($premium_type, $restaurant_types_rest)) {
					$restaurant_types_arr = [];
					$restaurant_types_arr['id'] = $premium_type;
					$restaurant_types_arr['name'] = $premium_type_text;

					foreach ($slices as $slice) {
						$slice_params = json_decode($slice['params'], true);
						$rest_type_id = isset($type_arr[$premium_type]) ? $type_arr[$premium_type] : '';

						if (count($slice_params) == 1 && isset($slice_params['rest_type']) && $slice_params['rest_type'] == $rest_type_id) {
							$restaurant_types_arr['link'] = $slice['alias'];
						}
					}

					array_push($restaurant_types, $restaurant_types_arr);
				}
			}
		}

		//добавление кастомного типа "Усадьба", если в названии зала или ресторана присутствует слово "усадьба"
		/* if (str_contains(mb_strtolower($room->name), 'усадьба') || str_contains(mb_strtolower($restaurant->name), 'усадьба')) {
			$restaurant_types_arr = [];
			$restaurant_types_arr['id'] = 100;
			$restaurant_types_arr['name'] = 'Усадьба';
			array_push($restaurant_types, $restaurant_types_arr);
		} */

		$record->restaurant_types = $restaurant_types;

		//Тип мероприятия ресторана
		$restaurant_spec = [];

		foreach ($restaurant_spec_rest as $key => $value) {
			$restaurant_spec_arr = [];
			$restaurant_spec_arr['id'] = $value;
			$restaurant_spec_arr['name'] = isset($restaurants_spec[$value]['name']) ? $restaurants_spec[$value]['name'] : '';
			array_push($restaurant_spec, $restaurant_spec_arr);
		}

		$record->restaurant_spec = $restaurant_spec;

		//Тип локации
		$restaurant_location = [];
		$restaurant_location_rest = explode(',', $restaurant->location);
		foreach ($restaurant_location_rest as $key => $value) {
			$restaurant_location_arr = [];
			$restaurant_location_arr['id'] = $value;
			array_push($restaurant_location, $restaurant_location_arr);
		}
		$record->restaurant_location = $restaurant_location;

		$record->id = $room->id;
		$record->gorko_id = $room->gorko_id;
		$record->restaurant_id = $room->restaurant_id;
		$record->price = $room->price;
		$record->capacity_reception = $room->capacity_reception;
		$record->capacity = $room->capacity;
		$record->capacity_min = $room->capacity_min;
		$record->type = $room->type;
		$record->rent_only = $room->rent_only;
		$record->banquet_price = $room->banquet_price;
		$record->banquet_price_min = $room->banquet_price_min;
		$record->banquet_price_person = $room->banquet_price_person;
		$record->bright_room = $room->bright_room;
		$record->separate_entrance = $room->separate_entrance;
		$record->type_name = $room->type_name;
		$record->name = $room->name;
		$record->features = $room->features;
		$record->karaoke = str_contains(mb_strtolower($room->name), 'караоке') ? 1 : '';
		$record->cover_url = $room->cover_url;

		//Картинки залов
		$images = [];
		$group = array();
		foreach ($restaurant->imagesext as $value) {
			$group[$value['room_id']][] = $value;
		}
		$images_sorted = array();
		$room_ids = array();
		foreach ($group as $room_id => $images_ext) {
			$room_ids[] = $room_id;
			foreach ($images_ext as $image) {
				$images_sorted[$room_id][$image['event_id']][] = $image;

				//устанавливаем сортировку картинок зала как на горько
				if ($room_id == 266201) {
					ArrayHelper::multisort($images_sorted[$room_id][$image['event_id']], ['sort'], [SORT_ASC]);
				}
				if ($room_id == 280053) {
					ArrayHelper::multisort($images_sorted[$room_id][$image['event_id']], ['sort'], [SORT_ASC]);
				}
			}
		}
		$specs = [1, 0];
		$image_flag = false;
		foreach ($specs as $spec) {
			for ($i = 0; $i < 20; $i++) {
				if (isset($images_sorted[$room->gorko_id]) && isset($images_sorted[$room->gorko_id][$spec]) && isset($images_sorted[$room->gorko_id][$spec][$i])) {
					$image = $images_sorted[$room->gorko_id][$spec][$i];
					$image_arr = [];
					$image_arr['id'] = $image['gorko_id'];
					$image_arr['sort'] = $image['sort'];
					$search = ['lh3.googleusercontent.com', 'nocdn.gorko.ru'];
					// $image_arr['realpath'] = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $image['path']);
					$image_arr['realpath'] = str_replace($search, 'img.arendazala.net', $image['path']);
					if (isset($images_module[$image['gorko_id']])) {
						// $image_arr['subpath']   = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $images_module[$image['gorko_id']]['subpath']);
						// $image_arr['waterpath'] = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $images_module[$image['gorko_id']]['waterpath']);
						// $image_arr['timestamp'] = str_replace('lh3.googleusercontent.com', 'img.arendazala.net', $images_module[$image['gorko_id']]['timestamp']);
						$image_arr['subpath']   = str_replace($search, 'img.arendazala.net', $images_module[$image['gorko_id']]['subpath']);
						$image_arr['waterpath'] = str_replace($search, 'img.arendazala.net', $images_module[$image['gorko_id']]['waterpath']);
						$image_arr['timestamp'] = str_replace($search, 'img.arendazala.net', $images_module[$image['gorko_id']]['timestamp']);
					} else {
						$queue_id = Yii::$app->queue->push(new AsyncRenewImages([
							'gorko_id'      => $image['gorko_id'],
							'params'        => $params,
							'rest_flag'     => false,
							'rest_gorko_id' => $restaurant->gorko_id,
							'room_gorko_id' => $room->gorko_id,
							'elastic_index' => static::index(),
							'elastic_type'  => 'room',
						]));
					}
					array_push($images, $image_arr);
				}
				if (count($images) > 19) {
					$image_flag = true;
					break;
				}
			}
			if ($image_flag) break;
		}
		if (count($images) == 0)
			return $result;

		$record->images = $images;

		// restaurant slug
		//if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
		//	$record->restaurant_slug = $row['slug'];
		//} else {
		//	$record->restaurant_slug = self::getTransliterationForUrl($restaurant->name);
		//	\Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' =>  $record->restaurant_slug])->execute();
		//}

		// room slug
		if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $room->gorko_id])->one()) {
			if (in_array($row['slug'], $rooms_slug)) {
				$slug = $row['slug'] . '-' . $iterator;
				$iterator++;
				$rooms_slug[] = $slug;
				Yii::$app->db->createCommand()
					->update('restaurant_slug', ['slug' => $slug], 'gorko_id = ' . $room->gorko_id)
					->execute();
			} else {
				$record->slug = $row['slug'];
				$rooms_slug[] = $row['slug'];
			}
		} else {
			$slug = self::getTransliterationForUrl($room->name);
			$slug .= '-' . $restaurant->id;
			if (in_array($slug, $rooms_slug)) {
				$slug = $slug . '-' . $iterator;
				$iterator++;
				$rooms_slug[] = $slug;
			} else {
				$rooms_slug[] = $slug;
			}
			$record->slug = $slug;
			\Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $room->gorko_id, 'slug' =>  $record->slug])->execute();
		}



		// Цены для разных типов мероприятий
		$room_specs_model = RoomsSpec::find()
			->limit(100000)
			->where(['room_id' => $room->id])
			->all();
		//соотвествие id в таблице filter_items и restaurants_spec
		$prazdnik_arr = [
			'9' => '1', //День рождения
			'12' => '2', //Детский день рождения
			'1' => '3', //Свадьба
			'17' => '4', //Новый год
			'15' => '5', //Корпоратив
			'11' => '6', //Выпускной
		];
		$room_prices_arr = [];
		if (isset($room_specs_model) && !empty($room_specs_model)) {
			foreach ($room_specs_model as $key => $spec_price) {
				$spec_model = RestaurantsSpec::find()->where(['id' => $spec_price['spec_id']])->one();
				$spec_name = $spec_model['name'];
				$room_prices = [];
				$room_prices['spec_id'] = $spec_price['spec_id'];
				$room_prices['prazdnik_id'] = isset($prazdnik_arr[$spec_price['spec_id']]) ? $prazdnik_arr[$spec_price['spec_id']] : '';
				$room_prices['spec_name'] = $spec_name;
				$room_prices['price'] = !empty($spec_price['price']) ? $spec_price['price'] : $room->price;

				array_push($room_prices_arr, $room_prices);
			}
		}
		$record->room_prices = $room_prices_arr;
		$record->rent_room_only = $room->rent_room_only;
		$record->rent_only = $room->rent_only;

		$min_price = 99999;
		$max_price = 0;
		foreach ($room_prices_arr as $room_price) {
			if (isset($room_price['price']) && !empty($room_price['price'])) {
				if ($room_price['price'] < $min_price) {
					$min_price = $room_price['price'];
				}
				if ($room_price['price'] > $max_price) {
					$max_price = $room_price['price'];
				}
			}
		}
		$record->min_room_price = $min_price != 99999 ? $min_price : '';
		$record->max_room_price = $max_price ? $max_price : '';

        //новая цена
        switch ($room->payment_model) {
            case 0:
                $price_only_banket = $room->price;
                $price_only_arenda = 0;
                break;
            case 1:
                $price_only_banket = $room->banquet_price_person;
                $price_only_arenda = 0;
                break;
            case 2:
                $price_only_banket = $room->price;
                $price_only_arenda = $room->rent_room_only;
                break;
            case 3:
                $price_only_banket = 0;
                $price_only_arenda = $room->rent_room_only;
                break;
        }
        $record->price_only_banket = $price_only_banket;
        $record->price_only_arenda = $price_only_arenda;

		//Тип мероприятия зала
		$room_spec = [];
		if (isset($room_specs_model) && !empty($room_specs_model)) {
			foreach ($room_specs_model as $room_spec_model) {
				if (isset($prazdnik_arr[$room_spec_model->spec['id']])) { //оставляем только 6 типов мероприятий(ДР, Детский ДР, Свадьба, Новый год, Корпоратив, Выпускной)
					$room_spec_arr = [];
					$room_spec_arr['id'] = $room_spec_model->spec['id'];
					$room_spec_arr['prazdnik_id'] = isset($prazdnik_arr[$room_spec_model->spec['id']]) ? $prazdnik_arr[$room_spec_model->spec['id']] : '';
					$room_spec_arr['name'] = isset($room_spec_model->spec['name']) ? $room_spec_model->spec['name'] : '';

					//добавляем ссылку на срез для типа мероприятия
					foreach ($slices as $slice) {
						$slice_params = json_decode($slice['params'], true);

						if (count($slice_params) == 1 && isset($slice_params['prazdnik']) && $slice_params['prazdnik'] == $room_spec_arr['prazdnik_id']) {
							$room_spec_arr['link'] = $slice['alias'];
						}
					}

					//добавляем типы заведения к данного типу мероприятия и ссылку на срез
					foreach ($restaurant_types as $key => $type) {
						$rest_type_id = isset($type_arr[$type['id']]) ? $type_arr[$type['id']] : '';

						$room_spec_arr['types'][$key]['id'] = $type['id'];
						$room_spec_arr['types'][$key]['name'] = $type['name'];
						foreach ($slices as $slice) {
							$slice_params = json_decode($slice['params'], true);

							if (
								count($slice_params) == 2
								&& isset($slice_params['prazdnik']) && $slice_params['prazdnik'] == $room_spec_arr['prazdnik_id']
								&& isset($slice_params['rest_type']) && $slice_params['rest_type'] == $rest_type_id
							) {
								$room_spec_arr['types'][$key]['link'] = $slice['alias'];
							} elseif (count($slice_params) == 1 && isset($slice_params['rest_type']) && $slice_params['rest_type'] == $rest_type_id) {
								$room_spec_arr['types'][$key]['link'] = $slice['alias'];
							}
						}
					}

					array_push($room_spec, $room_spec_arr);
				}
			}
		}
		$record->room_spec = $room_spec;



		//настройка релевантных опций для типов мероприятий 
		$extra_options_prazdnik_ids = [
			1 => 'День рождения',
			5 => 'Корпоратив',
			4 => 'Новый год',
			3 => 'Свадьбу',
			6 => 'Выпускной',
			2 => 'Детский день рождения',
		];
		// $restaurant_extra_services = $restaurant->extra_services;
		$rest_extra_serv_explode = array_map('trim', explode(',', $restaurant->extra_services));

		$extra_options_assoc_arr = [
			'ведущий (шоу-программа)' => 'Ведущий',
			'живая музыка' => 'Живая музыка',
			'торт' => 'Торт',
			'оформление зала на *Тип мероприятия*' => 'Оформление',
			'фотограф (фотосессия)' => 'Фотограф',
			'видеограф' => 'Видеограф',
			'DJ' => 'Dj',
		];

		// $restaurant_special = $restaurant->special;
		$rest_special_explode = array_map('trim', explode(',', $restaurant->special));

		$options_assoc_arr = [
			'сцена' => 'Сцена',
			'выездная регистрация' => 'Выездная регистрация',
			'велком зона' => 'Велком зона',
			'проектор' => 'Проектор',
			'TV экраны' => 'TV экраны',
			'Wi-Fi / интернет' => 'Wi-Fi / интернет',
			'музыкальное оборудование' => 'Музыкальное оборудование',
			'детское меню' => 'Детское меню',
		];

		$prazdnik_extra_options = [];
		$prazdnik_options = [];

		foreach ($extra_options_prazdnik_ids as $prazdnik_id => $prazdnik_name) {
			//выбор релевантных опций заданных в таблице в БД ("За доп. плату")
			$prazdnik_extra_options_model = SlicesExtraOptionVia::find()
				->where(['prazdnik_id' => $prazdnik_id])
				->with('options')
				->all();

			//сравнение опций ресторана с заданными в табилце в БД, если есть совпадения, то добавляются
			foreach ($prazdnik_extra_options_model as $key => $option) {
				foreach ($rest_extra_serv_explode as $key => $rest_extra_service) {
					if (
						isset($extra_options_assoc_arr[$option['options']['name']])
						&& $extra_options_assoc_arr[$option['options']['name']] == $rest_extra_service
					) {
						if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
							if ($option['options']['name'] == 'оформление зала на *Тип мероприятия*') {
								$prazdnik_extra_options[$prazdnik_id]['options'] .= ', оформление зала на ' . $prazdnik_name;
							} else {
								$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
							}
						} else {
							if ($option['options']['name'] == 'оформление зала на *Тип мероприятия*') {
								$prazdnik_extra_options[$prazdnik_id]['options'] = 'оформление зала на ' . $prazdnik_name;
							} else {
								$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
							}
						}
					}
				}

				//караоке
				if ($option['extra_option_id'] == 3 && str_contains(mb_strtolower($room->name), 'караоке')) {
					if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
						$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
					} else {
						$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
					}
				}

				//фуршет
				if ($option['extra_option_id'] == 5 && isset($room_spec) && !empty($room_spec)) {
					foreach ($room_spec as $key => $r_spec) {
						if ($r_spec['name'] == 'Фуршет') {
							if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
								$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
							} else {
								$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
							}

							break;
						}
					}
				}

				//у воды
				if ($option['extra_option_id'] == 7 && isset($restaurant_location_rest) && !empty($restaurant_location_rest)) {
					foreach ($restaurant_location_rest as $rest_location_id) {
						if ($rest_location_id == 1 || $rest_location_id == 2 || $rest_location_id == 7) {
							if (isset($prazdnik_extra_options[$prazdnik_id]['options']) && !empty($prazdnik_extra_options[$prazdnik_id]['options'])) {
								$prazdnik_extra_options[$prazdnik_id]['options'] .= ', ' . $option['options']['name'];
							} else {
								$prazdnik_extra_options[$prazdnik_id]['options'] = $option['options']['name'];
							}

							break;
						}
					}
				}
			}

			//выбор релевантных опций заданных в таблице в БД ("Что есть")
			$prazdnik_options_model = SlicesOptionVia::find()
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
		$record->prazdnik_extra_options = $prazdnik_extra_options;
		$record->prazdnik_options = $prazdnik_options;

		//настройка релевантных опций для типов площадки
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
			//выбор релевантных опций заданных в таблице в БД ("За доп. плату")
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

				//караоке
				if ($option['extra_option_id'] == 3 && str_contains(mb_strtolower($room->name), 'караоке')) {
					if (isset($rest_type_extra_options[$rest_type_id]['options']) && !empty($rest_type_extra_options[$rest_type_id]['options'])) {
						$rest_type_extra_options[$rest_type_id]['options'] .= ', ' . $option['options']['name'];
					} else {
						$rest_type_extra_options[$rest_type_id]['options'] = $option['options']['name'];
					}
				}

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

			//выбор релевантных опций заданных в таблице в БД ("Что есть")
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
		}
		$record->rest_type_extra_options = $rest_type_extra_options;
		$record->rest_type_options = $rest_type_options;





		try {
			if (!$isExist) {
				$result = $record->insert();
			} else {
				$result = $record->update();
			}
		} catch (\Exception $e) {
			$result = false;
		}

		$result = [
			'iterator' => $iterator,
			'rooms_slug' => $rooms_slug
		];

		return $result;
	}

	public static function updateDocument($data, $id, $options = [])
	{
		$db = static::getDb();
		$command = $db->createCommand();
		if ($command->exists(static::index(), static::type(), $id)) {
			$options['retry_on_conflict'] = 3;
			$command->update(static::index(), static::type(), $id, $data, $options);
		}

		gc_collect_cycles();
	}
}
