<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $md5_password
 * @property string $contacts
 * @property string $info
 * 
 * The followings are the available model relations:
 * @property Roleassignments[] $_user_roleassignments
 * @property UserDepartment[] $_user_user_department
 * @property Departments[] $_user_departments
 * @property Documents[] $_user_documents
 *
 * @property Flows[] $_user_flows
 * @property Files[] $_user_files
 * @property Answers[] $_user_answers
 * @property Events[] $_user_events
 *
 * @property integer[] $department_ids
 * @property string[] $role_ids
 *
 * @property string $Department
 * @property string $Role
 */
class Users extends CActiveRecord {
  public $department_ids;
  public $role_ids;
  public $Department;
  public $Role;

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Users the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'users';
  }

  protected function afterFind() {
    parent::afterFind();
    $this->department_ids = array();
    $this->role_ids = array();
    foreach ($this->_user_roleassignments as $model){
      $this->role_ids[] = $model->itemname;
    }
    foreach ($this->_user_user_department as $model){
      $this->department_ids[] = $model->DepartmentID;
    }
    return true;
  }
  
  protected function beforeSave() {
    parent::beforeSave();
    $this->md5_password = md5($this->password);
    return true;
  }
  
  protected function afterSave() {
    parent::afterSave();
    if (is_array($this->department_ids)){
      foreach ($this->_user_user_department as $model){
        $model->delete();
      }
      foreach ($this->department_ids as $dept_id){
        $ud = new UserDepartment();
        $ud->UserID = $this->id;
        $ud->DepartmentID = $dept_id;
        $ud->save();
      }
    }
    if (is_array($this->role_ids)){
      foreach ($this->_user_roleassignments as $model){
        $model->delete();
      }
      foreach ($this->role_ids as $role){
        $ra = new Roleassignments();
        $ra->userid = $this->id;
        $ra->itemname = $role;
        $ra->save();
      }
    }
    return true;
  }

  protected function beforeDelete() {
    parent::beforeDelete();
    foreach ($this->_user_roleassignments as $model){
      $model->delete();
    }
    foreach ($this->_user_user_department as $model){
      $model->delete();
    }
    foreach ($this->_user_documents as $model){
      $model->delete();
    }
    foreach ($this->_user_flows as $model){
      $model->delete();
    }
    foreach ($this->_user_files as $model){
      $model->delete();
    }
    foreach ($this->_user_answers as $model){
      $model->delete();
    }
    foreach ($this->_user_events as $model){
      $model->delete();
    }
    return true;
  }
  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('username, password, department_ids, role_ids', 'required'),
        array('username, password', 'length', 'max' => 255),
        array('md5_password', 'length', 'max' => 64),
        array('contacts, info', 'safe'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, username, password, md5_password, contacts, info', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        '_user_roleassignments' => array(self::HAS_MANY, 'Roleassignments', 'userid', 'order' => 'itemname ASC'),
        '_user_roles' => array(self::HAS_MANY, 'Roles', 'itemname', 'through' => '_user_roleassignments', 'order' => 'name ASC'),
        '_user_user_department' => array(self::HAS_MANY, 'UserDepartment', 'UserID'),
        '_user_departments' => array(self::HAS_MANY, 'Departments', 'DepartmentID', 'through' => '_user_user_department', 'order' => 'DepartmentName ASC'),
        '_user_documents' => array(self::HAS_MANY, 'Documents', 'UserID', 'order' => 'Created DESC'),
        '_user_flows' => array(self::HAS_MANY, 'Flows', 'UserID'),
        '_user_files' => array(self::HAS_MANY, 'Files', 'UserID', 'order' => 'Created DESC'),
        '_user_answers' => array(self::HAS_MANY, 'Answers', 'UserID'),
        '_user_events' => array(self::HAS_MANY, 'Events', 'UserID')
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'id' => 'ID',
        'username' => "Логін",
        'password' => 'Пароль',
        'md5_password' => 'Відбиток паролю',
        'contacts' => 'Контактні дані',
        'info' => 'ПІБ і посада',
        'Department' => 'Підрозділи',
        'Role' => 'Права',
        'department_ids' => 'Обрані підрозділи',
        'role_ids' => 'Обрані права',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search() {
    // Warning: Please modify the following code to remove attributes that
    // should not be searched.

    $criteria = new CDbCriteria;
    $criteria->with = array('_user_departments','_user_roleassignments');

    $criteria->group = 't.id';
    $criteria->together = true;
    
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.username', $this->username, true);
    //$criteria->compare('t.password', $this->password, true);
    $criteria->compare('t.info', $this->info, true);
    $criteria->compare('t.contacts', $this->contacts, true);
    $criteria->compare('_user_departments.DepartmentName', $this->Department, true);
    $criteria->compare('_user_roleassignments.itemname', $this->Role, true);
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'sort'=>array(
            'defaultOrder'=>'id DESC',
        ),
        'pagination' => array(
            'pageSize' => 10,
        )
    ));
  }
  
}
