<?php

class FlowsController extends Controller {
  
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
        array('allow', // allow all flows to perform  actions
            'actions' => array(
              'delanswer', 
            ),
            'roles' => array('Root','DocsAdmin', 'FlowsAdmin'),
        ),
        array('allow', // allow all flows to perform  actions
            'actions' => array(
              'index', 
              'create', 
              'update', 
              'xupdate',
              'answer',
              'delete',
              'getDepartments', 
              'getDocuments'
            ),
            'users' => array('@'),
        ),
        array('deny', // deny all flows
            'users' => array('*'),
        ),
    );
  }

  /**
   * This is the default 'index' action that is invoked
   * when an action is not explicitly requested by flows.
   */
  public function actionIndex() {
    $flows = Yii::app()->request->getParam('Flows',array());
    $model =new Flows('search');
    
    $model->unsetAttributes();
    if(!empty($flows)){
      foreach ($flows as $u_attr => $u_val){
        if (!empty($u_val)){
          $model->$u_attr = $u_val;
        }
      }
    }
    if (!Yii::app()->user->checkAccess('_FlowsAdmin')){
      $model->UserID = Yii::app()->user->id;
    }
    
    $this->render('index',array(
      'model' => $model,
    ));
  }
  
  protected function commonSaver($model){
    $flows = Yii::app()->request->getParam('Flows',array());
    $doc_id = Yii::app()->request->getParam('idDocument',null);
    $f_id = Yii::app()->request->getParam('idFlow',null);
    $ctr_mark = Yii::app()->request->getParam('ControlMark',null);
    $criteria = new CDbCriteria();
    if (!Yii::app()->user->checkAccess('_FlowsAdmin')){
      $model->UserID = Yii::app()->user->id;
      $criteria->with = array(
        '_document_flow_respondent'
      );
      $criteria->together = true;
      $criteria->group = "t.idDocument";
      $criteria->addCondition('(
        t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.intval(Yii::app()->user->id).')
        )
      ) OR (
        _document_flow_respondent.DeptID IN 
         (select ud.DepartmentID from user_department ud where ud.UserID='.intval(Yii::app()->user->id).')
      )');
    }
    if(!empty($flows)){
      foreach ($flows as $u_attr => $u_val){
        //if (is_array($u_val) || strlen(trim($u_val)) > 0 || is_integer($u_val)){
          $model->$u_attr = $u_val;
        //}
      }
      if (!$model->Created){
        $model->Created = date('Y-m-d H:i:s');
      }
      if (!Yii::app()->user->checkAccess('_DocsExtended')){
        $model->PeriodID = 1;
      }
      $cnt_docs = count($model->document_ids);
      if ($cnt_docs > 0){
        $doc_ = Documents::model()->findByPk($model->document_ids[0]);
        $model->FlowName = "Розсилка `".((strlen(trim($doc_->Summary))>0)? $doc_->Summary:$doc_->DocumentName)."`";
        if ($cnt_docs > 1){
          $model->FlowName .= " та ін.";
        }
      }
      if (strlen(trim($ctr_mark))>0){
        $period = "";
        if ($model->PeriodID > 1){
          $period = Periods::model()->findByPk($model->PeriodID)->PeriodName;
        }
        for ($i = 0; $i < $cnt_docs; $i++){
          $criteria1 = new CDbCriteria();
          $criteria1->compare('t.idDocument',$model->document_ids[$i]);
          $criteria1->addCondition('if(t.ControlMark is null,"",t.ControlMark) '
            .'not like "%'.trim($ctr_mark.' '.$period).'%"');
          $doc_ = Documents::model()->find($criteria1);
          if (!$doc_){
            continue;
          }
          if (strlen(trim($doc_->ControlMark)) > 0){
            $doc_->ControlMark .= " / ".$ctr_mark." ".$period;
          } else {
            $doc_->ControlMark = $ctr_mark." ".$period;
          }
          $doc_->force_save = 1;
          if (!$doc_->save()){
            var_dump($doc_->getErrors());exit();
          }
        }
      }
      if ($model->save() ){
        $this->redirect(Yii::app()->CreateUrl('flows/index',
          array('Flows[mode]'=>'from')));
      }
    }
    if ($doc_id > 0){
      $criteria->compare('t.idDocument',intval($doc_id));
      $doc_model = Documents::model()->find($criteria);
      if ($doc_model){
        $model->__flow_documents = array();
        $model->__flow_documents[] = $doc_model;
      }
    }
    if ($f_id > 0 && !$doc_id){
      $ex = Flows::model()->findByPk(intval($f_id));
      if ($ex){
        $model->FlowDescription = $ex->FlowDescription;
        $model->PeriodID = $ex->PeriodID;
        $model->__flow_documents = $ex->__flow_documents;
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
    
  }
  
  public function actionCreate() {
    $model = new Flows; 
    $this->commonSaver($model);
  }
  
  public function actionUpdate($id) {
    $model = Flows::model()->findByPk($id);
    $u = Users::model()->findByPk(Yii::app()->user->id);
    if (implode(",",$u->department_ids) != implode(",",$model->_flow_user->department_ids)
      && !Yii::app()->user->checkAccess("_FlowsAdmin")){
        throw new CHttpException(403, 
          'Редагувати розсилку #'.$model->idFlow.' можуть лише користувачі із підрозділів власника.');
    }
    $model->document_ids = null;
    $model->dept_ids = null;
    //$model->event_ids = null;
    $this->commonSaver($model);
  }
  
  /**
   * Асинхронне оновлення
   */
  public function actionXupdate() {
    $name = Yii::app()->request->getParam('name',false);
    $pk = Yii::app()->request->getParam('pk',false);
    $value = Yii::app()->request->getParam('value',false);
    
    $model = Flows::model()->findByPk($pk);
    $u = Users::model()->findByPk(Yii::app()->user->id);
    if (implode(",",$u->department_ids) != implode(",",$model->_flow_user->department_ids)
      && !Yii::app()->user->checkAccess("_FlowsAdmin")){
        throw new CHttpException(403, 
          'Редагувати розсилку #'.$model->idFlow.' можуть лише користувачі із підрозділів власника.');
    }
    $model->document_ids = null;
    $model->dept_ids = null;
    //$model->event_ids = null;
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
   * Наданння відповіді (інформування)
   */
  public function actionAnswer(){
    $AnswerText = Yii::app()->request->getParam('AnswerText',null);
    $UserID = Yii::app()->request->getParam('UserID',Yii::app()->user->id);
    $flow_id  = Yii::app()->request->getParam('idFlow',0);
    
    $model = new Answers();
    $dept_ids = Users::model()->findByPk($UserID)->department_ids;

    $model->AnswerText = $AnswerText;
    $model->UserID = $UserID;
    $response = array();
    if ( !$model->save() ){
      //випадок помилки збереження повідомлення - власне відповіді
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
      $response['msg'] = array('flow_id' => $flow_id, 'UserID' => $UserID, 'Text' => $AnswerText);
      echo CJSON::encode($response);
      return ;
    } else {
      for ($i = 0; $i < count($dept_ids); $i++){
        $flow_resp = FlowRespondent::model()->findByAttributes(array(
          'FlowID' => $flow_id,
          'DeptID' => $dept_ids[$i]
        ));
        if (!$flow_resp){
          continue;
        }
        $flow_resp->AnswerID = $model->idAnswer;
        if ( !$flow_resp->save() ){
          $response['status'] = 'error';
          $err = $flow_resp->getErrors();
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
          return ;
        }
      }
    }
    $response['status'] = 'success';
    echo CJSON::encode($response);
  }
  
  public function actionDelanswer($id=null){
    $identifier = Yii::app()->request->getParam('id',$id);
    if (!$identifier){
      return false;
    }
    $model = Answers::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
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
    $model = Flows::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
    }
  }
  
  /**
   * Додавання на веб-сторінку елемента x-editable або тексту (залежно від прав користувача) 
   */
  public function echoInfoContainer($model,$attr,$pk,$datatype,$content){
      if (empty($model->_flow_user)){
        echo "відсутній власник документа";
        return ;
      }
      if((implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) 
        != implode(',',$model->_flow_user->department_ids)
        && (!Yii::app()->user->checkAccess('_FlowsAdmin')))){
          echo ( (!strlen(trim($content)))? "<i>немає</i>" 
            : CHtml::encode($content) );
      } else {
        ?>
        <a href='#' class='<?php echo $attr; ?>' 
            data-pk='<?php echo $pk; ?>' 
            data-name='<?php echo $attr; ?>' 
            data-type='<?php echo $datatype; ?>'>
          <?php echo CHtml::encode($content); ?>
        </a>
        <?php
      }
  }
  
  /**
   * Відповідь на розсилку з ID FlowID користувача з ID UserID
   * @param integer $FlowID
   * @param integer $UserID
   * @return Answers
   */
  public function getAnswer($FlowID,$UserID){
    $criteria = new CDbCriteria();
    $criteria->addCondition('t.UserID IN 
      (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
        (select DepartmentID from user_department ud where ud.UserID='.intval($UserID).')
      )');
    $criteria->with = array();
    $criteria->with[] = '_answer_flow_respondents';
    $criteria->together = true;
    
    $criteria->compare('_answer_flow_respondents.FlowID', intval($FlowID));
    $model = Answers::model()->find($criteria);
    return $model;
  }
  
  /**
   * Метод для асинх. вибірки підрозділів у віджетах Many2Many
   * @param string $q параметр для пошуку назви підрозділу
   * @param string $n_ids JSON-закодований масив ідентифікаторів, які не треба вибирати
   */
  public function actionGetDepartments($q=null,$n_ids="[]"){
    $fields = array();
    $criteria = new CDbCriteria();
    $_n_ids = CJSON::decode($n_ids);
    if ($q == "*"){
      $q = null;
    }
    $criteria->compare('DepartmentName', $q, true);
    if (!empty($_n_ids)){
      $criteria->addNotInCondition('idDepartment',$_n_ids);
    }
    $criteria->addCondition('isnull(Hidden) or (Hidden = 0)');
    $criteria->addCondition('idDepartment not in '
      . '(select DepartmentID from user_department ud where ud.UserID='.Yii::app()->user->id.')');
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
   * Метод для асинх. вибірки документів у віджетах Many2Many
   * @param string $q параметр для пошуку назви документів
   * @param string $n_ids JSON-закодований масив ідентифікаторів, які не треба вибирати
   */
  public function actionGetDocuments($q=null,$n_ids="[]"){
    $fields = array();
    $criteria = new CDbCriteria();
    $n_ids = CJSON::decode($n_ids);
    $criteria->with = array();
    $criteria->with[] = '_document_flow_respondent';
    $criteria->select = array('*',
      'CONCAT(t.DocumentName,
        IF(not(isnull(t.Summary) or t.Summary=""),CONCAT("(",t.Summary,")"),"")
       ) as DocumentInfo');
    $criteria->compare('CONCAT(t.DocumentName,
        IF(not(isnull(t.Summary) or t.Summary=""),CONCAT("(",t.Summary,")"),"")
       )', $q, true);
    if(!Yii::app()->user->checkAccess('_FilesAdmin') || !Yii::app()->user->checkAccess('_DocsAdmin')){
      $criteria->addCondition('t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.Yii::app()->user->id.')
        )');
    }
    if (!empty($n_ids)){
      $criteria->addNotInCondition('idDocument',$n_ids);
    }
    $criteria->together = true;
    $criteria->group = 't.idDocument';
    if (!Yii::app()->user->checkAccess('_FlowsAdmin')){
      $criteria->addCondition('(
        t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.Yii::app()->user->id.')
        )
      ) OR (
        _document_flow_respondent.DeptID IN 
         (select ud.DepartmentID from user_department ud where ud.UserID='.Yii::app()->user->id.')
      )');
    }
    $criteria->order = 't.Created DESC';
    $criteria->limit = 100;
    foreach (Documents::model()->findAll($criteria) as $model){
      $fields[] = array(
        'text' => $model->DocumentInfo, 
        'id' => $model->idDocument
      );
    }
    echo CJSON::encode($fields);
  }
}
