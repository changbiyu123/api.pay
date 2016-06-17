<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '名医之道',
    // preloading 'log' component
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.models.base.*',
        'application.models.core.*',
        'application.models.region.*',
        'application.models.common.*',
        'application.models.site.*',
        'application.models.user.*',
        'application.models.trip.*',
        'application.models.booking.*',
        'application.models.blog.*',
        'application.models.sales.*',
        'application.models.payment.*',
        'application.models.payment.alipay.*',
        'application.models.album.*',
        'application.models.email.*',
        'ext.mail.YiiMailMessage',
        'ext.PHPDocCrontab.PHPDocCrontab',
        'application.modules.messagequeue.models.*',
        'application.modules.messagequeue.models.messagequeue.*',
    ),
    'commandMap' => array(
        'cron' => array(
            'class' => 'ext.PHPDocCrontab.PHPDocCrontab',
        ),
    ),
    // application components
    'components' => array(
        'mail' => array(
            'class' => 'ext.mail.YiiMail',
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.gmail.com',
                'username' => 'noreply.guidesky@gmail.com',
                'password' => '19911109',
                'port' => '465',
                'encryption' => 'ssl',
            // 'encryption' => 'tls',
            ),
            'viewPath' => 'application.views.mail',
            'logging' => true,
            'dryRun' => false
        ),
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=gs_prod', //dbname=gs_prod
            'emulatePrepare' => true,
            'username' => 'qin560', //qin560
            'password' => 'O8%DOxrSEJAr',
            'charset' => 'utf8',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'console.log',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'console_trace.log',
                    'levels' => 'trace',
                ),
            ),
        ),
    ),
);