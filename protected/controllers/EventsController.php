<?php

class EventsController extends Controller {

  /**
   * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
   * using two-column layout. See 'protected/views/layouts/column2.php'.
   */
  public $layout = '//layouts/main';
  public $defaultAction = 'admin';
  public $wdays = array("нд","пн","вт","ср","чт","пт","сб","нд");
  public $wday_alias = array(
     "нд" => "щонеділі",
     "пн" => "щопонеділка",
     "вт" => "щовівторка",
     "ср" => "щосереди",
     "чт" => "щочетверга",
     "пт" => "щоп`ятниці",
     "сб" => "щосуботи",
  );
  /**
   * @return array action filters
   */
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
        //'postOnly + delete', // we only allow deletion via POST request
    );
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   * @return array access control rules
   */
  public function accessRules() {
    return array(
        array('deny', // deny all anonymous users
            'users' => array('?'),
        ),
        array('allow', //
            'actions' => array('create', 
              'xupdate', 'update', 'attachmentrm'),
            'roles' => array('EventsGeneral', 'EventsAdmin'),
        ),
        array('allow', //
            'actions' => array('eventdatedelete', 'delete'),
            'roles' => array('EventsAdmin'),
        ),
        array('allow', //
            'actions' => array('index','admin','attachment','ajaxcounters'),
            'users' => array('@'),
        ),
        array('deny', // deny all users
            //'actions'=>array('index'),
            'users' => array('*'),
        ),
    );
  }
  
  /**
   * Загальний метод збереження
   * @param Events $model
   * @return Events
   */
  protected function commonSaver($model){
    $attrs = Yii::app()->request->getParam('Events',array());
    $eventdates = Yii::app()->request->getParam('eventdates',array());
    $InvitedComment = Yii::app()->request->getParam('InvitedComment',array());
    $InvitedComment_comment = Yii::app()->request->getParam('InvitedComment_comment',array());
    $OrganizerComment = Yii::app()->request->getParam('OrganizerComment',array());
    
    if(!empty($attrs)){
      //var_dump($_REQUEST);exit();
      foreach ($attrs as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if (isset($attrs['uploaded_file'])){
        $model->uploaded_file = CUploadedFile::getInstance($model, 'uploaded_file');
      }
      $model->UserID = Yii::app()->user->id;
      $model->event_dates = $eventdates;
      $model->invited_names = $InvitedComment;
      $model->invited_seets = $InvitedComment_comment;
      $model->organizer_names = $OrganizerComment;
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('events/index'
          ,array("id" => $model->idEvent)));
      }
    }
    return $model;
  }
  
  public function actionCreate() {
    $model = new Events; 
    $nmodel = $this->commonSaver($model);
    $this->render('create', array(
        'model' => $nmodel,
    ));
  }
  
  public function actionUpdate($id) {
    $model = Events::model()->findByPk($id); 
    $model->organizer_ids = null;
    $model->invited_ids = null;
    $model->organizer_names = null;
    $model->invited_names = null;
    $model->invited_seets = null;
    $model->event_dates = null;
    
    $nmodel = $this->commonSaver($model);
    $this->render('update', array(
        'model' => $nmodel,
    ));
  }
  
  /**
   * Асинхронне оновлення
   */
  public function actionXupdate() {
    $name = Yii::app()->request->getParam('name',false);
    $pk = Yii::app()->request->getParam('pk',false);
    $value = Yii::app()->request->getParam('value',false);
    $model = Events::model()->findByPk($pk);
    $model->organizer_ids = null;
    $model->invited_ids = null;
    $model->organizer_names = null;
    $model->invited_names = null;
    $model->invited_seets = null;
    $model->event_dates = null;
    if ($model && $name){
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
    }
  }


  /**
   * Deletes a particular model.
   * If deletion is successful, the browser will be redirected to the 'admin' page.
   * @param integer $id the ID of the model to be deleted
   */
  public function actionDelete($id) {
    $model = $this->loadModel($id);

    $model->delete();
    // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
    if (!isset($_GET['ajax']))
      $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
  }

  /**
   * Видалення однієї з дат заходу (якщо це остання, то і самого заходу)
   * @param integer $id the ID of the model to be deleted
   * @param string $date date of event
   */
  public function actionEventdatedelete($id,$date) {
    $model = EventDate::model()->findByPkAttributes(array(
      'EventID' => $id,
      'EventDate' => $date
    ));
    if ($model){
      $model->delete();
    } else {
      $id = 0;
    }
    if (!isset($_GET['ajax'])){
      $this->redirect(isset($_POST['returnUrl']) ? 
        $_POST['returnUrl'] : array('admin'));
    }
  }

  /**
   * View separate event.
   */
  public function actionIndex($id,$response = '') {
    $model = $this->loadModel($id);
    $this->render('index', array(
        'model' => $model,
        'response' => $response,
    ));
  }

  /**
   * Manages all models.
   */
  public function actionAdmin() {
    $model = new Events('search');
    $model->unsetAttributes();  // clear any default values
    if (isset($_GET['Events'])){
      $model->attributes = $_GET['Events'];
      if (isset($_GET['Events']['past'])){
        $model->past = $_GET['Events']['past'];
      } else {
        $model->past = 0;
      }
      if (isset($_GET['Events']['date_search'])){
        $model->date_search = $_GET['Events']['date_search'];
      } else {
        $model->date_search = '';
      }
    }
    $this->render('admin', array(
        'model' => $model,
    ));
  }
  
  /**
   * Завантаження прикріпленого файлу
   * @param integer $id ідентифікатор заходу
   * @throws CHttpException
   */
  public function actionAttachment($id){
    $model = Events::model()->findByPk($id);
    if (!$model){
        throw new CHttpException(404, 
          'Захід #'.$id.' не знайдено.');
    }
    if ($model->FileID){
      $this->redirect(Yii::app()->CreateUrl("/files/download",
        array('id' => $model->FileID)));
    } else {
      $this->redirect(Yii::app()->CreateUrl("/events/index",
        array('id' => $model->idEvent)));
    }
  }
  
  /**
   * Видалення прикріпленого файлу
   * @param integer $id ідентифікатор заходу
   * @throws CHttpException
   */
  public function actionAttachmentrm($id){
    $model = Events::model()->findByPk($id);
    if (!$model){
        throw new CHttpException(404, 
          'Захід #'.$id.' не знайдено.');
    }
    if ($model->FileID){
      $model->_event_file->delete();
      $model->FileID = null;
      $model->save();
    }
    $this->redirect(Yii::app()->CreateUrl("events/update",array('id' => $model->idEvent)));
  }
  
  /**
   * Повертає base64-дані прикріпленого файлу як зображення або FALSE,
   *  якщо файл не є зображенням
   * @param integer $id ідентифікатор заходу
   * @return mixed 
   * @throws CHttpException
   */
  protected function embedImageFromAttachment($id){
    $model = Events::model()->findByPk($id);
    if (!$model){
        throw new CHttpException(404, 
          'Захід #'.$id.' не знайдено.');
    }
    if (!$model->FileID){
        return false;
    }
    $fullname = $model->_event_file->folder.$model->_event_file->FileLocation;
    if (!$model->_event_file->exists){
        return false;
    }
    $contents = file_get_contents($fullname);
    $base64   = base64_encode($contents); 
    if(($mime=CFileHelper::getMimeTypeByExtension($fullname))===null)
              $mime='text/plain';
    if (strpos($mime,"image") === false){
      return false;
    }
    return 'data:' . $mime . ';base64,' . $base64;
  }
  
  public function actionAjaxcounters(){
    $_date1 = Yii::app()->request->getParam('date1',date('Y-m-01'));
    $_date2 = Yii::app()->request->getParam('date2',date('Y-m-01',strtotime("next month")));
    $date1 = date("Y-m-d",strtotime($_date1));
    $date2 = date("Y-m-d",strtotime($_date2));
    $list= Yii::app()->db->createCommand('select count(EventID) as cnt,EventDate '
    .'from event_date '
    .'where EventDate between "'.$date1.'" and "'.$date2.'"'
    .'group by EventDate order by EventDate ASC')->queryAll();
    echo CJSON::encode($list);
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer the ID of the model to be loaded
   */
  public function loadModel($id) {
    $model = Events::model()->findByPk($id);
    if ($model === null)
      throw new CHttpException(404, 'The requested page does not exist.');
    return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param CModel the model to be validated
   */
  protected function performAjaxValidation($model) {
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'events-form') {
      echo CActiveForm::validate($model);
      Yii::app()->end();
    }
  }
  
}
