<?php
$db_name = "docflow";
$username = "root";
$password = "";
$fp = fopen("protected/config/db.txt","r");
if ($fp){
  $db_name = str_replace("\n","",str_replace("\r","",fgets($fp)));
  $username = str_replace("\n","",str_replace("\r","",fgets($fp)));
  $password = str_replace("\n","",str_replace("\r","",fgets($fp)));
  fclose($fp);
}
return array(
   'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
   'name' => 'Документообіг',
   'sourceLanguage' => 'uk',
   'language' => 'uk',
   // preloading 'log' component
   'preload' => array('log'),
   // autoloading model and component classes
   'import' => array(
      'application.models.*',
      'application.components.*',
      'application.modules.srbac.controllers.SBaseController',
      'ext.EHttpClient.*',
      'ext.EHttpClient.adapter.*',
      'ext.EWideImage.EWideImage',
   ),
   'modules' => array(
      // uncomment the following to enable the Gii tool
      'srbac' => array(
         'userclass' => 'Users',
         'userid' => 'id',
         'username' => 'username',
         'debug' => true,
         'delimeter' => "@",
         'pageSize' => 10,
         'superUser' => 'Root',
         'css' => 'srbac.css',
         'layout' => 'application.views.layouts.main',
         'notAuthorizedView' => 'srbac.views.authitem.unauthorized',
         'alwaysAllowed' => array(),
         //'userActions' => array('show', 'View', 'List'),
         'listBoxNumberOfLines' => 15,
         'imagesPath' => 'srbac.images',
         'imagesPack' => 'tango',
         'iconText' => false,
         'header' => 'srbac.views.authitem.header',
         'footer' => 'srbac.views.authitem.footer',
         'showHeader' => true,
         'showFooter' => true,
         'alwaysAllowedPath' => 'srbac.components',
      ),
      'gii' => array(
         'generatorPaths' => array(
            'bootstrap.gii',
         ),
         'class' => 'system.gii.GiiModule',
         'password' => '111',
         // If removed, Gii defaults to localhost only. Edit carefully to taste.
         'ipFilters' => array('*', '::1'),
      ),
   ),

   'components' => array(
        'widgetFactory' => array(
            'widgets' => array(
                'CLinkPager' => array(
                    'htmlOptions' => array(
                        'class' => 'pagination'
                    ),
                    'header' => false,
                    'maxButtonCount' => 5,
                    'cssFile' => false,
                ),
                'CGridView' => array(
                    'htmlOptions' => array(
                        'class' => 'table-responsive'
                    ),
                    'pagerCssClass' => 'dataTables_paginate paging_bootstrap',
                    'itemsCssClass' => 'table table-striped table-hover',
                    'cssFile' => false,
                    'summaryCssClass' => 'dataTables_info',
                    'summaryText' => 'Showing {start} to {end} of {count} entries',
                    'template' => '{items}<div class="row-fluid"><div class="col-sm-12">{pager}</div></div><br />',
                ),
            ),
        ),
      'session' => array(
         'autoStart' => true,
      ),
      
      'authManager' => array(
         'class' => 'srbac.components.SDbAuthManager',
         'connectionID' => 'db',
         'itemTable' => 'roles',
         'assignmentTable' => 'roleassignments',
         'itemChildTable' => 'rolechildren',
      ),
      
      'user' => array(
         'class' => "WebUser", // enable cookie-based authentication
         'allowAutoLogin' => false,
      ),

      'urlManager' => array(
         'urlFormat' => 'path',
         'caseSensitive' => false,
         'showScriptName' => false,
         'rules' => array(
            '<controller:\w+>/<id:\d+>' => '<controller>/view',
            '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
            '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            '<module>/<controller:\w+>/<id:\d+>' => '<module>/<controller>/view',
            '<module>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
            '<module>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
         ),
      ),
      
      'db' => array(
         'connectionString' => 'mysql:host=localhost;dbname='.$db_name,
         'emulatePrepare' => true,
         'username' => $username,
         'password' => $password,
         'charset' => 'utf8',
         'enableProfiling' => true,
         'enableParamLogging' => true,
      ),
      
      'errorHandler' => array(
         // use 'site/error' action to display errors
         'errorAction' => 'site/error',
      ),

      'log' => array(
         'class' => 'CLogRouter',
         'routes' => array(
            array(
               'class' => 'CProfileLogRoute',
               'levels' => 'profile',
               'enabled' => 0,
            ),
         ),
      ),
      'JGoogleAPI' => array(
          'class' => 'ext.JGoogleAPI.JGoogleAPI',
          'defaultAuthenticationType'=>'serviceAPI',
          'serviceAPI' => array(
              'clientId' => '57453613982-6iiuc20c54b44kl17jmv4s79nls3ufua.apps.googleusercontent.com',
              'clientEmail' => '57453613982-6iiuc20c54b44kl17jmv4s79nls3ufua@developer.gserviceaccount.com',
              'keyFilePath' => 'e6556fd783c6f1e8a9fc23bcda8e71d43b369709-privatekey.p12',
          ),
          'scopes' => array(
              'serviceAPI' => array(
                  'drive' => array(
                      'https://www.googleapis.com/auth/drive.file',
                  ),
              ),
              'webappAPI' => array(
                  'drive' => array(
                      'https://www.googleapis.com/auth/drive.file',
                  ),
              ),
          ),
          'useObjects'=>true,
      ),
   ),
   'params' => array(
      'adminEmail' => '',
      //'docPath' => 'c:/UwAmp/docs/' ,
      //'docPath' => 'g:/docs/' ,
      'docPath' => '/home/sysadmin/docs/' ,
      
   ),
);
