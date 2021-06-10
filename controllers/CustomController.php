<?php
namespace app\modules\arenda\controllers;

use Yii;
use yii\web\Controller;
use common\models\FilterItems;
use common\models\Restaurants;
use common\models\Slices;
use common\models\SlicesExtended;
use common\models\RestaurantsSpec;
use common\models\elastic\ItemsFilterElastic;
use frontend\components\QueryFromSlice;
use frontend\modules\arenda\models\ElasticItems;
use frontend\modules\arenda\models\RestaurantsTypes;
use frontend\components\ParamsFromQuery;
use backend\models\Filter;

class CustomController extends Controller
{
  public function actionIndex()
  {
    return $this->render('index.twig');
  }
  public function actionCustom()
  {

    $elastic_model = new ElasticItems;
    // $item = $elastic_model::get($text_id);

    $item = ElasticItems::find()->query([
      'bool' => [
        'must' => [
          ['match' => ['id' => 256]],
        ],
      ]
    ])->one();

    // foreach (Slices::find()->where(['type' => 'rest_type'])->all() as $slice){
    //   if (RestaurantsTypes::find()->where(['text' => $slice->h1])->exists()){
    //     $restaurantsTypesItem = RestaurantsTypes::find()->where(['text' => $slice->h1])->one();
    //     $restaurantsTypesItem->alias = $slice->alias;
    //     $restaurantsTypesItem->save(false);
    //   }
    // }
    echo '<pre>';
    print_r($item);
    exit;
  }

  public function actionRefreshSlicesCount()
  {
    $filter_model = Filter::find()->with('items')->all();
		$slices_model = Slices::find()->all();
    $elastic_model = new ElasticItems;

    foreach (SlicesExtended::find()->where(['type' => 'prazdnik'])->all() as $eSlice){
      $eSlice->restaurants_count = 0;

      $slice_obj = new QueryFromSlice($eSlice->alias);
      $params = $this->parseGetQuery($slice_obj->params, $filter_model, $slices_model);
      $items = new ItemsFilterElastic($params['params_filter'], 100000, 1, false, 'rooms', $elastic_model);
      echo '<pre>';
      print_r($items);
      exit;
      // $eSlice->restaurants_count = 
      $slice->save(false);
    }
    echo 'Количество ресторанов у срезов по станциям метро обновлено';
    exit;
  }

  private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if(isset($getQuery['page'])){
			$return['page'] = $getQuery['page'];
		}
		else{
			$return['page'] = 1;
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);

		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		// $return['canonical'] = $temp_params->canonical;
		return $return;
	}

}