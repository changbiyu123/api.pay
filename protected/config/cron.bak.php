<?php

return array(
    // This path may be different. You can probably get it from `config/main.php`.
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Cron',
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
    ),
    // We'll log cron messages to the separate files
    'components' => array(
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cron.log',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cron_trace.log',
                    'levels' => 'trace',
                ),
            ),
        ),
        
          'db' => array(
          'connectionString' => 'mysql:host=localhost;dbname=gs_prod', //dbname=gs_prod
          'emulatePrepare' => true,
          'username' => 'qin560', //qin560
          'password' => 'O8%DOxrSEJAr',
          'charset' => 'utf8',
          ),
         
        /*
         * Local db.
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=mainapp',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ),
        */
    ),
);