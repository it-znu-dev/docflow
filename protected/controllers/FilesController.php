<?php

class FilesController extends Controller {
  
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
              'download', 
              'downloadAll',
              'xupdate',
              'getDocuments', 
              'hide', 
            ),
            'users' => array('@'),
        ),
        array('allow',
            'actions' => array(
              'delete', 
            ),
            'roles' => array('Root','FilesAdmin'),
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
    $attrs = Yii::app()->request->getParam('Files',array());
    $model =new Files('search');
    
    $model->unsetAttributes();
    if(!empty($attrs)){
      foreach ($attrs as $_attr => $_val){
        $model->$_attr = $_val;
      }
    }
    if (!Yii::app()->user->checkAccess('_FilesAdmin')){
      $model->UserID = Yii::app()->user->id;
    }
    $this->render('index',array(
      'model' => $model
    ));
  }
  
  public function actionCreate() {
    $attrs = Yii::app()->request->getParam('Files',array());
    $model = new Files; 
    
    if(!empty($attrs)){
      foreach ($attrs as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if (isset($attrs['file_itself'])){
        $model->file_itself = CUploadedFile::getInstance($model, 'file_itself');
      }
      if (!Yii::app()->user->checkAccess('_FilesAdmin')){
        $model->UserID = Yii::app()->user->id;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('files'));
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  public function actionUpdate($id) {
    $attrs = Yii::app()->request->getParam('Files',array());
    $model = Files::model()->findByPk($id); 
    $u = Users::model()->findByPk(Yii::app()->user->id);
    if (implode(",",$u->department_ids) != implode(",",$model->_file_user->department_ids)
      && !Yii::app()->user->checkAccess("_FilesAdmin")){
        throw new CHttpException(403, 
          'Редагувати файл #'.$model->idFile.' можуть лише користувачі із підрозділів власника.');
    }
    if(!empty($attrs)){
      //var_dump($attrs);exit();
      foreach ($attrs as $_attr => $_val){
        $model->$_attr = $_val;
      }
      if (isset($attrs['file_itself'])){
        $model->file_itself = CUploadedFile::getInstance($model, 'file_itself');
      }
      if (!Yii::app()->user->checkAccess('_FilesAdmin')){
        $model->UserID = Yii::app()->user->id;
      }
      if ($model->save()){
        $this->redirect(Yii::app()->CreateUrl('files'));
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
    $model = Files::model()->findByPk($pk);
    $u = Users::model()->findByPk(Yii::app()->user->id);
    if (implode(",",$u->department_ids) != implode(",",$model->_file_user->department_ids)
      && !Yii::app()->user->checkAccess("_FilesAdmin")){
        throw new CHttpException(403, 
          'Редагувати файл #'.$model->idFile.' можуть лише користувачі із підрозділів власника.');
    }
    if (in_array($name,array('FileLocation','Created','FileName')) && 
        !Yii::app()->user->checkAccess('_FilesAdmin')){
      $response['status'] = 'error';
      $response['msg'] = array('Не можна змінювати це поле');
      echo CJSON::encode($response);
      return ;
    }
    
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
    $model = Files::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
    }
  }
  
  public function actionHide($id){
    $model = Files::model()->findByPk($id);
    if ($model && ($model->UserID == Yii::app()->user->id)){
      $model->Visible = 0;
      return $model->save();
    }
    return false;
  }
  
    /**
    * Метод завантаження (скачування).
    * @param integer $id ID : files.idFile
    * @return bool Result of Yii::app()->getRequest()->sendFile function
    * @throws CHttpException
    */
  public function actionDownload($id=null) {
    $identifier = Yii::app()->request->getParam('id',$id);
    if (!$identifier){
      return false;
    }
    $model = Files::model()->findByPk($identifier);
    if ($model){
      if (!$model->exists) {
        throw new CHttpException(404, 'Помилка: файл ' .
        $model->FileLocation .
        ' не знайдено. Можливо він був видалений або виникла помилка при його завантаженні.');
      }
      $path = $model->folder;
      //визначення шляху, де знаходиться файл
      $file_entity = $path . $model->FileLocation;
      //розширення файлу
      $ext = pathinfo($file_entity, PATHINFO_EXTENSION);
      $valid_name = $model->idFile;
      if (strlen(trim($model->FileName))){
        $valid_name = $model->FileName;
      }
      if (!empty($model->_file_documents)){
        $d = $model->_file_documents[0];
        /* @var $d Documents */
        $valid_name = $d->DocumentName;
        if (strlen(trim(($d->Summary)))){
          $valid_name = $this->cutStrByWords($d->Summary,200);
        }
      }
      //заміна усих "негарних" символів для формування імені файлу
      $valid_name = preg_replace('/[\s\/\\<>?\}\{\[\]\@\"\']/', 
              '_', $valid_name) . '.' . $ext;
      $mime = NULL;
      $mimeType=CFileHelper::getMimeTypeByExtension($file_entity);
      if (stristr($mimeType,'pdf') !== false){
        $this->sendInline($file_entity,$valid_name);
      }
      
      if (stristr($mimeType,'image') !== false){
        $this->sendInline($file_entity,$valid_name);
      }
      return Yii::app()->getRequest()->sendFile($valid_name, file_get_contents($file_entity), $mime);
    } else {
      throw new CHttpException(404, 'Файл з #'.$id.' не знайдено.');
    }
  }
  
    /**
    * Метод повернення файлу із вставкою в браузер (якщо підтримується)
    * @param string $file_location
    * @param string $str
    * @throws CHttpException
    */
  protected function sendInline($file_entity, $valid_name){
    if(($mimeType=CFileHelper::getMimeTypeByExtension($file_entity))===null)
            $mimeType='text/plain';
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header("Content-type: $mimeType");
    if(ob_get_length()===false)
            header('Content-Length: '.(function_exists('mb_strlen') ? mb_strlen($content,'8bit') : strlen($content)));
    header("Content-Disposition: inline; filename=\"$valid_name\"");
    header('Content-Transfer-Encoding: binary');
    Yii::app()->end(0,false);
    echo file_get_contents($file_entity);
    exit(0);
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
