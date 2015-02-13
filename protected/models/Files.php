<?php

/**
 * This is the model class for table "files".
 *
 * The followings are the available columns in table 'files':
 * @property integer $idFile
 * @property integer $UserID
 * @property integer $Visible
 * @property string $Created
 * @property string $FileName
 * @property string $FileLocation
 *
 * The followings are the available model relations:
 * @property Users $_file_user
 * @property DocumentFile[] $_file_document_file
 * @property Events[] $_file_events
 *
 * @property string $UserInfo
 * @property string $DateStart
 * @property string $DateEnd
 * @property string $Document
 *
 * @property integer[] $document_ids
 *
 * @property CUploadedFile $file_itself
 * @property string $folder
 * @property boolean $exists
 *
 */
class Files extends CActiveRecord {
  public $UserInfo;
  public $DateStart;
  public $DateEnd;
  public $Document;
  
  public $document_ids;
  public $event_ids;
  
  public $file_itself;
  public $folder;
  public $exists;
  
  public function init(){
    $this->folder = Yii::app()->params['docPath'];
    return parent::init();
  }
  
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'files';
  }

  protected function afterFind() {
    parent::afterFind();
    $this->document_ids = array();
    //$this->event_ids = array();
    foreach ($this->_file_document_file as $model){
      $this->document_ids[] = $model->DocumentID;
    }
//     foreach ($this->_file_event_file as $model){
//       $this->event_ids[] = $model->EventID;
//     }
    $this->exists = is_file($this->folder . $this->FileLocation);
    return true;
  }
  
  protected function beforeValidate() {
    parent::beforeValidate();
    $user = Users::model()->findByPk($this->UserID);
    if (is_object($this->file_itself) && (get_class($this->file_itself) == "CUploadedFile") && $user){
      if (!strlen($this->file_itself->getTempName())){
        $this->addError('file_itself',"Не вдалося зберегти файл.");
        return false;        
      }
      $username = $user->username;
      $md5_name = md5_file($this->file_itself->getTempName());
      $ext = $this->file_itself->extensionName;
      if (!is_dir($this->folder.$username)){
        mkdir($this->folder.$username);
      }
      $this->FileLocation = $username . '/' . $md5_name . '.' . $ext;
      $this->FileName = $this->file_itself->name;
      if (!$this->Created){
        $this->Created = date("Y-m-d H:i:s");
      }
    }
    return true;
  }
  
  protected function beforeSave() {
    parent::beforeSave();
    if (is_object($this->file_itself) && (get_class($this->file_itself) == "CUploadedFile")){
      $new_filename = $this->folder . $this->FileLocation;
      if (!$this->file_itself->saveAs($new_filename)){
        $this->addError('file_itself',"Не вдалося зберегти файл");
        return false;
      }
    } else if(!is_null($this->file_itself)){
      $this->addError('file_itself',"Не вдалося зберегти файл: внутрішня помилка.");
      return false;      
    }
    return true;
  }
  
  protected function afterSave() {
    parent::afterSave();
    if (is_array($this->document_ids)){
      foreach ($this->_file_document_file as $model){
        $model->delete();
      }
      foreach ($this->document_ids as $_id){
        if ($_id){
          $m = new DocumentFile();
          $m->FileID = $this->idFile;
          $m->DocumentID = $_id;
          $m->save();
        }
      }
    }
    if (is_array($this->event_ids)){
      foreach ($this->_file_event_file as $model){
        $model->delete();
      }
      foreach ($this->event_ids as $_id){
        $m = new EventFile();
        $m->FileID = $this->idFile;
        $m->EventID = $_id;
        $m->save();
      }
    }
    if (!is_file($this->folder . $this->FileLocation)){
      $this->addError('file_itself',"Не вдалося зберегти файл '.$this->folder . $this->FileLocation.': внутрішня помилка.");
      return false;   
    }
    return true;
  }

  protected function beforeDelete() {
    parent::beforeDelete();
    $fullpath = $this->folder . $this->FileLocation;
    if (is_file($fullpath)){
      unlink($fullpath);
    }
    foreach ($this->_file_document_file as $model){
      $model->delete();
    }
    foreach ($this->_file_events as $model){
      $model->FileID = NULL;
      $model->save();
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
      array('UserID', 'required'),
      array('UserID, Visible', 'numerical', 'integerOnly'=>true),
      array('FileName, FileLocation', 'length', 'max'=>255),
      array('FileLocation','unique', 'message' => 'Такий файл вже існує'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('idFile, UserID, Visible, Created, FileName, FileLocation', 'safe', 'on'=>'search'),
      array('file_itself', 'file', 'types' => '
          pdf, rtf, odt, ods, txt, csv,  
          jpg, gif, png, tiff, tif, bmp, jpeg, 
          doc, docx, xls, xlsx, ppt, pptx, 
          html, htm, js, css, 
          zip, rar, 7z, tar, gz',
        'wrongType' => "Такий тип файлу не піддтримується",
        'maxSize' => intval(ini_get('upload_max_filesize')) * 1024 * 1024,
        'tooLarge' => "Занадто великий файл",
        'allowEmpty' => true
      ),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        '_file_user' => array(self::BELONGS_TO, 'Users', 'UserID'),
        '_file_document_file' => array(self::HAS_MANY, 'DocumentFile', 'FileID'),
        '_file_documents' => array(self::HAS_MANY, 'Documents', 'DocumentID', 'through' => '_file_document_file'),
        '_file_events' => array(self::HAS_MANY, 'Events', 'FileID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'idFile' => 'ID',
      'UserID' => 'ID власника',
      'Visible' => 'дійсний',
      'Created' => 'дата і час',
      'FileName' => 'назва файлу',
      'FileLocation' => 'назва і шлях до файлу',
      'UserInfo' => 'користувач',
      'file_itself' => 'Файл',
      'Document' => 'документи',
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
    $criteria->with = array('_file_user');
    if (strlen(trim($this->Document))){
      $criteria->with[1] = '_file_documents';
    }
    $criteria->together = true;
    $criteria->group = 't.idFile';
    $criteria->group = "idFile";
    
    $criteria->compare('idFile',$this->idFile);
    //$criteria->compare('UserID',$this->UserID);
    if ($this->UserID){
      $criteria->addCondition('t.UserID IN 
        (select _ud.UserID from user_department _ud where _ud.DepartmentID IN 
          (select DepartmentID from user_department ud where ud.UserID='.$this->UserID.')
        )');
    }
    if ($this->DateStart || $this->DateEnd){
      $criteria->addBetweenCondition('t.Created',
        date("Y-m-d",strtotime((($this->DateStart)? $this->DateStart : '1970-01-01'))) . " 00:00:00",
        date("Y-m-d",strtotime((($this->DateEnd)? $this->DateEnd : date('Y-m-d')))) . " 23:59:59"
      );
    }
    $criteria->compare('CONCAT_WS(" *;* ",_file_user.username,_file_user.info,_file_user.contacts)',$this->UserInfo,true);
    if (strlen(trim($this->Document))){
      if (preg_match('/^\=\s*([0-9]+)\s*$/',$this->Document,$matches)){
        $criteria->having ="count(_file_document_file.DocumentID) = ".$matches[1];
      } else {
        $criteria->compare('CONCAT_WS(" *;* ",_file_documents.DocumentName,_file_documents.Summary)',$this->Document,true);
      }
    }
    $criteria->compare('Visible',$this->Visible);
    $criteria->compare('FileName',$this->FileName,true);
    $criteria->compare('FileLocation',$this->FileLocation,true);
    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
        'sort'=>array(
            'defaultOrder'=>'t.Created DESC',
        ),
        'pagination' => array(
            'pageSize' => 10,
        )
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Files the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
}
