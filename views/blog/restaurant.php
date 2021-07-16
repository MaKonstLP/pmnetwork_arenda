<?php
use frontend\modules\arenda\models\ElasticItems;

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

echo $this->render('//components/generic/restaurant_adv.twig', ['item' => $item]);
?>