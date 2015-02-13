<?php

class SiteController extends Controller {
  /**
   * Declares class-based actions.
   */
  public function actions() {
    return array(
        // captcha action renders the CAPTCHA image displayed on the contact page
        'captcha' => array(
            'class' => 'CCaptchaAction',
            'backColor' => 0xFFFFFF,
        ),
        // page action renders "static" pages stored under 'protected/views/site/pages'
        // They can be accessed via: index.php?r=site/page&view=FileName
        'page' => array(
            'class' => 'CViewAction',
        ),
    );
  }
  
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
    );
  }
  
  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   * @return array access control rules
   */
  public function accessRules() {
    return array(
        array('allow', // allow all users to perform  actions
            'actions' => array(
              'index', 
              'login', 
              'error'
            ),
            'users' => array('*'),
        ),
        array('allow', // allow all users to perform  actions
            'actions' => array(
              'logout',
              'userinfo',
              'reports',
              'doclisttoxls',
              'rept1',
              'rept2'
            ),
            'users' => array('@'),
        ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }

  /**
   * This is the default 'index' action that is invoked
   * when an action is not explicitly requested by users.
   */
  public function actionIndex() {
    if (!Yii::app()->user->isGuest){
      if (Yii::app()->user->checkAccess('_UsersAdmin')){
        $this->redirect(Yii::app()->CreateUrl("users"));
        return ;
      }
      $this->redirect(Yii::app()->CreateUrl("flows"));
    }
    $model = new LoginForm;
    $this->render('index',array(
      'model' => $model
    ));
  }
  
  /**
   * Displays the login page
   */
  public function actionLogin() {
    $model = new LoginForm;
    $this->layout = "//layouts/main";
    // if it is ajax validation request
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }

    // collect user input data
    if (isset($_POST['LoginForm'])) {
      $model->attributes = $_POST['LoginForm'];
      // validate user input and redirect to the previous page if valid
      if ($model->validate() && $model->login())
        $this->redirect(Yii::app()->user->returnUrl);
    }
    // display the login form*/

    $this->render('login', array('model' => $model));
  }

 /**
   * This is the action to handle external exceptions.
   */
  public function actionError() {
    $this->layout = '//layouts/main';
    if ($error = Yii::app()->errorHandler->error) {
      if (Yii::app()->request->isAjaxRequest) {
        echo $error['message'];
      } else {
        $this->render('error', $error);
      }
    }
  }
  
  /**
   * Logs out the current user and redirect to homepage.
   */
  public function actionLogout() {
    Yii::app()->user->logout();
    $this->redirect(Yii::app()->homeUrl);
  }
  
  /**
   * Інформація про користувача
   * @return type
   */
  public function actionUserinfo(){
    $name = Yii::app()->request->getParam('name',"");
    $value = Yii::app()->request->getParam('value',null);
    $pk = Yii::app()->request->getParam('pk',0);
    $model = Users::model()->findByPk(Yii::app()->user->id);
    $permitted_fields = array(
      "info",
      "contacts",
      "password"
    );
    if ($model && in_array($name,$permitted_fields) 
      && (Yii::app()->user->id == $pk)){
      $model->$name = $value; 
      if (!$model->save()){
        $response = array();
        $response['status'] = 'error';
        $err = $model->getErrors();
        $err_msg = array();
        foreach ($err as $err_arr){
          if (is_string($err_arr)){
            $err_msg[] = $err_arr;
          } else {
            $err_msg[] = implode('; ',$err_arr);
          }
        }
        $response['msg'] = $err_msg;
        echo CJSON::encode($response);
      }
      return ;
    }
    $this->layout = '//layouts/main';
    $this->render("userinfo",array(
      "model" => $model
    ));
  }
  
  public function actionReports(){
    $this->layout = '//layouts/main';
    $this->render("reports");
  }
  
  /**
   * Журнал
   */
  public function actionDoclisttoxls(){
    $defaultLimitValue = 100;
    $reqLimit = Yii::app()->request->getParam('limit', $defaultLimitValue);
    $reqDateFrom = Yii::app()->request->getParam('datefrom', null);
    $reqDateTo = Yii::app()->request->getParam('dateto', null);

    if (!is_numeric($reqLimit) || !$reqLimit) {
      $reqLimit = $defaultLimitValue;
    }

    //Перетворення дат з формату d.m.Y у формат Y-m-d
    if ($reqDateFrom) {
      $t = strtotime(str_replace('.', '-', $reqDateFrom));
      $reqDateFrom = date('Y-m-d', $t);
    }
    if ($reqDateTo) {
      $t = strtotime(str_replace('.', '-', $reqDateTo));
      $reqDateTo = date('Y-m-d', $t);
    }

    $reqCategory = Yii::app()->request->getParam('category', null);
    $this->layout = 'clear';
    $criteria = new CDbCriteria();
    $criteria->with = array();
    $criteria->with += array('_document_flow_respondent' => 
      array('select' => false));
    $criteria->with += array('_document_submit' => 
      array('select' => false));
    
    $criteria->together = true;
    $criteria->group = 't.idDocument';
      $criteria->addCondition('(
        t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.intval(Yii::app()->user->id).')
        )
      )');
    $criteria->order = 'Created desc';
    $criteria->compare('CategoryID',(($reqCategory && is_numeric($reqCategory)) ? $reqCategory : null));
    $criteria->addCondition('Created'
            . ' BETWEEN STR_TO_DATE("' . $reqDateFrom . ' 00:00:00",  "%Y-%m-%d %H:%i:%s" ) '
            . '  AND STR_TO_DATE("' . $reqDateTo . ' 23:59:59",  "%Y-%m-%d %H:%i:%s" )'
            . '  AND Visible > 0');
    $criteria->limit = $reqLimit;
    $models = Documents::model()->findAll($criteria);
    $this->render('xlsdoclist', array(
        'models' => $models
    ));
  }
  
  //ДАЛІ -- звіти
  public function actionRept1(){
    $data = array();
    $year = Yii::app()->request->getParam('year',date('Y'));
    $year = date('Y',strtotime($year.'-01-01'));
    $data= Yii::app()->db->createCommand('select 
 dcc.CategoryName as `cat`,
(select count(idDocument)
 from  doccategories dc  
  left join documents on dc.idCategory=documents.CategoryID 
  left join user_department ud on ud.UserID=documents.UserID 
 where ((Created > "'.$year.'-01-01" and Created <= "'.$year.'-12-31" 
   -- Загальний відділ, Лебедєва Ольга Ігорівна, Романова Наталя Василівна, Овечко Наталя Сергіївна
   and ud.DepartmentID in (46,121,119,118)) )
   and dc.idCategory=dcc.idCategory and Visible is not null 
 group by idCategory
) as `at_all`,

(select count(idDocument)
 from  doccategories dc  
  left join documents on dc.idCategory=documents.CategoryID 
  left join user_department ud on ud.UserID=documents.UserID 
 where ((Created > "'.$year.'-01-01" and Created <= "'.$year.'-12-31" 
   -- Загальний відділ, Лебедєва Ольга Ігорівна, Романова Наталя Василівна, Овечко Наталя Сергіївна
   and ud.DepartmentID in (46,121,119,118) 
   and trim(if(isnull(ControlMark),"",ControlMark)) not like "")) 
   and dc.idCategory=dcc.idCategory 
   and Visible is not null 
 group by idCategory  
) as `control_mark`,

(select count(idDocument)
 from  doccategories dc 
  left join documents on dc.idCategory=documents.CategoryID 
  left join user_department ud on ud.UserID=documents.UserID 
 where ((Created > "'.$year.'-01-01" and Created <= "'.$year.'-12-31" 
   -- Загальний відділ, Лебедєва Ольга Ігорівна, Романова Наталя Василівна, Овечко Наталя Сергіївна
   and ud.DepartmentID in (46,121,119,118) 
   and trim(if(isnull(ControlMark),"",ControlMark)) not like "" 
   and trim(if(isnull(DoneMark),"",DoneMark)) not like "" )) 
   and dc.idCategory=dcc.idCategory  
   and Visible is not null 
 group by idCategory
) as `done_mark`
 from  doccategories dcc')->queryAll();
    $this->layout = 'clear';
    //var_dump($data);exit();
    $this->render('xls_rept1',array(
      'data'=>$data,
      'year' => $year
    ));
  }
  
  public function actionRept2(){
    $data = array();
    $year = Yii::app()->request->getParam('year',date('Y'));
    $year = date('Y',strtotime($year.'-01-01'));
    $data= Yii::app()->db->createCommand("select 
idDepartment,
DepartmentName,
(select count(distinct d_f.DocumentID) from 
  flow_respondent f_r 
  left join flows fl on fl.idFlow=f_r.FlowID
  left join document_flow d_f on d_f.FlowID=f_r.FlowID 
  left join documents docs on docs.idDocument=d_f.DocumentID 
 where f_r.DeptID=departments.idDepartment 
  and (trim(if(isnull(docs.ControlMark),'',docs.ControlMark)) not like '') 
  and fl.UserID in (select UserID from user_department u_d where u_d.DepartmentID = 46)
) as zagalom,

(select count(distinct d_f.DocumentID) from 
  flow_respondent f_r 
  left join flows fl on fl.idFlow=f_r.FlowID
  left join document_flow d_f on d_f.FlowID=f_r.FlowID 
  left join documents docs on docs.idDocument=d_f.DocumentID 
  left join document_submit d_s on docs.idDocument=d_s.DocumentID 
 where f_r.DeptID=departments.idDepartment 
  and (trim(if(isnull(docs.ControlMark),'',docs.ControlMark)) not like '') 
  and fl.UserID in (select UserID from user_department u_d where u_d.DepartmentID = 46)
  and d_s.SubmissionInfo like '%.".$year."%' 
) as za_rik,

(select count(distinct d_f.DocumentID) from 
  flow_respondent f_r 
  left join flows fl on fl.idFlow=f_r.FlowID
  left join document_flow d_f on d_f.FlowID=f_r.FlowID 
  left join documents docs on docs.idDocument=d_f.DocumentID 
  left join document_submit d_s on docs.idDocument=d_s.DocumentID 
 where f_r.DeptID=departments.idDepartment 
  and (trim(if(isnull(docs.ControlMark),'',docs.ControlMark)) not like '') 
  and fl.UserID in (select UserID from user_department u_d where u_d.DepartmentID = 46)
  and d_s.SubmissionInfo like '%.".$year."%' 
  and (trim(if(isnull(docs.DoneMark),'',docs.DoneMark)) not like '') 
) as vykonano

from departments 
where idDepartment not in (136,120,123,125,127,126,129,124,128,130,122,118,119,132,133,1,121) 
order by DepartmentName
")->queryAll();
    $this->layout = 'clear';
    //var_dump($data);exit();
    $this->render('xls_rept2',array(
      'data'=>$data,
      'year' => $year
    ));
  }


}
