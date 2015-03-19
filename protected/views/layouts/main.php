<?php /* @var $this Controller */ 
$controller=Yii::app()->controller;
$action=$controller->action;

$controller_name = $controller->uniqueID;
$action_name = $action->id;
$bU = Yii::app()->request->baseUrl;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <link href="<?php echo $bU; ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    
    <!-- jQuery -->
    <script type="text/javascript">
      if(typeof jQuery == 'undefined'){
        document.write('<script type="text/javascript" src="<?php echo $bU; ?>/own/js/jquery-1.11.1.min.js"><\/script>');
      }
    </script>
    
    <script type="text/javascript">
      var pagin_locale = function(){
        var str = $(".dataTables_info").text();
        str = str.replace(/Showing\s+([0-9]+)\s+to\s+([0-9]+)\s+of\s+([0-9]+)\sentries/,"елементи № $1-$2 , всього: $3");
        $(".dataTables_info").text(str);
      };
     $(function(){
      $('form').submit(function(){
        var t = $(this);
        t.find(':submit').attr('disabled','disabled');
        setTimeout(function(){
          t.find(':submit').attr('disabled',false);
        }, 10000);
      });
      pagin_locale();
     });
    </script>
    
    <!-- Bootstrap -->
    <link href="<?php echo $bU; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo $bU; ?>/bootstrap/js/bootstrap.min.js"></script>
    
    <!-- Bootstrap-3-editable -->
    <link href="<?php echo $bU; ?>/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?php echo $bU; ?>/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    
    <!-- Own styles and scripts -->
    <link rel="stylesheet" type="text/css" href="<?php echo $bU; ?>/own/css/main.css" />
    
    <!-- Users index -->
    <?php if($controller_name == "users" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/users/index.css" />
    <?php } ?>
    
    <!-- Users create or update -->
    <?php if($controller_name == "users" && in_array($action_name , array("create","update"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/users/form.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/many2many.js"></script>
    <?php } ?>
    
    <!-- Departments index -->
    <?php if($controller_name == "departments" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/departments/index.css" />
    <?php } ?>
    
    <!-- Departments create or update -->
    <?php if($controller_name == "departments" && in_array($action_name , array("create","update"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/departments/form.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/many2many.js"></script>
    <?php } ?>
    
    <!-- Deptgroups index -->
    <?php if($controller_name == "deptgroups" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/deptgroups/index.css" />
    <?php } ?>
    
    <!-- Deptgroups create or update -->
    <?php if($controller_name == "deptgroups" && in_array($action_name , array("create","update"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/deptgroups/form.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/many2many.js"></script>
    <?php } ?>
    
    <!-- Doccategories index -->
    <?php if($controller_name == "doccategories" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/doccategories/index.css" />
    <?php } ?>
    
    <!-- Doctypes index -->
    <?php if($controller_name == "doctypes" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/doctypes/index.css" />
    <?php } ?>
    
    <!-- Eventlevels index -->
    <?php if($controller_name == "eventlevels" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/eventlevels/index.css" />
    <?php } ?>
    
    <!-- Eventkinds index -->
    <?php if($controller_name == "eventkinds" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/eventkinds/index.css" />
    <?php } ?>
    
    <!-- Periods index -->
    <?php if($controller_name == "periods" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/periods/index.css" />
    <?php } ?>
    
    <!-- Files index -->
    <?php if($controller_name == "files" && $action_name == "index"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/files/index.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/prettyPhoto/css/prettyPhoto.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/prettyPhoto/js/jquery.prettyPhoto.js"></script>
    <?php } ?>
    
    <!-- Files create or update -->
    <?php if($controller_name == "files" && in_array($action_name , array("create","update"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/files/form.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/many2many.js"></script>
    <?php } ?>
    
    
    <!-- Documents create or update -->
    <?php if($controller_name == "documents" && in_array($action_name , array("create","update"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/documents/form.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/bootstrap/js/bootstrap-typeahead.js"></script>
      <!--script type="text/javascript" src="<?php echo $bU; ?>/bootstrap/js/typeahead.bundle.js"></script-->
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
    <?php } ?>
    
    <!-- Documents index -->
    <?php if($controller_name == "documents" && ($action_name == "index")){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/prettyPhoto/css/prettyPhoto.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/documents/index.css" />
        
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
      
      <script type="text/javascript" src="<?php echo $bU; ?>/prettyPhoto/js/jquery.prettyPhoto.js"></script>
      
      <script src="<?php echo $bU; ?>/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
      <script src="<?php echo $bU; ?>/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
      <script src="<?php echo $bU; ?>/jQuery-File-Upload/js/jquery.fileupload.js"></script>
    <?php } ?>
      
    <!-- Flows create or update -->
    <?php if($controller_name == "flows" && in_array($action_name , array("create","update"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/flows/form.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/many2many.js"></script>
    <?php } ?>
    
    <!-- Flows index -->
    <?php if($controller_name == "flows" && ($action_name == "index")){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/prettyPhoto/css/prettyPhoto.css" />
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/flows/index.css" />
        
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
      
      <script type="text/javascript" src="<?php echo $bU; ?>/prettyPhoto/js/jquery.prettyPhoto.js"></script>
    <?php } ?>
    
    <!-- Events index, admin, form -->
    <?php if($controller_name == "events"){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/events/events.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/many2many.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/own/js/events.js"></script>
    <?php } ?>
      
    <!-- Site reports -->
    <?php if($controller_name == "site" && in_array($action_name , array("reports"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
    <?php } ?>
      
    <!-- Site userinfo -->
    <?php if($controller_name == "site" && ($action_name == "userinfo")){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/datepicker/css/datepicker3.css" />
      <!--link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/own/css/site/userinfo.css" /-->
        
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/bootstrap-datepicker.js"></script>
      <script type="text/javascript" src="<?php echo $bU; ?>/datepicker/js/locales/bootstrap-datepicker.uk.js"></script>
    <?php } ?>
    
    <!-- Site index (before log in) -->
    <?php if($controller_name == "site" && in_array($action_name , array("index"))){ ?>
      <link rel="stylesheet" type="text/css" 
        href="<?php echo $bU; ?>/prettyPhoto/css/prettyPhoto.css" />
      <script type="text/javascript" src="<?php echo $bU; ?>/prettyPhoto/js/jquery.prettyPhoto.js"></script>
    <?php } ?>

  </head>
  <body>
    <div id="content" class="container-fluid container-offset">
      <?php if(!Yii::app()->user->isGuest){ ?>
      <nav class="navbar navbar-default header-menu"> <!--navbar-fixed-top -->
        <div class="container-fluid">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Навігація</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <!--a class="navbar-brand" href="#">СЕД</a-->
          </div>

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
              <!-- Документи -->
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                <span class="glyphicon glyphicon-file"></span> Документи <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="<?php echo Yii::app()->CreateUrl("documents/index"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-folder-open"></span> Список документів</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php echo Yii::app()->CreateUrl("documents/create"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-plus"></span> Створити документ</a></li>
                </ul>
              </li>
              
              <!-- Документообіг -->
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <span class="glyphicon glyphicon-briefcase"></span> Документообіг <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <?php if (!Yii::app()->user->checkAccess("_FlowsAdmin")){ ?>
                  <li><a href="<?php echo Yii::app()->CreateUrl("flows/index"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-circle-arrow-down"></span> Вхідні розсилки
                      </a>
                  </li>
                  <li><a href="<?php echo Yii::app()->CreateUrl("flows/index?Flows[mode]=from"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-circle-arrow-up"></span> Вихідні розсилки
                      </a>
                  </li>
                  <?php } else { ?>
                  <li><a href="<?php echo Yii::app()->CreateUrl("flows/index"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-circle-arrow-up"></span>
                      <span class="glyphicon glyphicon-circle-arrow-down"></span>
                      Розсилки
                      </a>
                  </li>                  
                  <?php } ?>
                  <li class="divider"></li>
                  <li><a href="<?php echo Yii::app()->CreateUrl("flows/create"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-send"></span> Створити розсилку</a></li>
                </ul>
              </li>
              
              <!-- Планування заходів -->
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <span class="glyphicon glyphicon-calendar"></span> Календар <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="<?php echo Yii::app()->CreateUrl("events/admin"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-list"></span> Перегляд заходів</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php echo Yii::app()->CreateUrl("events/create"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-plus"></span> Створити захід</a></li>
                </ul>
              </li>
              
              <!-- Форми звітів -->
              <li><a href="<?php echo Yii::app()->CreateUrl("site/reports"); ?>">
                  <span class="glyphicon glyphicon-list-alt"></span> Звіти</a></li>
              
              <!-- Сервіс -->
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <span class="glyphicon glyphicon-cog"></span> Сервіс <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  
                  <!-- Користувачі, підрозділи, групи підрозділів -->
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    <span class="glyphicon glyphicon-user"></span> Користувачі <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?php echo Yii::app()->CreateUrl("users/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Користувачі</a></li>
                      <li><a href="<?php echo Yii::app()->CreateUrl("departments/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Підрозділи</a></li>
                      <li><a href="<?php echo Yii::app()->CreateUrl("deptgroups/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Групи підрозділів</a></li>
                    </ul>
                  </li>
                  
                  <!--Довідники -->
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    <span class="glyphicon glyphicon-wrench"></span> Довідники <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?php echo Yii::app()->CreateUrl("doccategories/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Категорії документів</a></li>
                      <li><a href="<?php echo Yii::app()->CreateUrl("doctypes/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Типи документів</a></li>
                      <li class="divider"></li>
                      <li><a href="<?php echo Yii::app()->CreateUrl("eventlevels/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Рівні заходів</a></li>
                      <li><a href="<?php echo Yii::app()->CreateUrl("eventkinds/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Види заходів</a></li>
                      <li class="divider"></li>
                      <li><a href="<?php echo Yii::app()->CreateUrl("periods/index"); ?>" class="small-font">
                          <span class="glyphicon glyphicon-record"></span> Періодичність розсилок</a></li>
                    </ul>
                  </li>
                  
                  <!-- Файли -->
                  <li class="dropdown">
                    <a href="<?php echo Yii::app()->CreateUrl("files/index"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-list"></span> Файли
                    </a>
                  </li>
                  
                  <!-- Допомога і документація-->
                  <li class="divider"></li>
                  <li><a href="http://help.znu.edu.ua" class="small-font">
                      <span class="glyphicon glyphicon-bullhorn"></span> Допомога</a></li>
                  <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/docflow.pdf" class="small-font">
                      <span class="glyphicon glyphicon-question-sign"></span> Документація</a></li>
                </ul>
                
              </li>

            </ul>

            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <span class="glyphicon glyphicon-user"></span> <?php echo Yii::app()->user->name; ?> <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="<?php echo Yii::app()->CreateUrl("site/userinfo"); ?>" class="small-font">
                      <span class="glyphicon glyphicon-info-sign"></span> Інформація</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php echo Yii::app()->CreateUrl('site/logout'); ?>" class="small-font">
                      <span class="glyphicon glyphicon-off"></span> Вийти</a></li>
                </ul>
              </li>
            </ul>
          </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
      </nav>
      <?php } ?>
    
      <?php echo $content; ?>
      <hr/>
      
      <footer>
        <div class="row-fluid">
          <div class="span12 footer">
          © Лабораторія інформаційних систем та комп'ютерних технологій ЗНУ <br/>2013,<?php echo date("Y") ?>
          </div>
        </div>
      </footer>
    </div><!-- content -->
  </body>
</html>
