<?php

/**
 * This is the model class for table "event_flow".
 *
 * The followings are the available columns in table 'event_flow':
 * @property integer $EventID
 * @property integer $FlowID
 * 
 * The followings are the available model relations:
 * @property Flows $_event_flow_flow
 * @property Events $_event_flow_event
 */
class EventFlow extends CActiveRecord
{
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'event_flow';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('EventID, FlowID', 'required'),
      array('EventID, FlowID', 'numerical', 'integerOnly'=>true),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('EventID, FlowID', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_event_flow_flow' => array(self::BELONGS_TO, 'Flows', 'FlowID'),
      '_event_flow_event' => array(self::BELONGS_TO, 'Events', 'EventID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'EventID' => 'ідентифікатор заходу',
      'FlowID' => 'ідентифікатор розсилки',
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
    $criteria->compare('FlowID',$this->FlowID);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return EventFlow the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
}
