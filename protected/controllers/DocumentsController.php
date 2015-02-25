<?php

class DocumentsController extends Controller {
  
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
        array('allow', // allow all documents to perform  actions
            'actions' => array(
              'delete', 
              'temp'
            ),
            'roles' => array('Root','DocsAdmin'),
        ),
        array('allow', // allow all documents to perform  actions
            'actions' => array(
              'index', 
              'create', 
              'update', 
              'xupdate',
              'typeahead',
              'downloadZip',
              'cardprint',
              'expectedIndex',
              'searchIndexAndShowInfo'
            ),
            'users' => array('@'),
        ),
        array('deny', // deny all documents
            'users' => array('*'),
        ),
    );
  }

  /**
   * This is the default 'index' action that is invoked
   * when an action is not explicitly requested by documents.
   */
  public function actionIndex() {
    $documents = Yii::app()->request->getParam('Documents',array());
    $model =new Documents('search');
    
    $model->unsetAttributes();
    if(!empty($documents)){
      foreach ($documents as $u_attr => $u_val){
        if (!empty($u_val)){
          $model->$u_attr = $u_val;
        }
      }
    }
    
    if (!Yii::app()->user->checkAccess('_DocsAdmin')){
      $model->UserID = Yii::app()->user->id;
      $model->Visible = 1;
    }
    
    $this->render('index',array(
      'model' => $model,
    ));
  }
  
  public function actionCreate() {
    $documents = Yii::app()->request->getParam('Documents',array());
    $model = new Documents; 
    $model->file_ids = null;
    //$model->flow_ids = null;
    $model->SubmissionDate = date("d.m.Y");
    if(!Yii::app()->user->checkAccess('_DocsAdmin')){
      $model->UserID = Yii::app()->user->id;
    }
    if(!Yii::app()->user->checkAccess('_DocsExtened') 
      && !Yii::app()->user->checkAccess('_DocsAdmin')){
      $cat_model = Doccategories::model()->find("CategoryName LIKE 'Без категорії'");
      if ($cat_model){
        $model->CategoryID = $cat_model->idCategory;
      }
      $type_model = Doctypes::model()->find("TypeName LIKE 'Без типу'");
      if ($type_model){
        $model->TypeID = $type_model->idType;
      }
    }
    if(!empty($documents)){
      foreach ($documents as $u_attr => $u_val){
        if (strlen(trim($u_val)) > 0 || is_integer($u_val)){
          $model->$u_attr = $u_val;
        }
      }
      if (isset($documents['file'])){
        $file = $model->file;
        //var_dump($file);
        $model->uploaded_file = CUploadedFile::getInstance($model, 'file');
        //var_dump($model->uploaded_file);exit();
      }
      if (!$model->Created){
        $model->Created = date('Y-m-d H:i:s');
      }
      if ($model->save() ){
        $this->redirect(Yii::app()->CreateUrl('documents/index',
          array('Documents[CategoryID]'=>$model->CategoryID)));
      } else {
        $model->SubmissionDate = ((strlen($model->SubmissionDate))? 
          date("d.m.Y",strtotime($model->SubmissionDate)):"");
      }
    }
    $this->render('_form',array(
      'model' => $model
    ));
  }
  
  public function actionUpdate($id) {
    $documents = Yii::app()->request->getParam('Documents',array());
    $model = Documents::model()->findByPk($id);
    $u = Users::model()->findByPk(Yii::app()->user->id);
    if (implode(",",$u->department_ids) != implode(",",$model->_document_user->department_ids)
      && !Yii::app()->user->checkAccess("_DocsAdmin")){
        throw new CHttpException(403, 
          'Редагувати документ #'.$model->idDocument.' можуть лише користувачі із підрозділів власника.');
        exit();
    }
    $model->file_ids = null;
    //$model->flow_ids = null;
    if(!Yii::app()->user->checkAccess('_DocsAdmin') && !$model->UserID){
      $model->UserID = Yii::app()->user->id;
    }
    if(!empty($documents)){
      foreach ($documents as $u_attr => $u_val){
        if (strlen(trim($u_val)) > 0 || is_integer($u_val)){
          $model->$u_attr = $u_val;
        }
      }
      $model->SubmissionDate = ((strlen($model->SubmissionDate))? 
        date("d.m.Y",strtotime($model->SubmissionDate)):date("d.m.Y"));
      if (isset($documents['file'])){
        $file = $documents['file'];
        $model->uploaded_file = CUploadedFile::getInstance($model, 'file');
      }
      if ($model->save() ){
        if (!Yii::app()->request->isAjaxRequest){
          $this->redirect(Yii::app()->CreateUrl('documents/index',
            array('Documents[CategoryID]'=>$model->CategoryID)));
        } else {
          $nmodel = Documents::model()->findByPk($model->idDocument);
          echo CJSON::encode($nmodel->file_ids);
          return ;
        }
      } else if(Yii::app()->request->isAjaxRequest){
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
        return ;
      }
    }
    $model->SubmissionDate = ((strlen($model->SubmissionDate))? 
      date("d.m.Y",strtotime($model->SubmissionDate)):"");
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
    $u = Users::model()->findByPk(Yii::app()->user->id);
    if($name == 'SubmissionInfo'){
      $model = DocumentSubmit::model()->find('DocumentID='.intval($pk));
      if (implode(",",$u->department_ids) != implode(",",$model->document->_document_user->department_ids)
        && !Yii::app()->user->checkAccess("_DocsAdmin")){
          throw new CHttpException(403, 
            'Редагувати документ #'.$model->idDocument.' можуть лише користувачі із підрозділів власника.');
      }
    } else {
      $model = Documents::model()->findByPk($pk);
      if (implode(",",$u->department_ids) 
         != implode(",",$model->_document_user->department_ids)
        && !Yii::app()->user->checkAccess("_DocsAdmin")){
          throw new CHttpException(403, 
            'Редагувати документ #'.$model->idDocument.' можуть лише користувачі із підрозділів власника.');
      }
    }
    if ($model && $name){
      if ($name == "CategoryID" || $name == "SubmissionDate"){
        $model->SubmissionIndex = null;
      }
      $model->$name = $value;
      if (!strlen($model->SubmissionDate)){
        $model->SubmissionDate = date("d.m.Y");
      }
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
   * Метод для асинх. вибірки значень для елементів typeahead
   * @param string $name назва атрибуту для пошуку
   * @param string $q токен для пошуку
   */
  public function actionTypeahead($name=null,$q=null){
    $_q = Yii::app()->request->getParam('q',$q);
    $_name = Yii::app()->request->getParam('name',$name);
    $fields = array();
    $doc_model = new Documents();
    if ($doc_model->hasAttribute($name) && mb_strlen($q,'utf-8') >= 1){
      $criteria = new CDbCriteria();
      $criteria->compare($name, $q, ($name != "SubmissionIndex"));
      $criteria->addInCondition("YEAR(SubmissionDate)",array(date("Y")));
      $criteria->addCondition('(Visible is not null) and (Visible > 0)');
      $criteria->order = $name.' ASC';
      $criteria->group = $name;
      foreach (Documents::model()->findAll($criteria) as $model){
        $fields[] = $model->$name;
      }
    }
    echo CJSON::encode($fields);
  }
  
  /**
   * Метод для завантаження zip-архіву всіх файлів, пов’язканих з документом
   * @param int $id ідентифікатор документа
   */
  public function actionDownloadZip($id){
    $model = Documents::model()->findByPk(intval($id));
    if (!$model){
        throw new CHttpException(404, 'Документ з ІД ' 
              . $id . ' не знайдено.');
    }
    if (empty($model->file_ids)){
        throw new CHttpException(404, 'Документ з ІД ' 
              . $id . ' не має доступних файлів для завантаження.');
    }
    //Формування zip-архіва
    
    $pre_name = $this->cutStrByWords((strlen(trim($model->Summary))? 
      $model->Summary
      :$model->DocumentName),200);
    $_name = preg_replace('/[\s?\|\\\\\/\*;:\<\>\"()]/', '_', $pre_name);
    $zipname = $_name . '.zip';
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);
    $i = 0;
    foreach ($model->doc_visible_files as $df) {
      $i++;
      $path = $df->folder;
      $file_entity = $path . $df->FileLocation;
      if (file_exists($file_entity)) {
        $ext = pathinfo($file_entity, PATHINFO_EXTENSION);
        $pre_name = $_name . '_'. $i . '.' . $ext;
        $name_1 = preg_replace('/[\s?\|\\\\\/\*;:\<\>\"()]/', '_', $pre_name);
        $name_2 = str_replace('і',"i",$name_1);//i ukrainian to i latin
        $name_3 = str_replace('І',"I",$name_2);//I ukrainian to I latin
        $name_4 = str_replace('Ї',"YI",$name_3);//the same
        $name = str_replace('ї',"yi",$name_4);//the same
        $zip->addFile($file_entity,  
                ((mb_detect_encoding($name) == 'UTF-8')?
                iconv('UTF-8','ibm866//IGNORE',$name):$name));
      }
    }
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $zipname);
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);
    unlink($zipname);
  }
  
  
  /**
   * Додавання на веб-сторінку елемента x-editable або тексту (залежно від прав користувача) 
   */
  public function echoInfoContainer($model,$attr,$pk,$datatype,$content){
      if (empty($model->_document_user)){
        echo "відсутній власник документа";
        return ;
      }
      if((implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) 
        != implode(',',$model->_document_user->department_ids)
        && (!Yii::app()->user->checkAccess('_DocsAdmin')))){
          echo ((!strlen(trim($content)) && !in_array($attr,array("DoneDate","ControlDate")))? "<i>немає</i>":$content);
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
   * Виведення картки документа
   */
  public function actionCardprint($id){
    $this->layout = '//layouts/clear';
    $model = Documents::model()->findByPk(intval($id));
    if (!$model){
      throw new CHttpException(404, 'Документ з ІД ' 
            . $id . ' не знайдено.');
    }
    $this->render('card',array(
      'model' => $model
    ));
  }
  
  /**
   * Видалення
   */
  public function actionDelete($id=null) {
    $identifier = Yii::app()->request->getParam('id',$id);
    if (!$identifier){
      return false;
    }
    $model = Documents::model()->findByPk($identifier);
    if ($model){
      return $model->delete();
    }
  }
  
  /**
   * Повертає очікуваний індекс (json-об’єкт)
   * @param integer $CategoryID ID категорії із форми створення/редагування документа
   * @param string $SubmissionDate дата надходження 
   *   із форми створення/редагування документа виду dd.mm.yyyy
   * @param integer $UserID ID користувача із форми створення/редагування документа
   * @return integer
   */
  public function actionExpectedIndex($CategoryID,$SubmissionDate,$UserID=0){
    $model = new Documents();
    if ($UserID == 0){
      $UserID = Yii::app()->user->id;
    }
    $model->CategoryID = intval($CategoryID);
    if (preg_match("/^\d\d\.\d\d\.\d\d\d\d$/",$SubmissionDate)){
      $model->SubmissionDate = date("Y-m-d",strtotime($SubmissionDate));
    } else {
      unset($model);
      echo CJSON::encode(array(
        'expected_index' => ""
      ));
      return "";
    }
    $model->UserID = intval($UserID);
    $cat_code = Doccategories::model()->findByPk($CategoryID)->CategoryCode;
    $expected_index = "немає";
    if (strlen(trim($cat_code))>0){
      $expected_index = $model->incrementSubmissionIndex();
    }
    unset($model);
    echo CJSON::encode(array('expected_index' => $expected_index));
    return $expected_index;
  }
  
  /**
   * Повертає повідомлення, якщо документ із заданим 
   *   індексом, категорією і роком надходження вже існує (json-об’єкт)
   * @param integer $DocID ID поточного документа документа
   * @param integer $CategoryID ID категорії із форми створення/редагування документа
   * @param string $SubmissionDate дата надходження 
   * @param string $SubmissionIndex індекс надходження документа
   *   із форми створення/редагування документа виду dd.mm.yyyy
   * @param integer $UserID ID користувача із форми створення/редагування документа
   * @return string
   */
  public function actionSearchIndexAndShowInfo($DocID,$CategoryID,$SubmissionDate,$SubmissionIndex,$UserID=0){
    
    if ($UserID == 0){
      $UserID = Yii::app()->user->id;
    }
    if (!preg_match("/^\d\d\.\d\d\.\d\d\d\d$/",$SubmissionDate)){
      echo CJSON::encode(array(
        'expected_index' => ""
      ));
      return "";
    }
    $criteria = new CDbCriteria();
    if ($DocID > 0){
      $criteria->addNotInCondition('idDocument',array($DocID));
    }
    $criteria->compare("CategoryID",$CategoryID);
    $criteria->compare("SubmissionIndex",$SubmissionIndex);
    $criteria->compare("YEAR(SubmissionDate)", date("Y",strtotime($SubmissionDate)));
    
    $cat_code = Doccategories::model()->findByPk($CategoryID)->CategoryCode;
    $model = Documents::model()->find($criteria);
    $msg = "";
    
    if (strlen(trim($cat_code))>0 && $model && $DocID){
      $msg = "Знайдено документ з такими індексом, роком надходження і категорією: `"
        .$model->Summary
        ."`[".$model->_document_submit[0]->SubmissionInfo."]";
    }
    if (strlen(trim($cat_code))>0 && $model && !$DocID){
      $msg = "Попередній документ з такими роком надходження і категорією: `"
        .$model->Summary
        ."`[".$model->_document_submit[0]->SubmissionInfo."]";
    }
    unset($model);
    echo CJSON::encode(array('msg' => $msg));
    return $msg;
  }
  
  /**
   * TEMPORARY SOLUTION
   * @param mixed $limit
   * @param mixed $offset
   * @param string $password
   */
  public function actionTemp($limit=10,$offset=0, $password=""){
    $conn = mysqli_connect("localhost","root",$password,"docflow");
    if ($conn){
      $conn->query("set names utf8");
      $res = $conn->query("select idDocument, DocumentInputNumber, SubmissionDate from documents "
        . "where char_length(DocumentInputNumber) > 0 order by idDocument DESC "
      ."limit ".intval($offset)
      .", ".intval($limit));
      for ($i = 0; ($i < 100000 && ($r = mysqli_fetch_assoc ($res))); $i++){
        $_id = $r['idDocument'];
        $_num = $r['DocumentInputNumber'];
        $_date = $r['SubmissionDate'];
        //^([0-9]+)[\/|\\].+\s+
        $n = preg_match("/^\D*([0-9]+)[\/|\\\].+\s+(\d{1,2})\.(\d{1,2})\.(\d{4,4})/",$_num,$matches1);
        $m = preg_match("/^\D*([0-9]+)[\/|\\\].+\s+від\s*/",$_num,$matches2);
        if ($n || $m){
          $model = Documents::model()->findByPk($_id);
          $model->SubmissionDate = date("Y-m-d",strtotime($model->Created));
          if ($n){
            $model->SubmissionIndex = $matches1[1];
            $model->SubmissionDate = $matches1[4].'-'.$matches1[3].'-'.$matches1[2];
          }
          if ($m && !$n){
            $model->SubmissionIndex = $matches2[1];
            $model->SubmissionDate = $_date;
          }
          var_dump(array($model->save(),intval($model->idDocument),intval($model->SubmissionIndex),$model->SubmissionDate));
        }
      }
      $conn->close();
    } else {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
  }
  
  

}
