<?php
namespace app\modules\arenda\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\modules\arenda\models\ElasticItems;
use common\widgets\FilterWidget;
use common\models\elastic\ItemsWidgetElastic;
use common\models\Seo;
use common\models\Filter;
use common\models\Pages;
use common\models\Slices;
use common\models\SlicesExtended;
use common\models\RestaurantsSpec;

class SiteController extends Controller
{

    public function actionIndex()
    {
        $elastic_model = new ElasticItems;
        $filter_model = Filter::find()->with('items')->all();
        $slices_model = Slices::find()->all();

        $seo = (new Seo('index'))->seo;
        $this->setSeo($seo);

        $filter = FilterWidget::widget([
            'filter_active' => [],
            'filter_model' => $filter_model
        ]);

        $aggs = ElasticItems::find()->limit(0)->query(
            ['bool' => ['must' => ['match' => ['restaurant_city_id' => Yii::$app->params['subdomen_id']]]]]
          )
            ->addAggregate('specs', [
                'nested' => [
                    'path' => 'restaurant_spec',
                ],
                'aggs' => [
                    'ids' => [
                        'terms' => [
                            'field' => 'restaurant_spec.id',
                            'size' => 10000,
                        ]
                    ]
                ]
            ])->search();
      
          $slicesForTag = array_reduce($aggs['aggregations']['specs']['ids']['buckets'], function ($acc, $item){
            if (
              $item['doc_count'] > 3/* && count($acc) < 5*/
              && ($restTypeSlice = RestaurantsSpec::find()->with('slice')->where(['id' => intval($item['key'])])->one())
              && ($sliceObj = $restTypeSlice->slice)
          ) {
              $acc[] = [
                  'alias' => $sliceObj->alias,
                  'text' => $sliceObj->h1,
                  'count' => $item['doc_count']
              ];
          }
          return $acc;
          }, []);
      

        $slicesForListing = SlicesExtended::find()
            ->where(['alias' => [
                                'banketnyy-zal', 
                                'konferenc-zal', 
                                'restorany', 
                                'den-rojdeniya', 
                                'vypusknoy', 
                                'kafe', 
                                'svadba', 
                                'korporativ'
                                ]
            ])->all();    

        // echo '<pre>';
        // print_r($aggs);
        // exit;

        return $this->render('index.twig', [
            'filter' => $filter,
            'seo' => $seo,
            'slices_for_tag' => $slicesForTag,
            'slices_for_listing' => $slicesForListing,
        ]);
    }

    public function actionError()
    {
        $slicesForListing = SlicesExtended::find()
            ->where(['alias' => [
                                'banketnyy-zal', 
                                'konferenc-zal', 
                                'restorany', 
                                'den-rojdeniya', 
                                'vypusknoy', 
                                'kafe', 
                                'svadba', 
                                'korporativ'
                                ]
            ])->all();

        return $this->render('404.twig', [
            'slices_for_listing' => $slicesForListing,
        ]);
    }

    public function actionRobots()
    {
        header('Content-type: text/plain');
        if(Yii::$app->params['subdomen_alias']){
            $subdomen_alias = Yii::$app->params['subdomen_alias'].'.';
        }
        else{
            $subdomen_alias = '';
        }
        echo 'User-agent: *
Disallow: *?*
Allow: /catalog/?page=
Allow: */css/
Allow: */js/
Disallow: *rest_type=
Disallow: *prazdnik=
Sitemap: https://' . $subdomen_alias . 'arendazala.net/sitemap/';
        exit;
    }


    private function setSeo($seo){
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
    }
}
