<?php

/**
 * This is the model class for table "flows".
 *
 * The followings are the available columns in table 'flows':
 * @property integer $idFlow
 * @property integer $UserID
 * @property integer $PeriodID
 * @property string $Created
 * @property string $FlowName
 * @property string $FlowDescription
 *
 * The followings are the available model relations:
 * @property Users $_flow_user
 * @property DocumentFlow[] $_flow_document_flow
 * @property EventFlow[] $_flow_event_flow
 * @property Events[] $_flow_events
 * @property Documents[] $_flow_documents
 * @property Documents[] $__flow_documents
 * @property FlowRespondent[] $_flow_flow_respondent
 * @property Departments[] $_flow_departments
 * @property Answers[] $_flow_answers
 *
 * @property string $mode режим показу (вхідні, ініційовані, усі)
 * @property string $DocumentInfo пошук по короткому опису та індексам документів
 * @property string $UserInfo користувач
 * @property string $Respondent респондент
 *
 * @property integer[] $dept_ids ідентифікатори залежних підрозділів (респондентів)
 * @property integer[] $document_ids ідентифікатори залежних документів
 * @property integer[] $event_ids ідентифікатори залежних заходів
 */
class Flows extends CActiveRecord
{
  public $DocumentInfo;
  public $Respondent;
  public $UserInfo;
  public $mode;
  public $doc_cnt;
    
