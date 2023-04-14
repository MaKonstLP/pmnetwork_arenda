<?php
namespace frontend\modules\arenda\models;

use Yii;
use yii\db\ActiveRecord;

class SlicesExtraOption extends ActiveRecord
{
	// метод который возвращает имя таблицы в базе данных с которой нужно работать в данном случае "slices_extra_option"
	public static function tableName()
	{
		return 'slices_extra_option';
	}
}