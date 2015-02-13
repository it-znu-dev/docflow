<?php

class EventkindsController extends Controller {
  
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
        'postOnly + delete', // we only allow deletion via POST request
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
              'create', 
              'update', 
              'xupdate',
              'delete', 
            ),
            'roles' => array('Root','EventKindsAdmin'),
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
    $_params = Yii::app()->request->getParam('Eventkinds',array());
    $model =new Eventkinds('search');
    
    $model->unsetAttributes();
    if(!empty($_params)){
      foreach ($_params as $_attr => $_val){
        $model->$_attr = $_val;
      }
    }
    $this->render('index',array(
      'model' => $model
    ));
  }
  
  public function actionCreate() {
    $_params = Yii::app()->request->getParam('Eventkinds',array());
    $model = new Eventkinds; 
    
    if(!empty($_params)){
      foreach ($_params as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('eventkinds'));
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  public function actionUpdate($id) {
    $_params = Yii::app()->request->getParam('Eventkinds',array());
    $model = Eventkinds::model()->findByPk($id); 
    
    if(!empty($_params)){
      foreach ($_params as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('eventkinds'));
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  /**
   * Асинхронне оновлення
   */
  public function actionXupdate() {
    $name = Yii::app()->request->getParam('name',false);
    $pk = Yii::app()->request->getParam('pk',false);
    $value = Yii::app()->request->getParam('value',false);
    $model = Eventkinds::model()->findByPk($pk);
    
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
   * Видалення
   */
  public function actionDelete($id=null) {
    $identifier = Yii::app()->request->getParam('id',$id);
    if (!$identifier){
      return false;
    }
    $model = Eventkinds::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
    }
  }
  

}
