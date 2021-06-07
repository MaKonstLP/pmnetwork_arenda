<?php
namespace app\modules\arenda\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
// use frontend\modules\arenda\models\ElasticItems;
use common\models\blog\BlogPost;
use common\models\blog\BlogTag;


class BlogController extends Controller
{

	public function actionIndex()
	{

		// echo '<pre>';
		// print_r($post);
		exit;

		return $this->render('index.twig', array(

		));
	}

	public function actionPost($alias)
	{
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();

		// if (empty($post)) {
		// 	throw new \yii\web\NotFoundHttpException();
		// }

		// echo '<pre>';
		// print_r($post);
		// exit;

		return $this->render('post.twig', compact('post'));
	}

	public function actionPreview($id)
	{
	}
}