<?php

class DeptgroupsController extends Controller {
  
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
              'getDepartments',
              'x'
            ),
            'users' => array('@'),
        ),
        array('allow', // allow all users to perform  actions
            'actions' => array(
              'create', 
              'update', 
              'xupdate',
              'delete',
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
    $_receive = Yii::app()->request->getParam('Deptgroups',array());
    $model =new Deptgroups('search');
    
    $model->unsetAttributes();
    if(!empty($_receive)){
      foreach ($_receive as $_attr => $_val){
        $model->$_attr = $_val;
      }
    }
    $this->render('index',array(
      'model' => $model
    ));
  }
  
  public function actionCreate() {
    $_receive = Yii::app()->request->getParam('Deptgroups',array());
    $model = new Deptgroups; 
    
    if(!empty($_receive)){
      foreach ($_receive as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('deptgroups'));
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  public function actionUpdate($id) {
    $_receive = Yii::app()->request->getParam('Deptgroups',array());
    $model = Deptgroups::model()->findByPk($id); 
    
    if(!empty($_receive)){
      foreach ($_receive as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('deptgroups'));
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
    
    $model = Deptgroups::model()->findByPk($pk);
    
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
    $model = Deptgroups::model()->findByPk($identifier);
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
    $criteria->addCondition('Hidden is null or Hidden = 0');
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
   * Формує json-закодовані дані <code>{<br/>
   *  <b>options</b>: [{<br/>
   *   id: /ID групи підрозділів-респондентів/, <br/>
   *   text: /назва групи/}, {...}, ...],<br/>
   *  <b>items</b>: [{<br/>
   *   id: /ID підрозділу/, <br/>
   *   text: /назва підрозділу/}, {...}, ...] /опціонально/<br/>
   * }</code>
   * @param integer $id ідентифікатор групи
   * @param string $n_ids json-закодований масив ідентифікаторів підрозділів, які не треба включати у вибірку
   * @return string (JSON-об`єкт)
   */
  public function actionX($id=0, $n_ids="[]"){
    $req = array();
    $req['options'] = array();
    $group_counts = array();
    $criteria = new CDbCriteria();
    $gcriteria = new CDbCriteria();
    $n_ids = CJSON::decode($n_ids);
    if (!empty($n_ids)){
      $criteria->addNotInCondition('idDepartment',$n_ids);
      for ($i = 0; $i < count($n_ids); $i++){
        if (intval($n_ids[$i]) < 1){
          continue;
        }
        $m = Departments::model()->findByPk($n_ids[$i]);
        foreach ($m->_department_deptgroups as $gr){
          if (!isset($group_counts[$gr->idDeptGroup])){
            $group_counts[$gr->idDeptGroup] = 1;
          } else {
            $group_counts[$gr->idDeptGroup]++;
          }
        }
      }
    }
    //якщо надійшов ІД групи підрозділів
    if (intval($id) > 0){
      $req['items'] = array();
      $criteria->with = array(
        '_department_deptgroups'
      );
      $criteria->compare('_department_deptgroups.idDeptGroup',intval($id));
      $criteria->addCondition('idDepartment not in '
        . '(select DepartmentID from user_department ud where ud.UserID='.Yii::app()->user->id.')');
      $criteria->addCondition('if(t.Hidden is null,1,t.Hidden=0)');
      $criteria->group = 't.idDepartment';
      $criteria->order = 't.DepartmentName';
      $criteria->together =true;
      foreach (Departments::model()->findAll($criteria) as $model){
        /* @var $model Departments */
          $v = array();
          $v['id'] = $model->idDepartment;
          $v['text'] = $model->DepartmentName;
          $req['items'][] = $v;
      }
    }
    $gcriteria->order = 'DeptGroupName';
    $v = array();
    $v['id'] = 0;
    $v['text'] = "-- можна обрати групу респондентів --";
    $req['options'][] = $v;
    foreach (Deptgroups::model()->findAll($gcriteria) as $g){
      /* @var $g Deptgroups */
      if ( (isset($group_counts[$g->idDeptGroup]) 
           &&  $group_counts[$g->idDeptGroup] 
               == count($g->_deptgroup_department_deptgroup)
        ) || ($id > 0 && $g->idDeptGroup == $id)){
        continue;
      }
      $v = array();
      $v['id'] = $g->idDeptGroup;
      $v['text'] = $g->DeptGroupName;
      $req['options'][] = $v;
    }
    if (count($req['options']) == 1){
      $v = array();
      $req['options'] = array();
      $v['id'] = 0;
      $v['text'] = "-- немає груп або усі можливі обрано --";
      $req['options'][] = $v;
    }
    echo CJSON::encode($req);
    return true;
  }
  

}
