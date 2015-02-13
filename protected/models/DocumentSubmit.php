<?php

/**
 * This is the model class for table "document_submit".
 *
 * The followings are the available columns in table 'document_submit':
 * @property integer $DocumentID
 * @property string $SubmissionInfo
 *
 * The followings are the available model relations:
 * @property Documents $document
 */
class DocumentSubmit extends CActiveRecord{
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'document_submit';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('DocumentID, SubmissionInfo', 'required'),
      array('DocumentID', 'numerical', 'integerOnly'=>true),
      array('SubmissionInfo', 'length', 'max'=>255),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('DocumentID, SubmissionInfo', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        'document' => array(self::BELONGS_TO, 'Documents', 'DocumentID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'DocumentID' => 'ідентифікатор документа',
      'SubmissionInfo' => 'дата надходження та індекс документа',
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

    $criteria->compare('DocumentID',$this->DocumentID);
    $criteria->compare('SubmissionInfo',$this->SubmissionInfo,true);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return DocumentSubmit the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
}
