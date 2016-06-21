<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '名医主刀',
    'defaultController' => 'home',
    // preloading 'log' component
    'preload' => array('log'),
    // application default language.
    'language' => 'zh_cn',
    //'language'=>'en_us',
    // config to be defined at runtime.
    'behaviors' => array('ApplicationConfigBehavior'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        //    'application.sdk.alipaydirect.*',
        'ext.mail.YiiMailMessage',
    ),
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'password',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),
    // application components
    'components' => array(
        'mobileDetect' => array(
            'class' => 'ext.MobileDetect.MobileDetect'
        ),
        'image' => array(
            'class' => 'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver' => 'GD',
        ),
        'mail' => array(
            'class' => 'ext.mail.YiiMail',
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.ym.163.com',
                'username' => 'noreply@mingyihz.com',
                'password' => '91466636',
                'port' => '994',
                'encryption' => 'ssl',
            // 'encryption' => 'tls',
            ),
            'viewPath' => 'application.views.mail',
            'logging' => true,
            'dryRun' => false
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true
        ),
        // User module
        /*
          'authManager' => array(
          'class' => 'CDbAuthManager',
          'connectionID' => 'db',
          'defaultRoles' => array('Authenticated', 'Guest'),
          ),
         * 
         */
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'caseSensitive' => false,
            'showScriptName' => false,
            'rules' => array(
                '<controller:\w+>/<action:index>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        // myzd-test.
         'db' => array(
         'connectionString' => 'mysql:host=qpmyzdstaging91466636.mysql.rds.aliyuncs.com;dbname=myzd-test',
         'emulatePrepare' => true,
         'username' => 'supertestuser',
         'password' => 'Qp91466636',
         'charset' => 'utf8',
         'schemaCachingDuration' => 3600    // 开启表结构缓存（schema caching）提高性能
         ),
        // 本地数据库        
//         'db' => array(
//             'connectionString' => 'mysql:host=localhost;dbname=myzd_local',
//             'emulatePrepare' => true,
//             'username' => 'root',
//             'password' => '',
//             'charset' => 'utf8',
//             'schemaCachingDuration' => 3600    // 开启表结构缓存（schema caching）提高性能
//         ),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CDbLogRoute',
                    'connectionID' => 'db',
                    'logTableName' => 'core_log',
                    'levels' => 'info,error'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'application.log',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'trace.log',
                    'levels' => 'trace',
                ),
            /*
              array(
              // log db command in firebug.
              'class' => 'CWebLogRoute',
              'categories' => 'system.db.CDbCommand',
              'showInFireBug' => true,
              'ignoreAjaxInFireBug' => false,
              ),
             * 
             */
            ),
        ),
        'session' => array(
            'class' => 'CDbHttpSession',
            'connectionID' => 'db',
            'sessionTableName' => 'core_session',
            'timeout' => 3600 * 24 * 14, // 14 days.
        ),
        'clientScript' => array(
            'class' => 'CClientScript',
            'coreScriptPosition' => CClientScript::POS_HEAD,
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'admin' => 'superbeta',
        'adminPassword' => '9be4e9c1e40a1952d3ad23cdb9343cedce6814d8e2b031d9d775ba58a02108b0',
        'adminEmail' => 'fainqin@foxmail.com',
        //'contactEmail' => 'contactus@mingyihz.com',
    ),
);
