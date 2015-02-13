<?php

/**
 * This is the model class for table "flow_respondent".
 *
 * The followings are the available columns in table 'flow_respondent':
 * @property integer $FlowID
 * @property integer $DeptID
 * @property integer $AnswerID
 *
 * @property Departments $_flow_respondent_department
 * @property Answers $_flow_respondent_answer
 * @property Flows $_flow_respondent_flow
 */
class FlowRespondent extends CActiveRecord
{
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'flow_respondent';
  }
  
  protected function beforeDelete(){
    parent::beforeDelete();
    if ($this->_flow_respondent_answer){
      $this->_flow_respondent_answer->delete();
    }
    return true;
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('FlowID, DeptID', 'required'),
      array('FlowID, DeptID, AnswerID', 'numerical', 'integerOnly'=>true),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('FlowID, DeptID, AnswerID', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_flow_respondent_department' => array(self::BELONGS_TO, 'Departments', 'DeptID'),
      '_flow_respondent_answer' => array(self::BELONGS_TO, 'Answers', 'AnswerID'),
      '_flow_respondent_flow' => array(self::BELONGS_TO, 'Flows', 'FlowID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'FlowID' => 'ідентифікатор розсилки',
      'DeptID' => 'ідентифікатор підрозділу або окремого респондента',
      'AnswerID' => 'ідентифікатор відповіді',
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

    $criteria->compare('FlowID',$this->FlowID);
    $criteria->compare('DeptID',$this->DeptID);
    $criteria->compare('AnswerID',$this->AnswerID);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return FlowRespondent the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
}
