<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use common\models\Subdomen;
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
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link rel="stylesheet" type="text/css" href="http://fonts.fontstorage.com/import/firasans.css">
    <title><?php echo $this->title ?></title>
    <?php $this->head() ?>
    <?php if (isset($this->params['desc']) and !empty($this->params['desc'])) echo "<meta name='description' content='".$this->params['desc']."'>";?>
    <?php if (isset($this->params['canonical']) and !empty($this->params['canonical'])) echo "<link rel='canonical' href='".$this->params['canonical']."'>";?>
    <?php if (isset($this->params['kw']) and !empty($this->params['kw'])) echo "<meta name='keywords' content='".$this->params['kw']."'>";?>
    <?php if (isset($this->params['robots']) and $this->params['robots']) echo "<meta name='robots' content='noindex, follow'>";?>
    <?php if (isset($this->params['robots_2']) and $this->params['robots_2']) echo "<meta name='robots' content='noindex, nofollow'>";?>
    <?= Html::csrfMetaTags() ?>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PQ92WXZ');</script>
    <!-- End Google Tag Manager -->

</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQ92WXZ"
height="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php $this->beginBody() ?>

    <div class="main_wrap">
        
        <header>
            <!--Верстка шапки сайта -->
    <div class="top">
        <a href="/">
        <div class="logo_contein">
            
        <img src="/images/logo_img.svg" class="logo">
            <p class="logo_text">Аренда залов</p>
           
        </div>
         </a>
            <div class="city">
                <img src="/images/map.svg" class="map_inc">
                <p class="city_name"><?=Yii::$app->params['subdomen_name']?></p>
                <img src="/images/dropdown_icon.svg" class="dropdown" data-city-dropdown>

            </div>
            <div class="city_select_wrapper">
             <div class="city_select_search_wrapper _hide">

                        <p class="back_to_header_menu">Назад в меню</p>

                        <p class="pseudo_h4">Выберите город</p>

                        <?php /*<div class="input_search_wrapper">

                            <input type="search" placeholder="Название города">

                        </div> */?>

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
        <nav class="header_menu">
             <a href="/catalog/zaly/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'zaly')echo '_active';?>">Залы</a>
             <a href="/catalog/loft/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'loft')echo '_active';?>">Лофт</a>
             <a href="/catalog/ploshchadki/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'ploschadki')echo '_active';?>">Площадки</a>
             <a href="/catalog/zavedeniya/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'zavedeniya')echo '_active';?>">Заведения</a>
             <a href="/catalog/priroda/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'priroda')echo '_active';?>">Природа</a>
             <!-- <a href="/popular/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'lutchee')echo '_active';?>">Лучшее</a> -->
             <a href="/popular/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'vse-kategorii')echo '_active';?>">Все категории</a>
             <!-- <a href="/blog/" class="<?if(!empty($this->params['menu']) and $this->params['menu'] == 'blog')echo '_active';?>">Блог</a> -->
              <div class="city _mobile">
                <!-- <img src="/images/map.svg" class="map_inc"> -->
                <a href="#"><p class="city_name"><?=Yii::$app->params['subdomen_name']?></p></a>
                <img src="/images/dropdown_icon_down.svg" class="dropdown" data-city-dropdown>
            </div>
        </nav>
        <div class="right_block">
        <a href="" class="head_tel"></a>
        <div class="link_form" data-open-popup-form>
        <img src="/images/confetti.svg" class="confetti">
        <p class="for_form _link">Подберите мне зал</p>
        </div>
        </div>
                <div class="header_burger">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
    </div>
    <!--Конец верстки шапки сайта -->

    
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
                            <a href="/privacy/" target="_blank" class="footer_pc _link">Политика конфиденциальности</a>
                        </div>                        
                    </div>
                    <div class="footer_block _right">
                        <div class="footer_phone">
                            <a href=""><p></p></a>
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
            <?=$this->render('//components/generic/form.twig')?>
        </div>

        <div class="popup_filter_container _hidden">
            
        </div>

    </div>

<?php $this->endBody() ?>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap&subset=cyrillic" rel="stylesheet">
</body>
</html>
<?php $this->endPage() ?>
