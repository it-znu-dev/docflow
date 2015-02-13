<?php

class UsersController extends Controller {
  
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
            ),
            'users' => array('@'),
        ),
        array('allow', // allow all users to perform  actions
            'actions' => array(
              'create', 
              'update', 
              'xupdate',
              'delete', 
              'getDepartments', 
              'getRoles', 
            ),
            'roles' => array('Root','UsersAdmin'),
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
    $users = Yii::app()->request->getParam('Users',array());
    $model =new Users('search');
    
    $model->unsetAttributes();
    if(!empty($users)){
      foreach ($users as $u_attr => $u_val){
        if (!empty($u_val)){
          $model->$u_attr = $u_val;
        }
      }
    }
    $this->render('index',array(
      'model' => $model
    ));
  }
  
  public function actionCreate() {
    $users = Yii::app()->request->getParam('Users',array());
    $model = new Users; 
    
    $model->department_ids = null;
    $model->role_ids = null;
    
    if(!empty($users)){
      foreach ($users as $u_attr => $u_val){
        if (!empty($u_val)){
          $model->$u_attr = $u_val;
        }
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('users'));
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  public function actionUpdate($id) {
    $users = Yii::app()->request->getParam('Users',array());
    $model = Users::model()->findByPk($id); 
    
    $model->department_ids = null;
    $model->role_ids = null;
    
    if(!empty($users)){
      foreach ($users as $u_attr => $u_val){
        if (!empty($u_val)){
          $model->$u_attr = $u_val;
        }
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('users'));
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
    $model = Users::model()->findByPk($pk);
    
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
    $model = Users::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
    }
  }
  
  /**
   * Метод для асинх. вибірки підрозділів у віджетах Many2Many
   * @param string $q параметр для пошуку назви підрозділу
   * @param string $n_ids JSON-закодований масив ідентифікаторів, які не треба вибирати
   */
  public function actionGetDepartments($q=null,$n_ids="[]"){
    $fields = array();
    $criteria = new CDbCriteria();
    $n_ids = CJSON::decode($n_ids);
    $criteria->compare('DepartmentName', $q, true);
    if (!empty($n_ids)){
      $criteria->addNotInCondition('idDepartment',$n_ids);
    }
    $criteria->addCondition('isnull(Hidden) or (Hidden = 0)');
    $criteria->order = 'DepartmentName ASC';
    foreach (Departments::model()->findAll($criteria) as $model){
      $fields[] = array(
        'text' => $model->DepartmentName, 
        'id' => $model->idDepartment
      );
    }
    echo CJSON::encode($fields);
  }
  
  /**
   * Метод для асинх. вибірки ролей у віджетах Many2Many
   * @param string $q параметр для пошуку ролі
   * @param string $n_ids JSON-закодований масив ідентифікаторів, які не треба вибирати
   */
  public function actionGetRoles($q=null,$n_ids="[]"){
    $fields = array();
    $criteria = new CDbCriteria();
    $n_ids = CJSON::decode($n_ids);
    $criteria->compare('name', $q, true);
    if (!empty($n_ids)){
      $criteria->addNotInCondition('name',$n_ids);
    }
    $criteria->addCondition('type=2');
    $criteria->order = 'name ASC';
    foreach (Roles::model()->findAll($criteria) as $model){
      $fields[] = array(
        'text' => $model->name, 
        'id' => $model->name
      );
    }
    echo CJSON::encode($fields);
  }

}
