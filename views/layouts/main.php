<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use common\models\Subdomen;
use frontend\modules\arenda\models\SubdomenHeaderMenu;
use frontend\modules\arenda\models\SubdomenFooterLinks;
use frontend\modules\arenda\assets\AppAsset;

frontend\modules\arenda\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="yandex-verification" content="483683dd3d41a86f" />
	<meta name="yandex-verification" content="a13f5006e1a7895b" />
	<link rel="icon" type="image/png" href="/images/favicon.png">
	<!-- <link rel="stylesheet" type="text/css" href="http://fonts.fontstorage.com/import/firasans.css"> -->
	<title><?php echo $this->title ?></title>
	<?php $this->head() ?>
	<?php if(Yii::$app->params['noindex_global'] === true){
		echo '<meta name="robots" content="noindex" />';
	} ?>
	<?php if (isset($this->params['desc']) and !empty($this->params['desc'])) echo "<meta name='description' content='".$this->params['desc']."'>";?>
	<?php if (isset($this->params['canonical']) and !empty($this->params['canonical'])) echo "<link rel='canonical' href='".$this->params['canonical']."'>";?>
	<?php if (isset($this->params['kw']) and !empty($this->params['kw'])) echo "<meta name='keywords' content='".$this->params['kw']."'>";?>
	<?php //if (isset($this->params['robots']) and $this->params['robots']) echo "<meta name='robots' content='noindex, follow'>";?>
	<?php //if (isset($this->params['robots_2']) and $this->params['robots_2']) echo "<meta name='robots' content='noindex, nofollow'>";?>
	<?= Html::csrfMetaTags() ?>

	<!-- schemaOrg START -->
	<?php if (isset(Yii::$app->params['sale_event_first']) && !empty(Yii::$app->params['sale_event_first'])) {
		echo '<script type="application/ld+json">' . Yii::$app->params['sale_event_first'] . '</script>';
	}
	?>
	<?php if (isset(Yii::$app->params['sale_event_second']) && !empty(Yii::$app->params['sale_event_second'])) {
		echo '<script type="application/ld+json">' . Yii::$app->params['sale_event_second'] . '</script>';
	}
	?>
	<?php if (isset(Yii::$app->params['sale_event_third']) && !empty(Yii::$app->params['sale_event_third'])) {
		echo '<script type="application/ld+json">' . Yii::$app->params['sale_event_third'] . '</script>';
	}
	?>
	<?php if (isset(Yii::$app->params['schema_product']) && !empty(Yii::$app->params['schema_product'])) {
		echo '<script type="application/ld+json">' . Yii::$app->params['schema_product'] . '</script>';
	}
	?>
	<!-- schemaOrg END -->

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-193001228-1"></script>
	<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-2RMENLSN37"></script> -->
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-193001228-1');
	// gtag('config', 'G-2RMENLSN37');

    </script>
