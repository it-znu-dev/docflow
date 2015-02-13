<?php

/**
 * This is the model class for table "answers".
 *
 * The followings are the available columns in table 'answers':
 * @property integer $idAnswer
 * @property integer $UserID
 * @property string $Created
 * @property string $AnswerText
 *
 * @property Users $_answer_user
 * @property FlowRespondent[] _answer_flow_respondents
 */
class Answers extends CActiveRecord
{
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'answers';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('UserID, Created', 'required'),
      array('UserID', 'numerical', 'integerOnly'=>true),
      array('AnswerText', 'safe'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('idAnswer, UserID, Created, AnswerText', 'safe', 'on'=>'search'),
    );
  }

  
  protected function beforeValidate(){
    parent::beforeValidate();
    $this->Created = date('Y-m-d H:i:s');
    return true;
  }
  
  protected function beforeDelete(){
    parent::beforeDelete();
    $flow_resps = FlowRespondent::model()->findAllByAttributes(array(
      'AnswerID' => $this->idAnswer
    ));
    foreach ($flow_resps as $m){
      /* @var $m FlowRespondent */
      $m->AnswerID = null;
      $m->save();
    }
    return true;
  }
  
  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_answer_user' => array(self::BELONGS_TO, 'Users', 'UserID'),
      '_answer_flow_respondents' => array(self::HAS_MANY, 'FlowRespondent', 'AnswerID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'idAnswer' => 'автоінкрементний ідентифікатор',
      'UserID' => 'ідентифікатор користувача, власника відповіді',
      'Created' => 'дата і час створення',
      'AnswerText' => 'коментар',
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

    $criteria->compare('idAnswer',$this->idAnswer);
    $criteria->compare('UserID',$this->UserID);
    $criteria->compare('Created',$this->Created,true);
    $criteria->compare('AnswerText',$this->AnswerText,true);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Answers the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
  
}
