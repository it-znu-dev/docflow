<?php

/**
 * This is the model class for table "user_department".
 *
 * The followings are the available columns in table 'user_department':
 * @property integer $UserID
 * @property integer $DepartmentID
 * 
 * The followings are the available model relations:
 * @property Users $user
 * @property Departments $department
 *
 */
class UserDepartment extends CActiveRecord {

  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return UserDepartment the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'user_department';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('UserID, DepartmentID', 'required'),
        array('UserID, DepartmentID', 'numerical', 'integerOnly'=>true),
        array('UserID, DepartmentID', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'user' => array(self::BELONGS_TO, 'Users', 'UserID'),
        'department' => array(self::BELONGS_TO, 'Departments', 'DepartmentID')
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'UserID' => 'UserID',
        'DepartmentID' => "DepartmentID",
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
    $criteria->compare('t.UserID', $this->UserID);
    
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
    ));
  }
  
}
