<?php
namespace frontend\modules\arenda\models;

use Yii;
use yii\db\ActiveRecord;

class SlicesExtraOptionVia extends ActiveRecord
{
	// метод который возвращает имя таблицы в базе данных с которой нужно работать в данном случае "slices_extra_option_via"
	public static function tableName()
	{
		return 'slices_extra_option_via';
	}

	public function getOptions()
	{
		return $this->hasOne(SlicesExtraOption::className(), ['extra_option_id' => 'extra_option_id']);
	}
}