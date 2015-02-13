<?php

/**
 * This is the model class for table "eventlevels".
 *
 * The followings are the available columns in table 'eventlevels':
 * @property integer $idLevel
 * @property string $LevelName
 * @property string $LevelStyle
 * 
 * The followings are the available model relations:
 * @property Events[] $_eventlevel_events
 *
 *
 */
class Eventlevels extends CActiveRecord {
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Eventlevels the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'eventlevels';
  }

  
  protected function beforeDelete() {
    parent::beforeDelete();
    foreach ($this->_eventlevel_events as $model){
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
        array('LevelName', 'required'),
        array('LevelName', 'length', 'max' => 255),
        array('LevelStyle', 'length', 'max' => 32),
        array('idLevel', 'numerical', 'integerOnly'=>true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('idLevel, LevelName, LevelStyle', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        '_eventlevel_events' => array(self::HAS_MANY, 'Events', 'LevelID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'idLevel' => 'Автоінкрементний ідентифікатор',
        'LevelName' => "Назва рівня",
        'LevelStyle' => 'Клас стилів',
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
    
    $criteria->compare('t.idLevel', $this->idLevel);
    $criteria->compare('t.LevelName', $this->LevelName, true);
    $criteria->compare('t.LevelStyle', $this->LevelStyle, true);
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'pagination' => array(
            'pageSize' => 100,
        )
    ));
  }
  
}
