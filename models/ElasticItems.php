<?php
namespace frontend\modules\arenda\models;

use Yii;
use common\models\Restaurants;
use common\models\RestaurantsTypes;
use common\models\RestaurantsSpec;
use yii\helpers\ArrayHelper;

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
            'restaurant_firework',
            'restaurant_name',
            'restaurant_slug',
            'restaurant_address',
            'restaurant_cover_url',
            'restaurant_latitude',
            'restaurant_longitude',
            'restaurant_own_alcohol',
            'restaurant_cuisine',
            'restaurant_parking',
            'restaurant_extra_services',
            'restaurant_payment',
            'restaurant_special',
            'restaurant_phone',
            'restaurant_location',
            'restaurant_types',
            'restaurant_spec',
            'restaurant_commission',
            'id',
            'gorko_id',
            'restaurant_id',
            'price',
            'capacity_reception',
            'capacity',
            'type',
            'rent_only',
            'banquet_price',
            'bright_room',
            'separate_entrance',
            'type_name',
            'name',
            'slug',
            'features',
            'cover_url',
            'images',
            'thumbs',
            'description'
        ];
    }

    public static function index() {
        return 'pmn_arenda_rooms';
    }
    
    public static function type() {
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
                    'restaurant_city_id'            => ['type' => 'integer'],
                    'restaurant_price'                 => ['type' => 'integer'],
                    'restaurant_min_capacity'          => ['type' => 'integer'],
                    'restaurant_max_capacity'          => ['type' => 'integer'],
                    'restaurant_district'              => ['type' => 'integer'],
                    'restaurant_parent_district'       => ['type' => 'integer'],
                    'restaurant_alcohol'               => ['type' => 'integer'],
                    'restaurant_firework'              => ['type' => 'integer'],
                    'restaurant_name'                  => ['type' => 'text'],
                    'restaurant_address'               => ['type' => 'text'],
                    'restaurant_slug'                  => ['type' => 'keyword'],
                    'restaurant_cover_url'             => ['type' => 'text'],
                    'restaurant_latitude'              => ['type' => 'text'],
                    'restaurant_longitude'             => ['type' => 'text'],
                    'restaurant_own_alcohol'           => ['type' => 'text'],
                    'restaurant_cuisine'               => ['type' => 'text'],
                    'restaurant_parking'               => ['type' => 'text'],
                    'restaurant_extra_services'        => ['type' => 'text'],
                    'restaurant_payment'               => ['type' => 'text'],
                    'restaurant_special'               => ['type' => 'text'],
                    'restaurant_phone'                 => ['type' => 'text'],
                    'restaurant_types'              => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_spec'                 => ['type' => 'nested', 'properties' => [
                        'id'                             => ['type' => 'integer'],
                        'name'                           => ['type' => 'text'],
                    ]],
                    'restaurant_location'              => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                    ]],
                    'restaurant_commission'            => ['type' => 'integer'],
                    'id'                    => ['type' => 'integer'],
                    'gorko_id'              => ['type' => 'integer'],
                    'restaurant_id'         => ['type' => 'integer'],
                    'price'                 => ['type' => 'integer'],
                    'capacity_reception'    => ['type' => 'integer'],
                    'capacity'              => ['type' => 'integer'],
                    'type'                  => ['type' => 'integer'],
                    'rent_only'             => ['type' => 'integer'],
                    'banquet_price'         => ['type' => 'integer'],
                    'bright_room'           => ['type' => 'integer'],
                    'separate_entrance'     => ['type' => 'integer'],
                    'type_name'             => ['type' => 'text'],
                    'name'                  => ['type' => 'text'],
                    'slug'                  => ['type' => 'keyword'],
                    'features'              => ['type' => 'text'],
                    'cover_url'             => ['type' => 'text'],
                    'description'           => ['type' => 'text'],
                    'images'                => ['type' => 'nested', 'properties' =>[
                        'id'                => ['type' => 'integer'],
                        'sort'              => ['type' => 'integer'],
                        'realpath'          => ['type' => 'text'],
                        'subpath'           => ['type' => 'text'],
                        'waterpath'         => ['type' => 'text'],
                        'timestamp'         => ['type' => 'text'],
                    ]],
                    'thumbs'                => ['type' => 'nested', 'properties' =>[
                        'id'                => ['type' => 'integer'],
                        'sort'              => ['type' => 'integer'],
                        'realpath'          => ['type' => 'text'],
                        'subpath'           => ['type' => 'text'],
                        'waterpath'         => ['type' => 'text'],
                        'timestamp'         => ['type' => 'text'],
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

    public static function refreshIndex() {
        $res = self::deleteIndex();
        $res = self::updateMapping();
        $res = self::createIndex();

        $connection = new \yii\db\Connection([
            'dsn'       => 'mysql:host=localhost;dbname=pmn_arenda',
            'username'  => 'root',
            'password'  => 'Gkcfmdsop',
            'charset'   => 'utf8mb4',
        ]);
        $connection->open();
        Yii::$app->set('db', $connection);

        $restaurants = Restaurants::find()
            ->with('rooms')
            ->limit(10000)
            ->all($connection);


        $restaurants_types = RestaurantsTypes::find()
            ->limit(100000)
            ->asArray()
            ->all($connection);
        $restaurants_types = ArrayHelper::index($restaurants_types, 'value');

        $restaurants_spec = RestaurantsSpec::find()
            ->limit(100000)
            ->asArray()
            ->all($connection);

        $restaurants_spec = ArrayHelper::index($restaurants_spec, 'id');


        // echo '<pre>';
        // print_r($restaurants_types);
        // exit;

        foreach ($restaurants as $restaurant) {

            foreach ($restaurant->rooms as $room) {
                $res = self::addRecord($room, $restaurant, $restaurants_types, $restaurants_spec);
            }            
        }
        echo 'Обновление индекса '. self::index().' '. self::type() .' завершено<br>';
    }

    public static function softRefreshIndex() {
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
        echo 'Обновление индекса '. self::index().' '. self::type() .' завершено<br>';
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

    public static function addRecord($room, $restaurant, $restaurants_types, $restaurants_spec){
        $isExist = false;
        
        try{
            $record = self::get($room->id);
            if(!$record){
                $record = new self();
                $record->setPrimaryKey($room->id);
            }
            else{
                $isExist = true;
            }
        }
        catch(\Exception $e){
            $record = new self();
            $record->setPrimaryKey($room->id);
        }

        $restaurant_spec_white_list = [1, 9, 11, 12, 15, 17];
        $restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);

        if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
            return 'Неподходящий тип мероприятия';
        }

        $restaurant_type_white_list = [31, 17, 2, 37, 34, 33, 27, 36, 1, 3, 4, 16, 30, 14, 15];
        $restaurant_type = explode(',', $restaurant->type);

        if (count(array_intersect($restaurant_type_white_list, $restaurant_type)) === 0) {
            return 'Неподходящий тип площадки';
        }

        if(count($room->images) == 0)
            return 0;

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
        $record->restaurant_firework = $restaurant->firework;
        $record->restaurant_name = $restaurant->name;
        $record->restaurant_address = $restaurant->address;
        $record->restaurant_cover_url = $restaurant->cover_url;
        $record->restaurant_latitude = $restaurant->latitude;
        $record->restaurant_longitude = $restaurant->longitude;
        $record->restaurant_own_alcohol = $restaurant->own_alcohol;
        $record->restaurant_cuisine = $restaurant->cuisine;
        $record->restaurant_parking = $restaurant->parking;
        $record->restaurant_extra_services = $restaurant->extra_services;
        $record->restaurant_payment = $restaurant->payment;
        $record->restaurant_special = $restaurant->special;
        $record->restaurant_phone = $restaurant->phone;
        $record->restaurant_commission = $restaurant->commission;
        
        //Тип помещения
        $restaurant_types = [];
        $restaurant_types_rest = explode(',', $restaurant->type);
        foreach ($restaurant_types_rest as $key => $value) {
            $restaurant_types_arr = [];
            $restaurant_types_arr['id'] = $value;
            $restaurant_types_arr['name'] = isset($restaurants_types[$value]['text']) ? $restaurants_types[$value]['text'] : '';
            array_push($restaurant_types, $restaurant_types_arr);
        }
        $record->restaurant_types = $restaurant_types;

        //Тип мероприятия
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
        $record->type = $room->type;
        $record->rent_only = $room->rent_only;
        $record->banquet_price = $room->banquet_price;
        $record->bright_room = $room->bright_room;
        $record->separate_entrance = $room->separate_entrance;
        $record->type_name = $room->type_name;
        $record->name = $room->name;
        $record->features = $room->features;
        $record->cover_url = $room->cover_url;

        $images = [];
        $thumbs = [];

        foreach ($room->images as $key => $image) {
            $image_arr = [];
            $image_arr['id'] = $image->id;
            $image_arr['sort'] = $image->sort;
            $image_arr['realpath'] = $image->realpath;
            $image_arr['subpath'] = $image->subpath;
            $image_arr['waterpath'] = $image->waterpath;
            $image_arr['timestamp'] = $image->timestamp;
            array_push($images, $image_arr);
            array_push($thumbs, $image_arr);
        }

        $record->images = $images;
        $record->images = $thumbs;

        // restaurant slug
        if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
            $record->restaurant_slug = $row['slug'];
        } else {
            $record->restaurant_slug = self::getTransliterationForUrl($restaurant->name);
            \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' =>  $record->restaurant_slug])->execute();
        }

        // room slug
        if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $room->gorko_id])->one()) {
            $record->slug = $row['slug'];
        } else {
            $slug = self::getTransliterationForUrl($room->name);
            $slug .= '-' . $restaurant->id;
            $record->slug = $slug;
            \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $room->gorko_id, 'slug' =>  $record->slug])->execute();
        }
        
        try{
            if(!$isExist){
                $result = $record->insert();
            }
            else{
                $result = $record->update();
            }
        }
        catch(\Exception $e){
            $result = false;
        }
        
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