  public $__flow_documents;
  public $dept_ids;
  public $document_ids;
  public $event_ids;
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'flows';
  }
  
  protected function beforeDelete(){
    parent::beforeDelete();
    
    foreach ($this->_flow_document_flow as $model){
      $model->delete();
    }
    foreach ($this->_flow_flow_respondent as $model){
      $model->delete();
    }
    foreach ($this->_flow_event_flow as $model){
      $model->delete();
    }
    return true;
  }
  
  protected function afterSave() {
    parent::afterSave();
    if (is_array($this->document_ids)){
      foreach ($this->_flow_document_flow as $model){
        $model->delete();
      }
      foreach ($this->document_ids as $_id){
        if ($_id){
          $m = new DocumentFlow();
          $m->FlowID = $this->idFlow;
          $m->DocumentID = $_id;
          $m->save();
        }
      }
    }
    if (is_array($this->dept_ids)){
      if (!$this->isNewRecord){
        foreach ($this->_flow_flow_respondent as $fr){
          if ($fr->_flow_respondent_answer){
            $fr->_flow_respondent_answer->delete();
          }
        }
        $q = FlowRespondent::model()->deleteAll(
          "FlowID=".intval($this->idFlow));
      }
      foreach ($this->dept_ids as $_id){
        if ($_id){
          $m = new FlowRespondent();
          $m->FlowID = $this->idFlow;
          $m->DeptID = $_id;
          if(!$m->save()){
            var_dump($m->getErrors());exit();
          }
        }
      }
    }
    if (is_array($this->event_ids)){
      foreach ($this->_flow_event_flow as $model){
        $model->delete();
      }
      foreach ($this->event_ids as $_id){
        $m = new EventFlow();
        $m->FlowID = $this->idFlow;
        $m->EventID = $_id;
        $m->save();
      }
    }
    $this->sendViaGmail($this->idFlow);
    return true;
  }

  protected function afterFind() {
    parent::afterFind();
    $this->document_ids = array();
    $this->dept_ids = array();
    foreach ($this->_flow_document_flow as $model){
      $this->document_ids[] = $model->DocumentID;
    }
    foreach ($this->_flow_flow_respondent as $model){
      $this->dept_ids[] = $model->DeptID;
    }
    foreach ($this->_flow_event_flow as $model){
      $this->event_ids[] = $model->EventID;
    }
    $this->__flow_documents = $this->_flow_documents;
    return true;
  }
  
  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('UserID, PeriodID, Created, FlowName', 'required'),
      array('UserID, PeriodID', 'numerical', 'integerOnly'=>true),
      array('FlowName', 'length', 'max'=>255),
      array('FlowDescription', 'safe'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('idFlow, UserID, PeriodID, Created, FlowName, FlowDescription', 'safe', 'on'=>'search'),
    );
  }
  

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_flow_user' => array(self::BELONGS_TO, 'Users', 'UserID'),
      '_flow_document_flow' => array(self::HAS_MANY, 'DocumentFlow', 'FlowID'),
      '_flow_documents' => array(self::HAS_MANY, 'Documents', 'DocumentID', 
        'through' => '_flow_document_flow', 'order' => '_flow_documents.Created DESC'),
      '_flow_event_flow' => array(self::HAS_MANY, 'EventFlow', 'FlowID'),
      '_flow_events' => array(self::HAS_MANY, 'Events', 'EventID', 
        'through' => '_flow_event_flow', 'order' => '_flow_events.Created ASC'),
      '_flow_flow_respondent' => array(self::HAS_MANY, 'FlowRespondent', 'FlowID'),
      '_flow_departments' => array(self::HAS_MANY, 'Departments', 'DeptID', 
        'through' => '_flow_flow_respondent', 'order' => '_flow_departments.DepartmentName ASC'),
      '_flow_answers' => array(self::HAS_MANY, 'Answers', 'AnswerID', 
        'through' => '_flow_flow_respondent', 'order' => '_flow_answers.Created DESC'),
    );
  }
  
  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'idFlow' => 'автоінкрементний ідентифікатор',
      'UserID' => 'ідентифікатор користувача, власника розсилки',
      'PeriodID' => 'періодичність',
      'Created' => 'дата і час створення',
      'FlowName' => 'назва розсилки',
      'FlowDescription' => 'деталі розсилки',
      'DocumentInfo' => 'опис або індекси документа',
      'UserInfo' => 'користувач',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   *
   * Typical usecase:
   * - Initialize the model fields with values from filter form.
   * - Execute this method to get CActiveDataProvider instance which will filter
   * models according to data in model fields.
   * - Pass data provider to CGridView, CListView or any similar widget.
   *
   * @return CActiveDataProvider the data provider that can return the models
   * based on the search/filter conditions.
   */
  public function search(){
    // @todo Please modify the following code to remove attributes that should not be searched.

    $criteria=new CDbCriteria;
    
    $criteria->with = array();
    $criteria->with[] = '_flow_user';
    
    if ($this->UserID && $this->mode == 'from' && !$this->idFlow){
      $criteria->select = array(
        '*',
        new CDbExpression("'from' as mode")
      );
      $criteria->addCondition('t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.$this->UserID.')
        )');
    }
    if ($this->UserID && $this->mode != 'from' && !$this->idFlow){
      $criteria->with += array('_flow_flow_respondent' => array('select' => false));
      $criteria->with += array('_flow_flow_respondent._flow_respondent_answer' => array('select' => false));
      
      $criteria->addCondition('_flow_flow_respondent.DeptID in '
        . '(select DepartmentID from user_department '
        . ' where UserID='.$this->UserID.')');
      if ($this->mode == 'without_answer'){
        $criteria->select = array(
          '*',
          new CDbExpression("'without_answer' as mode")
        );
        $criteria->addCondition('_flow_respondent_answer.idAnswer IS NULL'); 
      }
    }

    if ($this->idFlow > 0 && $this->UserID){
      $criteria->with += array('_flow_flow_respondent' => 
        array('select' => false));
      $criteria->with += array('_flow_flow_respondent._flow_respondent_answer' => 
        array('select' => false));

      $criteria->select = array(
        '*',
        new CDbExpression("if(
        _flow_flow_respondent.DeptID in "
        . "(select DepartmentID from user_department "
        . " where UserID=".$this->UserID."),"
          . "if(_flow_respondent_answer.idAnswer IS NULL,'in','without_answer'),"
          . "'from')"
        . " as mode")
      );
      $criteria->addCondition('(t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.$this->UserID.')
        )) OR (
        _flow_flow_respondent.DeptID in '
        . '(select DepartmentID from user_department '
        . ' where UserID='.$this->UserID.'))');
    }
    if (strlen(trim($this->DocumentInfo)) > 0){
      if (preg_match("/^\\>([0-9]+)/",$this->DocumentInfo,$matches)){
        //$criteria->select[] = new CDbExpression("COUNT(_flow_documents.idDocument) as doc_cnt");
        $criteria->addCondition(" (select COUNT(_docf.DocumentID) > "
          .intval($matches[1])
          ." from document_flow _docf where _docf.FlowID=t.idFlow group by _docf.FlowID) ");
      } else {
        $criteria->with += array('_flow_documents' => array('select' => false));
        $criteria->with += array('_flow_documents._document_submit' 
          => array('select' => false));
        $criteria->compare('CONCAT_WS("\t",'
          . '_flow_documents.Summary,'
          . '_document_submit.SubmissionInfo,'
          . '_flow_documents.ExternalIndex)',$this->DocumentInfo,true);
      }
    }
    if (strlen(trim($this->UserInfo))){
      $criteria->compare('CONCAT(_flow_user.username,"#**#",_flow_user.info)',$this->UserInfo,true);
    }
    if (strlen(trim($this->Respondent)) > 0){
      $criteria->with[] = '_flow_departments';
      
      $criteria->compare('_flow_departments.DepartmentName',$this->Respondent,true);
    }
    
    $criteria->together = true;
    $criteria->group = 't.idFlow';
    
    $criteria->compare('idFlow',$this->idFlow);
    $criteria->compare('PeriodID',$this->PeriodID);
    $criteria->compare('t.Created',$this->Created,true);
    $criteria->compare('FlowName',$this->FlowName,true);
    $criteria->compare('FlowDescription',$this->FlowDescription,true);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
        'pagination' => array(
            'pageSize' => 10,
        ),
        'sort'=>array(
            'defaultOrder'=>'t.Created DESC',
        ),
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Flows the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
  
  
  /**
   * Розсилка повідомлення через gmail
   * @param integer $idFlow
   */
  public function sendViaGmail($idFlow){
    $model = Flows::model()->findByPk($idFlow);
    if (!$model){
      return false;
    }
    $fh = fopen(Yii::app()->basePath . '/gmail_password.txt',"r");
    $gpassword = "";
    if ($fh){
      $gpassword = fgets($fh);
      fclose($fh);
    } else {
      return false;
    }
    if (!$gpassword){
      return false;
    }
    require_once "Mail.php";
    $to = "";
    $a_to = array();
    $from = 'it.znu.edu@gmail.com';
    $subject = "СЕД ЗНУ : ".$model->FlowName;

    $a_depts = array();
    foreach ($model->_flow_user->_user_departments as $_dept){
      /* @var $_dept Departments */
      $a_depts[] = $_dept->DepartmentName;
    }

    $body = ((strlen(trim($model->FlowDescription)))? $model->FlowDescription: "")
      . "\n"
      . "Розсилку ініційовано користувачем: \n"
      . $model->_flow_user->info . ' (' . implode("; ",$a_depts) . ")\n"
      . "Деталі за посиланням: " . Yii::app()->createAbsoluteUrl("flows/index",array("Flows[idFlow]" => $model->idFlow));
    foreach ($model->_flow_departments as $dept){
      /* @var $dept Departments */
      foreach ($dept->_department_users as $user){
        /* @var $user Users */
        $k = preg_match("/\\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}\\b/i",$user->contacts,$matches);
        if ($k){
          $a_to[] = $matches[0];
        }
      }
    }
    
    if (count($a_to) == 0){
      return false;
    }
    $to = implode(", ",$a_to);
    $headers = array(
        'From' => $from,
        'To' => $to,
        'Subject' => $subject
    );
    $smtp = Mail::factory('smtp', array(
            'host' => 'ssl://smtp.gmail.com',
            'port' => '465',
            'auth' => true,
            'username' => 'it.znu.edu@gmail.com',
            'password' => $gpassword
        ));
    $mail = $smtp->send($to, $headers, $body);
    
    $dir = Yii::app()->basePath . '/logs/';
    if (is_file($dir . "mail_logger.log")){
      $fp = fopen($dir . "mail_logger.log","a");
    } else {
      $fp = fopen($dir . "mail_logger.log","w");
    }
    $log_msg = "";
    if (PEAR::isError($mail)) {
        $log_msg = date("Y-m-d H:i:s") . " ER " . $mail->getMessage() . "\n";
    } else {
        $log_msg = date("Y-m-d H:i:s") . " OK =>[" . $to . "]\n";
    }
    if ($fp){
      fwrite($fp,$log_msg);
      fclose($fp);
    }

    return true;

  }
  
}
