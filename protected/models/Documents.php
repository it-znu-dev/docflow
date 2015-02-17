<?php

/**
 * This is the model class for table "documents".
 *
 * The followings are the available columns in table 'documents':
 * @property integer $idDocument
 * @property integer $UserID
 * @property integer $TypeID
 * @property integer $CategoryID
 * @property integer $Visible
 * @property string $Created
 * @property string $DocumentName
 * @property string $Summary
 * @property integer $SubmissionIndex
 * @property string $SubmissionDate
 * @property string $ExternalIndex
 * @property string $Correspondent
 * @property string $Signed
 * @property string $Resolution
 * @property string $ControlMark
 * @property string $ControlDate
 * @property string $DoneMark
 *
  * The followings are the available model relations:
 * @property DocCategories $_document_doccategory
 * @property DocTypes $_document_doctype
 * @property Users $_document_user
 * @property DocumentFile[] $_document_document_file
 * @property Files[] $_document_files
 * @property Files[] $doc_visible_files
 * @property DocumentFlow[] $_document_document_flow
 * @property Flows[] $_document_flows
 * @property FlowRespondent[] $_document_flow_respondent
 * @property DocumentSubmit[] $_document_submit
 *
 * @property string $UserInfo фільтр по користувачам (логін+ПІБ та посада)
 * @property string $DocumentInfo фільтр по кор.змісту та індексам
 * @property integer $SubmissionYear фільтр по року надходження
 * @property integer $WithControl фільтр: є відмтка контролю, але немає відмітки виконання
 *
 * @property CUploadedFile $uploaded_file прикріплений файл для збереження
 * @property integer[] $file_ids ідентифікатори файлів
 * @property integer[] $flow_ids ідентифікатори розсилок
 *
 */
class Documents extends CActiveRecord{
  public $UserInfo;
  public $DocumentInfo;
  public $SubmissionYear;
  public $WithControl;
  
