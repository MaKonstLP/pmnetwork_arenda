<?php

use frontend\modules\arenda\models\ElasticItems;
use common\models\Subdomen;

$elastic_model = new ElasticItems;
// $item = $elastic_model::get($text_id);

$slug = str_replace('hall-', '', $text_id);
$item = ElasticItems::find()->query([
	'bool' => [
		'must' => [
			['match' => ['slug' => $slug]],
		],
	]
])->one();


if (isset($item) && !empty($item)) {

	$subdomen_model = Subdomen::find()->where(['city_id' => $item['restaurant_city_id']])->one();
	$subdomen = $subdomen_model['alias'];

	echo $this->render('//components/generic/restaurant_adv.twig', [
		'item' => $item,
		'subdomen' => $subdomen,
	]);
}
