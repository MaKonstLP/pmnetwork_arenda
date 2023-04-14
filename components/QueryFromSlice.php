<?php

namespace frontend\modules\arenda\components;

use Yii;
use yii\base\BaseObject;
use backend\models\Slices;
use backend\models\SlicesVia;

class QueryFromSlice extends BaseObject
{

	public $params;
	public $seo;
	public $flag = false;
	public $slice_model = null;
	public $slices_top;
	public $slices_bot;
	public $type;

	public function __construct($slice)
	{
		$this->slice_model = $slice_model = Slices::find()->where(['alias' => $slice])->one();
		if ($slice_model) {
			$slices_top = [];
			// $slices_bot = [];

			$slices_top_model = SlicesVia::find()->where(['slice' => $slice_model['id'], 'type' => 0])->all();
			foreach ($slices_top_model as $item) {
				$slice_name = Slices::getSliceNameById($item['slice_id']);
				$slices_top[] = $slice_name[0];
			}

			// $slices_bot_model = SlicesVia::find()->where(['slice' => $slice_model['id'], 'type' => 1])->all();
			// foreach ($slices_bot_model as $item) {
			// 	$slice_name = Slices::getSliceNameById($item['slice_id']);
			// 	$slices_bot[] = $slice_name[0];
			// }

			$this->params = json_decode($slice_model->params, true);
			$this->seo = [
				'h1' => $slice_model->h1,
				'title' => $slice_model->title,
				'description' => $slice_model->description,
				'keywords' => $slice_model->keywords,
				'text_top' => $slice_model->text_top,
				'text_bottom' => $slice_model->text_bottom,
				'img_alt' => $slice_model->img_alt,
				'feature' => $slice_model->feature,
			];
			$this->flag = true;
			$this->slices_top = $slices_top;
			// $this->slices_bot = $slices_bot;
			$this->type = $slice_model->type;
		}
	}
}
