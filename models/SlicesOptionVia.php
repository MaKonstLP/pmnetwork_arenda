<?php
namespace frontend\modules\arenda\models;

use Yii;
use yii\db\ActiveRecord;

class SlicesOptionVia extends ActiveRecord
{
	// метод который возвращает имя таблицы в базе данных с которой нужно работать в данном случае "slices_option_via"
	public static function tableName()
	{
		return 'slices_option_via';
	}

	public function getOptions()
	{
		return $this->hasOne(SlicesOption::className(), ['option_id' => 'option_id']);
	}
}