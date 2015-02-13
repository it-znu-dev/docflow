<?php

/**
 * This is the model class for table "event_invited".
 *
 * The followings are the available columns in table 'event_invited':
 * @property integer $EventID
 * @property integer $DeptID
 * @property string $InvitedComment
 * @property integer $Seets
 */
class EventInvited extends CActiveRecord
{
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'event_invited';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('EventID, DeptID, InvitedComment', 'required'),
      array('EventID, DeptID, Seets', 'numerical', 'integerOnly'=>true),
      array('InvitedComment', 'length', 'max'=>255),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('EventID, DeptID, InvitedComment, Seets', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'EventID' => 'ідентифікатор заходу',
      'DeptID' => 'ідентифікатор запрошеного підрозділу або -1',
      'InvitedComment' => 'запрошені текстом',
      'Seets' => 'кількість місць',
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

    $criteria->compare('EventID',$this->EventID);
    $criteria->compare('DeptID',$this->DeptID);
    $criteria->compare('InvitedComment',$this->InvitedComment,true);
    $criteria->compare('Seets',$this->Seets);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return EventInvited the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
}
