<?php

namespace frontend\modules\arenda\models;

use Yii;

/**
 * This is the model class for table "subdomen_footer_links".
 *
 * @property int $id
 */
class SubdomenFooterLinks extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'subdomen_footer_links';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['city_id'], 'required'],
			[['name', 'link'], 'string'],
			[['id', 'city_id', 'active', 'sort'], 'integer'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'city_id' => 'city_id',
			'name' => 'name',
			'link' => 'link',
			'active' => 'active',
			'sort' => 'sort',
		];
	}
}