  public $doc_visible_files;
  public $uploaded_file;
  public $file;
  public $file_ids;
  public $flow_ids;

  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'documents';
  }
  
  protected function beforeValidate() {
    parent::beforeValidate();
    if (strlen($this->SubmissionDate)){
      if (!strtotime($this->SubmissionDate)){
        $this->addError('SubmissionDate','Невірний формат дати надходження');
        return false;
      }
      $this->SubmissionDate = date("Y-m-d",strtotime($this->SubmissionDate));
    } else {
      $this->SubmissionDate = date("Y-m-d");
    }
    if ($this->uploaded_file){
      $fmodel = new Files();
      $fmodel->Visible = 1;
      $fmodel->UserID = Yii::app()->user->id;
      $fmodel->file_itself = $this->uploaded_file;
      if (!$fmodel->validate()){
        $errs = $fmodel->getErrors();
        $err = array();
        foreach ($errs as $e){
          $err[] = implode('; ',$e);
        }
        $this->addError('file',implode('| ',$err));
        return false;
      }
    }
    return true;
  }
  
  protected function beforeSave() {
    parent::beforeSave();
    $this->incrementSubmissionIndex();
    return true;
  }
  
  protected function afterSave() {
    parent::afterSave();
    if (isset($this->uploaded_file)){
      $fmodel = new Files();
      $fmodel->Visible = 1;
      $fmodel->UserID = Yii::app()->user->id;
      $fmodel->file_itself = $this->uploaded_file;
      if(!$fmodel->save()){
        $errs = $fmodel->getErrors();
        $err = array();
        foreach ($errs as $e){
          $err[] = implode('; ',$e);
        }
        $this->addError('file',implode('| ',$err));
          return false;
      }
      
      $dfmodel = new DocumentFile();
      $dfmodel->DocumentID = $this->idDocument;
      $dfmodel->FileID = $fmodel->idFile;
      $dfmodel->save();
    }
    if ($this->SubmissionIndex && strtotime($this->SubmissionDate)){
      $ds = DocumentSubmit::model()->find("DocumentID=".$this->idDocument);
      if (!$ds){
        $ds = new DocumentSubmit();
      }
      $ds->DocumentID = $this->idDocument;
      $ds->SubmissionInfo = '№ ' . str_pad($this->SubmissionIndex, 2, "0", STR_PAD_LEFT) 
        . '/' . $this->_document_doccategory->CategoryCode
        . ' від ' . date('d.m.Y',strtotime($this->SubmissionDate));
      $ds->save();
    }
      
    return true;
  }
  
  
  protected function afterFind() {
    parent::afterFind();
    if ($this->ControlDate){
      $this->ControlDate = date('d.m.Y',strtotime($this->ControlDate));
    }
    $this->file_ids = array();
    $criteria = new CDbCriteria();
    $criteria->with = array('_file_document_file');
    $criteria->together = true;
    $criteria->compare('_file_document_file.DocumentID',$this->idDocument);
    $criteria->addCondition('Visible is not null');
    $criteria->addCondition('Visible > 0');
    $criteria->order = 'Created DESC';
    $this->doc_visible_files = Files::model()->findAll($criteria);
    
    foreach ($this->doc_visible_files as $model){
      if($model->exists){
        $this->file_ids[] = $model->idFile;
      }
    }
    
    if (!$this->ExternalIndex){
      $this->ExternalIndex = "Без №, без d";
    }
    $this->DocumentInfo = $this->DocumentName.
        ((strlen(trim($this->Summary)))? "(".$this->Summary.")":"");
    if (strtotime($this->SubmissionDate)){
      $this->SubmissionDate = date("d.m.Y",strtotime($this->SubmissionDate));
    }
    return true;
  }
  
  protected function beforeDelete(){
    parent::beforeDelete();
    foreach ($this->_document_files as $model){
      $model->delete();
    }
    if (!empty($this->_document_submit)){
      $ds = $this->_document_submit[0];
      $ds->delete();
    }
    
    foreach ($this->_document_document_flow as $model){
      $model->delete();
    }
    return true;
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('UserID, TypeID, CategoryID, Created, DocumentName, SubmissionDate', 'required'),
      array('UserID, TypeID, CategoryID, Visible, SubmissionIndex', 'numerical', 'integerOnly'=>true),
      array('DocumentName, ExternalIndex, Correspondent, Signed, Resolution, ControlMark, DoneMark', 'length', 'max'=>255),
      array('Summary, SubmissionDate, ControlDate, ', 'safe'),
      array('ControlDate', 'is_date'),
      array('uploaded_file', 'file', 'types' => '
          pdf, rtf, odt, ods, txt, csv,  
          jpg, gif, png, tiff, tif, bmp, jpeg, 
          doc, docx, xls, xlsx, ppt, pptx, 
          html, htm, js, css, 
          zip, rar, 7z, tar, gz',
        'wrongType' => "Такий тип файлу не підтримується",
        'maxSize' => ini_get('upload_max_filesize') * 1024 * 1024,
        'tooLarge' => "Занадто великий файл",
        'allowEmpty' => true
      ),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('idDocument, UserID, TypeID, CategoryID, Visible, Created, DocumentName, Summary, '
        .'SubmissionIndex, SubmissionDate, ExternalIndex, Correspondent, Signed, Resolution, '
        .'ControlMark, DoneMark, ControlDate', 'safe', 'on'=>'search'),
    );
  }
  
  /**
   * Check if attribute is valide date dd.mm.YYYY
   */
  public function is_date($attribute,$params){
    $format = 'd.m.Y';
    if (!strlen(trim($this->$attribute))){
      $this->$attribute = null;
      return ;
    }
    $d = DateTime::createFromFormat($format, $this->$attribute);
    if (!($d && $d->format($format) == $this->$attribute)){
      $this->addError($attribute,'Невірний формат дати');
    } else {
      $this->$attribute = $d->format('Y-m-d');
    }
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_document_user' => array(self::BELONGS_TO, 'Users', 'UserID'),
      '_document_doccategory' => array(self::BELONGS_TO, 'Doccategories', 'CategoryID'),
      '_document_doctype' => array(self::BELONGS_TO, 'Doctypes', 'TypeID'),
      '_document_document_file' => array(self::HAS_MANY, 'DocumentFile', 'DocumentID'),
      '_document_files' => array(self::HAS_MANY, 'Files', 'FileID', 
        'through' => '_document_document_file', 'order' => '_document_files.Created DESC'),
      '_document_submit' => array(self::HAS_MANY, 'DocumentSubmit', 'DocumentID'),
      '_document_document_flow' => array(self::HAS_MANY, 'DocumentFlow', 'DocumentID'),
      '_document_flows' => array(self::HAS_MANY, 'Flows', 'FlowID', 
        'through' => '_document_document_flow', 'order' => '_document_flows.Created DESC'),
      '_document_flow_respondent' => array(self::HAS_MANY, 'FlowRespondent', 'FlowID', 
        'through' => '_document_document_flow', 'order' => 'DeptID ASC'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'idDocument' => 'ID',
      'UserID' => 'Ідентифікатор користувача',
      'TypeID' => 'Тип документа',
      'CategoryID' => 'Категорія документа',
      'Visible' => 'Доступний',
      'Created' => 'створено',
      'DocumentName' => 'Назва документа',
      'Summary' => 'Короткий зміст',
      'SubmissionIndex' => 'індекс документа в межах категорії і року надходження',
      'SubmissionDate' => 'Дата надходження',
      'ExternalIndex' => 'Дата та індекс документа',
      'Correspondent' => 'Кореспондент',
      'Signed' => 'Підписано',
      'Resolution' => 'Резолюція або кому надіслано документ',
      'UserInfo' => 'Користувач',
      'SubmissionYear' => 'Рік надходження',
      'ControlMark' => 'Контроль',
      'DoneMark' => 'Відмітка виконання документа',
      'ControlDate' => 'Дата контролю',
      'file' => 'Файли',
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
    
    $defOrder = 't.Created DESC';
    
    $criteria->compare('idDocument',$this->idDocument);
    $criteria->with = array();
    $criteria->with[] = '_document_flow_respondent';
    if (strlen(trim($this->UserInfo))){
      $criteria->with[] = '_document_user';
      $criteria->compare('CONCAT(_document_user.username,"#**#",_document_user.info)',$this->UserInfo,true);
    }
    if ($this->WithControl){
      $criteria->addCondition("
      (trim(replace(if(isnull(t.ControlDate),'',t.ControlDate),\"\\n\",' ')) not like '' 
      or trim(replace(if(isnull(t.ControlMark),'',t.ControlMark),\"\\n\",' ')) not like ''
      )
      and trim(replace(if(isnull(t.DoneMark),'',t.DoneMark),\"\\n\",' ')) like '' 
      ");
    }
    if ($this->DocumentInfo){
      $criteria->with[] = '_document_submit';
      $criteria->compare('CONCAT_WS("\t",t.Summary,_document_submit.SubmissionInfo,t.ExternalIndex)',$this->DocumentInfo,true);
    }
    if (intval($this->SubmissionYear) >= 1970){
      $criteria->with[] = '_document_submit';
      $criteria->addCondition('(YEAR(SubmissionDate) = '.intval($this->SubmissionYear).')'
        .' OR '
        .'(_document_submit.SubmissionInfo like "%'.intval($this->SubmissionYear).'%")'
        .' OR '
        .'(YEAR(Created) = '.intval($this->SubmissionYear).')'
      );
    }
    $criteria->together = true;
    $criteria->group = 't.idDocument';
    if ($this->UserID){
      $criteria->addCondition('(
        t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.intval($this->UserID).')
        )
      ) OR (
        _document_flow_respondent.DeptID IN 
         (select ud.DepartmentID from user_department ud where ud.UserID='.intval($this->UserID).')
      )');
    }
    $criteria->compare('TypeID',$this->TypeID);
    $criteria->compare('CategoryID',$this->CategoryID);
    $criteria->compare('t.Visible',$this->Visible);
    $criteria->compare('t.Created',$this->Created,true);
    $criteria->compare('DocumentName',$this->DocumentName,true);
    $criteria->compare('Summary',$this->Summary,true);
    $criteria->compare('SubmissionIndex',$this->SubmissionIndex);
    $criteria->compare('SubmissionDate',$this->SubmissionDate,true);
    $criteria->compare('ExternalIndex',$this->ExternalIndex,true);
    $criteria->compare('Correspondent',$this->Correspondent,true);
    $criteria->compare('Signed',$this->Signed,true);
    $criteria->compare('Resolution',$this->Resolution,true);
    $criteria->compare('ControlDate',$this->ControlDate,true);
    $criteria->compare('ControlMark',$this->ControlMark,true);
    $criteria->compare('DoneMark',$this->DoneMark,true);
    
    if ($this->CategoryID){
      $defOrder = 't.SubmissionIndex DESC, t.Created DESC';
    }

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
        'pagination' => array(
            'pageSize' => 10,
        ),
        'sort'=>array(
            'defaultOrder'=>$defOrder,
        ),
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Documents the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
  
  /**
   * Повертає індекс надходження нового документа за правилом:
   * індекс попереднього документа із такою ж категорією і роком надходження +1.
   * Якщо категорія не має коду, завжди повертається 1.
   * @return integer індекс надходження нового документа
   */
  public function incrementSubmissionIndex(){
    $submission_number = 0;
    if ($this->SubmissionIndex){
      //індекс створюється лише тоді, коли його ще не задано
      return 0;
    }
    $criteria = new CDbCriteria();
    $criteria->with = array('_document_doccategory','_document_submit');
    $criteria->together = true;
    $criteria->group = "t.idDocument";
    
    $criteria->addCondition('t.UserID IN 
      (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
        (select DepartmentID from user_department ud where ud.UserID='.$this->UserID.')
      )');
    $criteria->compare('CategoryID',$this->CategoryID);
    $criteria->addCondition('(YEAR(SubmissionDate)="'.date('Y',strtotime($this->SubmissionDate)).'") OR ' 
      . '(_document_submit.SubmissionInfo LIKE "%.'.date('Y',strtotime($this->SubmissionDate)).'%")');
    $criteria->addCondition('(Visible is not null) and (Visible > 0)');
    $criteria->order = "SubmissionIndex DESC, Created DESC";
    $criteria->limit = 100;
    $models = Documents::model()->findAll($criteria);
    $submission_number = 1;
    $cat_code = Doccategories::model()->findByPk($this->CategoryID)->CategoryCode;
    if ($cat_code){
    // здесь была проблема, дебажил как мог
//      foreach ($models as $m){
//        $ds = $m->_document_submit[0];
//        $n = preg_match('/([0-9]+)[\/|\\\].+/', $ds->SubmissionInfo, $matches);
//        $submission_number = intval($matches[1]) + 1;
//        var_dump(array(
//          'idDocument' => $m->idDocument,
//          'SubmissionIndex' => $m->SubmissionIndex,
//          'SubmissionInfo' => $ds->SubmissionInfo,
//          'Created' => $m->Created,
//          'matches' => $matches,
//          'n' => $n,
//          'next_n' => $submission_number
//        ));
//      }
//      exit();
      foreach ($models as $model){
        if (!$model->SubmissionIndex){
          //якщо немає індексу, то треба його виділити із даних виду 
          // № ##/###-### від ##.##.####
          // # - цифра [0-9]
          $ds = $model->_document_submit[0];
          if (!$ds){
            continue;
          }
          $n = preg_match('/([0-9]+)[\/|\\\].+/', $ds->SubmissionInfo, $matches);
          if ($n) {
            $submission_number = intval($matches[1]) + 1;
            break;
          }
        } else {
          // якщо є індекс, то просто +1
          $submission_number = intval($model->SubmissionIndex) + 1;
          break;
        }
      }
      $this->SubmissionIndex = $submission_number;
    }
    return $submission_number;
  }
  
  /**
   * Повертає усі роки створення документів у вигляді асоц. масиву
   * @return array e.g. [''=>'рік','2014'=>'2014', '2015'=>'2015']
   */
  public function getYears(){
    $data= Yii::app()->db->createCommand('select YEAR(if(Created is not null,Created,CURDATE())) as _year 
    from documents 
    group by YEAR(if(Created is not null,Created,CURDATE())) 
    order by YEAR(if(Created is not null,Created,CURDATE())) DESC')->queryAll();
    $years = array();
    $years[''] = 'рік';
    for ($i = 0; $i < count($data); $i++){
      $years[$data[$i]['_year']] = $data[$i]['_year'];
    }
    return $years;
  }
}
