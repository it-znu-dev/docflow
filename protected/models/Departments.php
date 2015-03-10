<?php

/**
 * This is the model class for table "departments".
 *
 * The followings are the available columns in table 'departments':
 * @property integer $idDepartment
 * @property string $DepartmentName
 * @property integer $Hidden
 * 
 * The followings are the available model relations:
 * @property UserDepartment[] $_department_user_department
 * @property Users[] $_department_users
 * @property DepartmentDeptgroup[] $_department_department_deptgroup
 * @property Deptgroups[] $_department_deptgroups
 * @property FlowRespondent[] $_department_flow_respondent
 *
 * @property string $Groups
 * @property string $GroupName
 */
class Departments extends CActiveRecord {
  public $Groups;
  public $GroupName;
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
    return 'departments';
  }

  protected function afterSave() {
    parent::afterSave();
    if (is_array($this->Groups)){
      foreach ($this->_department_department_deptgroup as $model){
        $model->delete();
      }
      foreach ($this->Groups as $_id){
        if (intval($_id) > 0){
          $m = new DepartmentDeptgroup();
          $m->DepartmentID = $this->idDepartment;
          $m->DeptGroupID = $_id;
          $m->save();
        }
      }
    }
    return true;
  }
  
  protected function beforeDelete() {
    parent::beforeDelete();
    foreach ($this->_department_department_deptgroup as $model){
      $model->delete();
    }
    foreach ($this->_department_users as $model){
      $model->delete();
    }
    foreach ($this->_department_flow_respondent as $model){
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
        array('DepartmentName', 'required'),
        array('DepartmentName', 'length', 'max' => 255),
        array('idDepartment, Hidden', 'numerical', 'integerOnly'=>true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('idDepartment, DepartmentName, Hidden', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        '_department_user_department' => array(self::HAS_MANY, 'UserDepartment', 'DepartmentID'),
        '_department_users' => array(self::HAS_MANY, 'Users', 'UserID', 'through' => '_department_user_department'),
        '_department_department_deptgroup' => array(self::HAS_MANY, 'DepartmentDeptgroup', 'DepartmentID'),
        '_department_deptgroups' => array(self::HAS_MANY, 'Deptgroups', 'DeptGroupID', 
          'through' => '_department_department_deptgroup', 'order' => '_department_deptgroups.DeptGroupName ASC'),
        '_department_flow_respondent' => array(self::HAS_MANY, 'FlowRespondent', 'DeptID')
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'idDepartment' => 'Автоінкрементний ідентифікатор підрозділу або окремого респондента',
        'DepartmentName' => "Назва підрозділу",
        'Hidden' => 'Приховано?',
        'GroupName' => 'Група підрозділів'
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
    $criteria->with = array('_department_deptgroups');
    $criteria->group = 't.idDepartment';
    $criteria->together = true;
    
    $criteria->compare('t.idDepartment', $this->idDepartment);
    $criteria->compare('t.DepartmentName', $this->DepartmentName, true);
    $criteria->compare('t.Hidden', $this->Hidden);
    //$criteria->compare('_department_users.info', $this->UserInfo, true);
    $criteria->compare('_department_deptgroups.DeptGroupName', $this->GroupName, true);
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'sort'=>array(
            'defaultOrder'=>'idDepartment DESC',
        ),
        'pagination' => array(
            'pageSize' => 10,
        )
    ));
  }
  
}
