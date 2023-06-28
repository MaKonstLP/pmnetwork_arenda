<?php

namespace frontend\modules\arenda\models;

use Yii;

/**
 * This is the model class for table "subdomen_header_menu".
 *
 * @property int $id
 */
class SubdomenHeaderMenu extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'subdomen_header_menu';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['name', 'text'], 'string'],
			[['id'], 'integer'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'name',
			'text' => 'text',
		];
	}

	public function getSubmenus()
	{
		return $this->hasMany(SubdomenHeaderSubmenu::className(), ['menu_id' => 'id']);
	}
}
