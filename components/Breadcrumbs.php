<?php

namespace frontend\modules\arenda\components;

use Yii;
use backend\models\Slices;
use frontend\modules\arenda\models\RestaurantsTypes;

class Breadcrumbs {
	public static function get_breadcrumbs($level, $slice_alias = false, $params = false) {
		switch ($level) {
			case 1:	
				$breadcrumbs=[
					'/' => 'Главная',
				];
				break;
			case 2:
				$breadcrumbs=[
					'/' => 'Главная',
					'/catalog/' => Slices::find()->where(['alias' => $slice_alias])->exists() ? Slices::find()->where(['alias' => $slice_alias])->one()->h1 : '',
				];
				break;
			case 3:
				$breadcrumbs=[
					'/blog/' => 'Статьи блога'
				];
				break;
			case 4:
				$breadcrumbs=[
					self::get_breadcrumbs(1)['/'],
					'/catalog/' => Breadcrumbs::getFilterRestTypes($params)
				];
				break;
			case 'item':
				$breadcrumbs=[
					self::get_breadcrumbs(1)['/'],
					'/catalog/' => 'Правильные площадки Москвы'
				];
				break;
		}
		return $breadcrumbs;
	}

	public static function getItemCrumb($item){
		$restType = count(explode(',', $item->type)) > 1 ? array_shift(explode(',', $item->type)) : $item->type;
		if (RestaurantsTypes::find()->where(['value' => $restType])->exists()){
			$sliceInfo = Slices::find()->where(['alias' => RestaurantsTypes::find()->where(['value' => $restType])->one()->alias])->one();
			$breadcrumbs = [
				'/' => 'Главная',
				"/catalog/{$sliceInfo->alias}/" => $sliceInfo->h1,
				
			];
			return $breadcrumbs;
		}
	}

	private static function getFilterRestTypes($params) {
		$restTypeList = [
			1 => 'Площадки',
			2 => 'Арт-площадки',
			3 => 'Летняя площадка',
			4 => 'Залы',
			5 => 'Банкетный зал',
			6 => 'Танцевальный зал',
			7 => 'Конференц-зал',
			8 => 'Кинозалы',
			9 => 'Актовые залы',
			10 => 'Залы в гостинице',
			11 => 'Лофт',
			12 => 'Заведения',
			13 => 'Рестораны',
			14 => 'Кафе',
			15 => 'Бары/пабы',
			16 => 'Клубы ',
			17 => 'Природа',
			18 => 'Терраса',
			19 => 'Веранда',
			20 => 'Шатры',
			21 => 'Коттеджи',
			// 22 => 'Усадьбы',
		];

		$restSpecList = [
			1 => 'День рождения',
			2 => 'Детский день рождения',
			3 => 'Свадьба',
			4 => 'Новый год',
			5 => 'Корпоратив',
			6 => 'Выпускной',
		];

		$crumbString = '';
		
		if (isset($params['rest_type'])){
			foreach ($params['rest_type'] as $key => $value){
				$crumbString .= $restTypeList[$value] . ', ';
			}
		}

		if (isset($params['prazdnik'])){
			foreach ($params['prazdnik'] as $key => $value){
				$crumbString .= $restSpecList[$value] . ', ';
			}
		}

		if ($crumbString !== ''){
			return substr($crumbString, 0, -2);
		}
		return 'Площадки';
	}
}
