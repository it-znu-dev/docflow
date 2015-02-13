<?php

class DepartmentsController extends Controller {
  
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
              'create', 
              'update', 
              'xupdate',
              'delete'
            ),
            'roles' => array('Root','UsersAdmin'),
        ),
        array('allow', // allow all users to perform  actions
            'actions' => array(
              'index', 
              'getDeptGroups'
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
    $departments = Yii::app()->request->getParam('Departments',array());
    $model =new Departments('search');
    
    $model->unsetAttributes();
    if(!empty($departments)){
      foreach ($departments as $d_attr => $d_val){
        $model->$d_attr = $d_val;
      }
    }
    $this->render('index',array(
      'model' => $model
    ));
  }
  
  public function actionCreate() {
    $departments = Yii::app()->request->getParam('Departments',array());
    $model = new Departments; 
    
    if(!empty($departments)){
      foreach ($departments as $d_attr => $d_val){
        $model->$d_attr = $d_val;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('departments'));
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  public function actionUpdate($id) {
    $departments = Yii::app()->request->getParam('Departments',array());
    $model = Departments::model()->findByPk($id); 
    
    if(!empty($departments)){
      foreach ($departments as $d_attr => $d_val){
        $model->$d_attr = $d_val;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('departments'));
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
    $model = Departments::model()->findByPk($pk);
    
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
    $model = Departments::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
    }
  }
  
  /**
   * Метод для асинх. вибірки груп підрозділів у віджетах Many2Many
   * @param string $q параметр для пошуку назви групи підрозділів
   * @param string $n_ids JSON-закодований масив ідентифікаторів, які не треба вибирати
   */
  public function actionGetDeptGroups($q=null,$n_ids="[]"){
    $fields = array();
    $criteria = new CDbCriteria();
    $n_ids = CJSON::decode($n_ids);
    $criteria->compare('DeptGroupName', $q, true);
    if (!empty($n_ids)){
      $criteria->addNotInCondition('idDeptGroup',$n_ids);
    }
    $criteria->order = 'DeptGroupName ASC';
    foreach (Deptgroups::model()->findAll($criteria) as $model){
      $fields[] = array(
        'text' => $model->DeptGroupName, 
        'id' => $model->idDeptGroup
      );
    }
    echo CJSON::encode($fields);
  }
  

}