</head>
<body data-channel-id="2" class="<?if(isset($this->params['prazdnik_type']) && !empty($this->params['prazdnik_type'])) echo $this->params['prazdnik_type'];?>">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQ92WXZ"
height="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php $this->beginBody() ?>
	<div class="main_wrap">
		<header>
			<div class="top">
				<a href="/">
					<div class="logo_contein">
						<img src="/images/logo_img.svg" class="logo">
						<p class="logo_text">Аренда залов</p>
					</div>
				</a>
				<div class="city" data-city-id="<?= Yii::$app->params['subdomen_id'] ?>">
					<!-- <img src="/images/map.svg" class="map_inc"> -->
					<p class="city_name"><?=Yii::$app->params['subdomen_name']?></p>
					<img src="/images/dropdown_icon.svg" class="dropdown" data-city-dropdown>
				</div>
				<noindex>
					<div class="city_select_wrapper">
						<div class="city_select_search_wrapper _hide">
							<p class="back_to_header_menu">Назад в меню</p>
							<p class="pseudo_h4">Выберите город</p>

							<div class="city_select_list">
								<?php
									$subdomen_list = Subdomen::find()
										->where(['active' => 1])
										->orderBy(['name' => SORT_ASC])
										->all();

									function createCityNameLine($city){
										if($city->alias){
											$newLine = "<p><a href='//$city->alias." . Yii::$app->params['siteAddress'] . "'>$city->name</a></p>";
										}
										else{
											$newLine = "<p><a href='//" . Yii::$app->params['siteAddress'] . "'>$city->name</a></p>";
										}
										return $newLine;
									}

									function createLetterBlock($letter){
										$newBlock = "<div class='city_select_letter_block' data-first-letter=$letter>";
										return $newBlock;
									}

									function createCityList($subdomen_list){
										$citiesListResult = "";
										$currentLetterBlock = "";

										foreach ($subdomen_list as $key => $subdomen){
											$currentFirstLetter = substr($subdomen->name, 0, 2);
											if ($currentFirstLetter !== $currentLetterBlock){
												$currentLetterBlock = $currentFirstLetter;
												$citiesListResult .= "</div>";
												$citiesListResult .= createLetterBlock($currentLetterBlock);
												$citiesListResult .= createCityNameLine($subdomen);
											} else {
												$citiesListResult .= createCityNameLine($subdomen);
											}
										}
											
										$citiesListResult .= "</div>";
										echo substr($citiesListResult, 6);
									}

									createCityList($subdomen_list);
								?>
							</div>
						</div>
					</div>
				</noindex>
				<nav class="header_menu">
					<div class="header_menu_burger">
						<span>Каталог</span>
						<img src="/images/Close_icon_mobile.svg">
					</div>
					<div class="city _mobile">
						<!-- <img src="/images/map.svg" class="map_inc"> -->
						<a href="#"><p class="city_name"><?=Yii::$app->params['subdomen_name']?></p></a>
						<img src="/images/dropdown_icon_down.svg" class="dropdown" data-city-dropdown>
					</div>
					<!-- <div class="header_menu__item">
						<a href="/catalog/svadba/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'vse-kategorii')echo '_active';?>">
							<span style="font-weight: 400;">💍</span>Где отметить свадьбу
						</a>
					</div> -->
					<?php
						$header_menu = SubdomenHeaderMenu::find()
							->with(['submenus' => function ($query) {
								$query->andWhere(['active' => 1]);
								$query->andWhere(['city_id' => Yii::$app->params['subdomen_id']]);
							}])
							->all();
					?>
					<?php foreach ($header_menu as  $menu_item): ?>
						<div class="header_menu__item">
							<span> <?= $menu_item['text'] ?></span>
							<div class="header__submenu">
								<?php foreach ($menu_item['submenus'] as $submenu): ?>
									<a href="/catalog/<?= $submenu['link'] ?>/"><?= $submenu['name'] ?></a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
					<!-- <div class="header_menu__item">
						<span>💍Свадьба</span>
						<div class="header__submenu">
							<a href="/catalog/svadba/">Все площадки</a>
							<a href="/catalog/arenda-banketnogo-zala-dlya-svadby/">Банкетные залы</a>
							<a href="/catalog/arenda-restorana-na-svadbu/">Рестораны</a>
							<a href="/catalog/arenda-kafe-dlya-svadby/">Кафе</a>
							<a href="/catalog/arenda-lofta-dlya-svadby/">Лофты</a>
							<a href="/catalog/svadba-na-prirode/">На природе</a>
							<a href="/catalog/arenda-shatrov-na-svadbu/">Шатры</a>
							<a href="/catalog/arenda-verandy-dlya-svadby/">Веранды</a>
							<a href="/catalog/svadba-y-vody/">У воды</a>
							<a href="/catalog/arenda-kottedzha-dlya-svadby/">Коттеджи</a>
							<a href="/catalog/svadba-v-otele/">Отели</a>
						</div>
					</div>
					<div class="header_menu__item">
						<span>🎂День рождения</span>
						<div class="header__submenu">
							<a href="/catalog/den-rojdeniya/">Все площадки</a>
							<a href="/catalog/arenda-banketnogo-zala-dlya-dnya-rozhdeniya/">Банкетные залы</a>
							<a href="/catalog/arenda-restorana-dlya-dnya-rozhdeniya/">Рестораны</a>
							<a href="/catalog/arenda-kafe-dlya-dnya-rozhdeniya/">Кафе</a>
							<a href="/catalog/arenda-lofta-dlya-dnya-rozhdeniya/">Лофты</a>
							<a href="/catalog/den-rozhdeniya-na-prirode/">На природе</a>
							<a href="/catalog/arenda-terrasy-dlya-dnya-rozhdeniya/">Шатры</a>
							<a href="/catalog/arenda-verandy-dlya-dnya-rozhdeniya/">Веранды</a>
							<a href="/catalog/arenda-kottedzha-dlya-dnya-rozhdeniya/">Коттеджи</a>
							<a href="/catalog/arenda-cluba-dlya-dnya-rozhdeniya/">Клубы</a>
							<a href="/catalog/arenda-bara-dlya-dnya-rozhdeniya/">Бары</a>
						</div>
					</div>
					<div class="header_menu__item">
						<span>🤟Корпоратив</span>
						<div class="header__submenu">
							<a href="/catalog/korporativ/">Все площадки</a>
							<a href="/catalog/ploshchadki-dlya-korporativa/">Площадки</a>
							<a href="/catalog/loft-dlya-korporativa/">Лофты</a>
							<a href="/catalog/zagorodnyye-ploshchadki-dlya-korporativa/">На природе</a>
							<a href="/catalog/restorany-dlya-korporativa/">Рестораны</a>
							<a href="/catalog/kafe-dlya-korporativa/">Кафе</a>
						</div>
					</div>
					<div class="header_menu__item">
						<span>🎓Выпускной</span>
						<div class="header__submenu">
							<a href="/catalog/vypusknoy/">Все площадки</a>
							<a href="/catalog/vypusknoy-za-gorodom-na-prirode/">На природе</a>
						</div>
					</div>
					<div class="header_menu__item">
						<span>⛄Новый год</span>
						<div class="header__submenu">
							<a href="/catalog/novyy-god/">Все площадки</a>
						</div>
					</div> -->
					<!-- <div class="city _mobile">
						<a href="#"><p class="city_name"><?=Yii::$app->params['subdomen_name']?></p></a>
						<img src="/images/dropdown_icon_down.svg" class="dropdown" data-city-dropdown>
					</div> -->

					<div class="header_menu_footer">
						<?php
							if (!isset(Yii::$app->params['premium_rest'])) {
								if (isset(Yii::$app->params['subdomen_phone']) && Yii::$app->params['subdomen_phone'] !== ''){
									echo '<div class="right_phone_block"><p class="right_block_text">Колл-центр</p>';
									echo '<a href="tel:' . Yii::$app->params['subdomen_phone'] . '" class="head_tel" data-layout-phone data-copy-phone>';
												$phone = Yii::$app->params['subdomen_phone'];
												echo substr($phone, 0, 2) . ' (' . substr($phone, 2, 3) . ') ' . substr($phone, 5, 3) . '-' . substr($phone, 8, 2) . '-' . substr($phone, 10, 2);
									echo '</a>';
									echo '<div class="phone-bufer"><div class="phone-bufer__close"></div>Номер скопирован в буфер обмена</div></div>';
								}
							}
						?>
						<div class="link_form" data-open-popup-form>
							<p class="for_form _link">Подберите мне зал</p>
						</div>
					</div>
				</nav>
				<div class="right_block">
					<?php
						if (!isset(Yii::$app->params['premium_rest'])) {
							if (isset(Yii::$app->params['subdomen_phone']) && Yii::$app->params['subdomen_phone'] !== ''){
								echo '<div class="right_phone_block"><p class="right_block_text">Колл-центр</p>';
								echo '<a href="tel:' . Yii::$app->params['subdomen_phone'] . '" class="head_tel" data-layout-phone data-copy-phone>';
											$phone = Yii::$app->params['subdomen_phone'];
											echo substr($phone, 0, 2) . ' (' . substr($phone, 2, 3) . ') ' . substr($phone, 5, 3) . '-' . substr($phone, 8, 2) . '-' . substr($phone, 10, 2);
								echo '</a>';
								echo '<div class="phone-bufer"><div class="phone-bufer__close"></div>Номер скопирован в буфер обмена</div></div>';
							}
						}
					?>
					<div class="link_form" data-open-popup-form>
						<img src="/images/confetti.svg" class="confetti">
						<p class="for_form _link">Подберите мне зал</p>
					</div>

					<div class="header_burger">
						<dvi class="header_burger_button">
							<div></div>
							<div></div>
							<div></div>
						</dvi>
						Каталог
					</div>
				</div>
				
			</div>
		</header>

		<div class="content_wrap forIndex">
			<?= $content ?>
			<div class="confetti_big"></div>
			<div class="ball_img"></div>
		</div>

		<footer>
			<div class="footer_wrap">
				<div class="footer_row">
					<div class="footer_block _left">
						<a href="/" class="footer_logo">
							<div class="footer_logo_img"></div>
							<p class="logo_text">Аренда залов</p>
						</a>
						<div class="footer_info">
							<p class="footer_copy">© <?php echo date("Y");?> Аренда залов</p>
							<a href="https://arendazala.net/o-nas/" target="_blank" class="footer_pc _link">О нас</a>
							<a href="https://arendazala.net/requisites/" target="_blank" class="footer_pc _link">Реквизиты</a>
							<a href="https://arendazala.net/advertisement/" target="_blank" class="footer_pc _link">Реклама на сайте</a>
							<a href="https://arendazala.net/privacy/" target="_blank" class="footer_pc _link">Политика конфиденциальности</a>
						</div>
					</div>
					<div class="footer__menu">
						<?php
						$footer_links = SubdomenFooterLinks::find()
							->where(['active' => 1])
							->andWhere(['city_id' => Yii::$app->params['subdomen_id']])
							->orderBy(['sort' => SORT_ASC])
							->all();
						
						foreach ($footer_links as $key => $footer_link) {
							echo '<a class="footer__menu-link" href="/catalog/' . $footer_link["link"] . '/">'. $footer_link["name"] .'</a>';
						};
						?>
					</div>
					<div class="footer_block _right">
						<div class="footer_phone">
							<?php
								if (!isset(Yii::$app->params['premium_rest'])) {
									if (isset(Yii::$app->params['subdomen_phone']) && Yii::$app->params['subdomen_phone'] !== '') {
										echo '<a href="tel:' . Yii::$app->params['subdomen_phone'] . '" data-layout-phone data-copy-phone><p>';
											$phone = Yii::$app->params['subdomen_phone'];
											echo substr($phone, 0, 2) . ' (' . substr($phone, 2, 3) . ') ' . substr($phone, 5, 3) . '-' . substr($phone, 8, 2) . '-' . substr($phone, 10, 2);
										echo '</p></a>';
										echo '<div class="phone-bufer"><div class="phone-bufer__close"></div>Номер скопирован в буфер обмена</div>';
									}
								}
							?>
						</div>

						<div class="footer_phone_button" data-open-popup-form>
							<img src="/images/confetti.svg" class="confetti">
							<p class="_link">Подберите мне зал</p>
						</div>
					</div>
				</div>
			</div>
		</footer>

	</div>

	<div class="popup_wrap">
		<div class="popup_layout" data-close-popup></div>

		<div class="popup_form">
			<?= $this->render('//components/generic/form.twig', array('type' => 'popup')) ?>
		</div>

		<div class="popup_filter_container _hidden"></div>
	</div>

	<div class="popup_wrap__item-mobile">
		<div class="popup_layout" data-close-popup></div>

		<div class="popup_form">
			<?= $this->render('//components/generic/form_item_mobile.twig', array(
				'type' => 'item-popup-mobile', 
				'venue_id' => isset(Yii::$app->params['rest_gorko_id']) ? Yii::$app->params['rest_gorko_id'] : '',
				))
			?>
		</div>
	</div>

	<?php if (isset(Yii::$app->params['room_gorko_id'])) : ?>
		<div class="popup_wrap__item-review">
			<div class="popup_layout" data-close-popup></div>

			<div class="popup_form">
				<?= $this->render('//components/generic/form_review.twig', array(
					'type' => 'item-review', 
					'venue_id' => isset(Yii::$app->params['rest_gorko_id']) ? Yii::$app->params['rest_gorko_id'] : '',
					'room_id' => isset(Yii::$app->params['room_gorko_id']) ? Yii::$app->params['room_gorko_id'] : '',
					))
				?>
			</div>
		</div>
	<?php endif; ?>

