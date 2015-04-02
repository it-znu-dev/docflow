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
  
  /**
   * Повертає статистику
   */
  public function getStat(){
    $condition = "1";
    if ($this->id){
      $condition = "users.id=".intval($this->id);
    }
    $stat= Yii::app()->db->createCommand("
      select 
       users.id, 
       if(isnull(user_doc.user_doc_cnt),0,user_doc.user_doc_cnt) 
        as docs,
       if(isnull(user_file.user_file_cnt),0,user_file.user_file_cnt) 
        as files,
       if(isnull(user_in_flow.user_in_flow_cnt),0,user_in_flow.user_in_flow_cnt) 
        as in_flows,
       if(isnull(user_in_flow_wa.user_in_flow_wa_cnt),0,user_in_flow_wa.user_in_flow_wa_cnt) 
        as flows_wa,
       if(isnull(user_from_flow.user_from_flow_cnt),0,user_from_flow.user_from_flow_cnt) 
        as from_flows,
       if(isnull(user_answer_flow.user_answer_flow_cnt),0,user_answer_flow.user_answer_flow_cnt) 
        as answers,
       if(isnull(user_answer_flow.user_answer_days_avg),0,user_answer_flow.user_answer_days_avg) 
        as answers_days_avg
      from users
      left join (select count(d.idDocument) as user_doc_cnt, d.UserID as UserID from documents d group by d.UserID) user_doc
        on users.id=user_doc.UserID
      left join (select count(f.idFile) as user_file_cnt, f.UserID as UserID from files f group by f.UserID) user_file 
        on users.id=user_file.UserID
      left join (select count(distinct fl.idFlow) as user_in_flow_cnt, ud.UserID as UserID from flows fl
              left join flow_respondent fl_r on fl_r.FlowID=fl.idFlow
              left join user_department ud on fl_r.DeptID=ud.DepartmentID
            group by ud.UserID) user_in_flow 
        on users.id=user_in_flow.UserID
      left join (select count(distinct fl_r.FlowID) as user_in_flow_wa_cnt, ud.UserID as UserID from 
            user_department ud
              left join flow_respondent fl_r on fl_r.DeptID=ud.DepartmentID
            where fl_r.AnswerID is null
            group by ud.UserID) user_in_flow_wa 
        on users.id=user_in_flow_wa.UserID
      left join (select count(fl1.idFlow) as user_from_flow_cnt, fl1.UserID as UserID from flows fl1 group by fl1.UserID) user_from_flow 
        on users.id=user_from_flow.UserID
      left join (select count(distinct ans.idAnswer) as user_answer_flow_cnt,
            round(avg(hour(timediff(ans.Created, fl.Created)) / 24 ), 2) as user_answer_days_avg,
            ans.UserID as UserID
            from answers ans
              inner join flow_respondent fl_r on fl_r.AnswerID=ans.idAnswer
              inner join flows fl on fl_r.FlowID=fl.idFlow
            group by ans.UserID) user_answer_flow
        on users.id=user_answer_flow.UserID
      where ".$condition."
    ")->queryAll();
    
    return $stat;

  }
  
}
