<!DOCTYPE html>
<html lang="zh" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>                
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta charset="utf-8" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="pragma" content="no-cache" />
        <link rel="shortcut icon" type="image/ico" href="<?php echo Yii::app()->baseUrl; ?>/css/icons/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/font-awesome.min.css" />
        <!-- blueprint CSS framework -->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />        
        <!--[if lt IE 8]>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />        

        <?php
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery-1.9.1.min.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/bootstrap.min.js', CClientScript::POS_HEAD);
        ?>

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>

    <body>

        <div class="container" id="page">

            <div id="header">
                <div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
            </div><!-- header -->

            <div id="mainmenu">
                <?php
                $this->widget('zii.widgets.CMenu', array(
                    'items' => array(
                        array('label' => 'Home', 'url' => array('/site/index')),
                        array('label' => '医院', 'url' => array('/admin/hospital/index'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => '医生', 'url' => array('/admin/doctor/index'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => '科室', 'url' => array('/admin/faculty/index'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => '病历', 'url' => array('/admin/medicalrecord/index'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => '预约', 'url' => array('/admin/mrbooking/index'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => '用户', 'url' => array('/admin/user/index'), 'visible' => !Yii::app()->user->isGuest),
                        array('label' => 'Login', 'url' => array('/admin/default/login'), 'visible' => Yii::app()->user->isGuest),
                        array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/admin/default/logout'), 'visible' => !Yii::app()->user->isGuest)
                    ),
                ));
                ?>
            </div><!-- mainmenu -->
            <div class="container">
                <?php if (isset($this->breadcrumbs)): ?>
                    <?php
                    $this->widget('zii.widgets.CBreadcrumbs', array(
                        'links' => $this->breadcrumbs,
                    ));
                    ?><!-- breadcrumbs -->
                <?php endif ?>

                <?php echo $content; ?>

                <div class="clear"></div>
            </div>
            <div id="footer"></div>
            <!-- footer -->

        </div><!-- page -->

    </body>
</html>