<script>
    function setCssVars() {
        const title = document.querySelector('.reviews-tags__title') ? document.querySelector('.reviews-tags__title') : document.querySelector('.reviews-smalltags__title');
        const heightTags = window.getComputedStyle(title).getPropertyValue('height');
        const widthTags = window.getComputedStyle(title).getPropertyValue('width');
        document.documentElement.style.setProperty('--heightTags', heightTags);
        document.documentElement.style.setProperty('--widthTags', widthTags);
    }

    setCssVars();

    window.addEventListener("resize", function() {
        setCssVars();
    });
</script>
<?php $this->endBody() ?>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap&subset=cyrillic" rel="stylesheet">
<?php if (Yii::$app->params['subdomen_alias'] == 'nnovgorod'): ?>
	<!-- <script type="text/javascript" src="https://spikmi.org/Widget?id=16223" async></script> -->
<?php endif; ?>
<?php if (Yii::$app->params['subdomen_alias'] == 'chelyabinsk'): ?>
	<script>(function () { var widget = document.createElement('script'); widget.dataset.pfId = 'ddfd253c-2803-4a5a-a6d3-448f59512f57'; widget.src = 'https://widget.profeat.team/script/widget.js?id=ddfd253c-2803-4a5a-a6d3-448f59512f57&now='+Date.now(); document.head.appendChild(widget); })()</script>
<?php endif; ?>
</body>
</html>
<?php $this->endPage() ?>
