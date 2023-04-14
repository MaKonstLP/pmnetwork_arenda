<?php
namespace app\modules\arenda\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use common\models\blog\BlogPost;
use common\models\blog\BlogTag;
use common\models\Seo;

class BlogController extends Controller
{
	public function actionIndex()
	{
		if (Yii::$app->params['subdomen_alias'] != ''){
			throw new \yii\web\NotFoundHttpException();
		}

		$query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 6,
				'defaultPageSize' => 6,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
		]);

		// $topPosts = (clone $query)->where(['featured' => true])->limit(6)->all();
		// $seo = (new Seo('blog', $dataProvider->getPagination()->page + 1))->seo;
		// $this->setSeo($seo);

		$listConfig = [
			'dataProvider' => $dataProvider,
			'itemView' => '_list-item.twig',
			'layout' => "{items}\n<div class='pagination_wrapper items_pagination' data-pagination-wrapper>{pager}</div>",
			'pager' => [
				'class' => LinkPager::class,
				'disableCurrentPageButton' => true,
				'nextPageLabel' => false,
				'prevPageLabel' => false,
				'maxButtonCount' => 4,
				'activePageCssClass' => '_active',
				'pageCssClass' => 'items_pagination_item',
			],
		];

		// echo '<pre>';
		// print_r($dataProvider->getPagination()->page);
		// exit;

		// return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
		return $this->render('index.twig', compact('listConfig'));
	}

	public function actionPost($alias)
	{
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();

		if (Yii::$app->params['subdomen_alias'] != '' || empty($post)) {
			throw new \yii\web\NotFoundHttpException();
		}

		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);

		$extraData = [
			'headings' => $post->getTableOfContentsArray(),
	 ];

		// echo '<pre>';
		// print_r($seo);
		// exit;

		return $this->render('post.twig', compact('post', 'seo', 'extraData'));
	}

	public function actionPreview($id)
	{
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}

}