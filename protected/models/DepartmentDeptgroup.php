<?php

/**
 * This is the model class for table "department_deptgroup".
 *
 * The followings are the available columns in table 'department_deptgroup':
 * @property integer $DepartmentID
 * @property integer $DeptGroupID
 * 
 * The followings are the available model relations:
 * @property Departments $department
 * @property Departgroups $deptgroup
 *
 */
class DepartmentDeptgroup extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return DepartmentDeptgroup the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'department_deptgroup';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('DepartmentID, DeptGroupID', 'required'),
        array('DepartmentID, DeptGroupID', 'numerical', 'integerOnly'=>true),
        array('DepartmentID, DeptGroupID', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'department' => array(self::BELONGS_TO, 'Departments', 'DepartmentID'),
        'deptgroup' => array(self::BELONGS_TO, 'Deptgroups', 'DeptGroupID')
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'DepartmentID' => "DepartmentID",
        'DeptGroupID' => "DeptGroupID",
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

    $criteria->compare('t.DepartmentID', $this->DepartmentID);
    $criteria->compare('t.DeptGroupID', $this->DeptGroupID);
    
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }
  
}
