<?php

namespace app\modules\arenda;


use Yii;
use common\models\Subdomen;
/**
 * svadbanaprirode module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\arenda\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $subdomen = explode('.', $_SERVER['HTTP_HOST'])[0];

        if($subdomen != 'arendazala' and $subdomen != 'arendazala_dev'){
            Yii::$app->params['subdomen'] = $subdomen;

            $subdomen_model = Subdomen::find()
                ->where(['alias' => $subdomen])
                ->one();

            if(!$subdomen_model)
                throw new \yii\web\NotFoundHttpException();
        }
        else{
            Yii::$app->params['subdomen'] = '';

            $subdomen_model = Subdomen::find()
                ->where(['alias' => ''])
                ->one();
        }

        if($subdomen_model){
            Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
            Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
            Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
            Yii::$app->params['subdomen_name'] = $subdomen_model->name;
            Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
            Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;
            Yii::$app->params['subdomen_phone'] = $subdomen_model->phone;
            Yii::$app->params['uploadFolder'] = 'upload';
        }

        $noindex_global = false;
        foreach ($_GET as $key => $value) {
            if ($key != 'page' && $key != 'q') {
                $noindex_global = true;
            }
        }
        Yii::$app->params['noindex_global'] = $noindex_global;
            
        //Yii::$app->setLayoutPath('@app/modules/svadbanaprirode/layouts');
        //Yii::$app->layout = 'svadbanaprirode';
        //$this->viewPath = '@app/modules/svadbanaprirode/views/';
        parent::init();
        //$this->viewPath = '@app/modules/svadbanaprirode/views/';


        // custom initialization code goes here
    }
}
