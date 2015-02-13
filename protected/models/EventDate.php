<?php

/**
 * This is the model class for table "event_date".
 *
 * The followings are the available columns in table 'event_date':
 * @property integer $EventID
 * @property string $EventDate
 * 
 * The followings are the available model relations:
 * @property Events $_event_date_event
 */
class EventDate extends CActiveRecord
{
  public $past;
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'event_date';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('EventID, EventDate', 'required'),
      array('EventID', 'numerical', 'integerOnly'=>true),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('EventID, EventDate', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_event_date_event' => array(self::BELONGS_TO, 'Events', 'EventID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'EventID' => 'ідентифікатор заходу',
      'EventDate' => 'дата заходу',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   * @param Events $event модель події для вибірки
   * Typical usecase:
   * - Initialize the model fields with values from filter form.
   * - Execute this method to get CActiveDataProvider instance which will filter
   * models according to data in model fields.
   * - Pass data provider to CGridView, CListView or any similar widget.
   *
   * @return CActiveDataProvider the data provider that can return the models
   * based on the search/filter conditions.
   */
  public function search($event){
    $criteria=new CDbCriteria;
    $criteria->with = array(
       '_event_date_event'
    );
    $criteria->select = array('*',
      'CONCAT(EventDate," ",'
        . 'IF(ISNULL(_event_date_event.StartTime),"08:00:00",_event_date_event.StartTime)) as EventFullTime',
      ( (strlen(trim($event->date_search)) == 0)? 
      	(($event->past)? $event->past: '0') : '-1') .' as past',
    );
    $criteria->together = true;
    $criteria->group = 't.EventDate,t.EventID';
    
    $criteria->compare('EventID',$this->EventID);
    $criteria->compare('EventDate',$event->date_search,true);
    if (!is_string($event->date_search) || $event->date_search === ""){
      if ($event->past == 0 ){
        $criteria->addCondition('NOW() < CONCAT(t.EventDate," ",'
          . 'IF(ISNULL(_event_date_event.StartTime),"08:00:00",_event_date_event.StartTime))');
      } 
      if ($event->past == 1){
        $criteria->addCondition('NOW() >= CONCAT(t.EventDate," ",'
          . 'IF(ISNULL(_event_date_event.FinishTime),"17:00:00",_event_date_event.FinishTime))');
      }
      if ($event->past == 2){
        $criteria->addCondition('NOW() < CONCAT(t.EventDate," ",'
          . 'IF(ISNULL(_event_date_event.FinishTime),"17:00:00",_event_date_event.FinishTime))');
        $criteria->addCondition('NOW() >= CONCAT(t.EventDate," ",'
          . 'IF(ISNULL(_event_date_event.StartTime),"08:00:00",_event_date_event.StartTime))');
      }
    }
    $criteria->compare('_event_date_event.idEvent',$event->idEvent);
    $criteria->compare('_event_date_event.EventName',$event->EventName,true);
    $criteria->compare('_event_date_event.EventDescription',$event->EventDescription,true);
    $criteria->compare('_event_date_event.Place',$event->Place,true);
    $criteria->compare('_event_date_event.Responsible',$event->Responsible,true);
    $criteria->compare('_event_date_event.Contacts',$event->Contacts,true);
    
    $criteria->compare('_event_date_event.KindID',$event->KindID);
    $criteria->compare('_event_date_event.LevelID',$event->LevelID);
    $criteria->compare('_event_date_event.UserID',$event->UserID);
    $criteria->compare('_event_date_event.Created',$event->Created,true);
    
    $criteria->order = 'EventFullTime '.( ($event->past == 0)? 'ASC':'DESC');
    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
      'pagination' => array(
          'pageSize' => 15
      )
    ));

  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return EventDate the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
}
