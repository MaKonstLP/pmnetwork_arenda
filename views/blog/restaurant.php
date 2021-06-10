<?php
use frontend\modules\arenda\models\ElasticItems;

$elastic_model = new ElasticItems;
$item = $elastic_model::get($text_id);

$item = ElasticItems::find()->query([
  'bool' => [
    'must' => [
      ['match' => ['id' => $text_id]],
    ],
  ]
])->one();

echo $this->render('//components/generic/restaurant_adv.twig', ['item' => $item]);
?>