<?php

/**
 * Ця модель створена для таблиці "roleassignments".
 *
 * Далі йде перелік стовпців таблиці 'roleassignments':
 * @property string $itemname
 * @property integer $userid
 * @property string $bizrule
 * @property string $data
 */
class Roleassignments extends CActiveRecord
{
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Roleassignments the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'roleassignments';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('itemname, userid', 'required'),
      array('userid', 'numerical', 'integerOnly'=>true),
      array('itemname', 'length', 'max'=>64),
      array('bizrule, data', 'safe'),
      // The following rule is used by search().
      // Please remove those attributes that should not be searched.
      array('itemname, userid, bizrule, data', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        //'role' => array(self::BELONGS_TO, 'Roles', 'itemname'),
        'user' => array(self::BELONGS_TO, 'Users', 'userid')
    );
  }
        

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
    'itemname' => 'Itemname',
    'userid' => 'Userid',
    'bizrule' => 'Bizrule',
    'data' => 'Data',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
   */
  public function search(){
    $criteria=new CDbCriteria;

    $criteria->compare('itemname',$this->itemname,true);
    $criteria->compare('userid',$this->userid);
    $criteria->compare('bizrule',$this->bizrule,true);
    $criteria->compare('data',$this->data,true);
    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }
}