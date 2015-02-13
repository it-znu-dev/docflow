<?php

/**
 * This is the model class for table "doctypes".
 *
 * The followings are the available columns in table 'doctypes':
 * @property integer $idType
 * @property string $TypeName
 * 
 * The followings are the available model relations:
 * @property Documents[] $_doctype_documents
 *
 *
 */
class Doctypes extends CActiveRecord {
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Doctypes the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'doctypes';
  }

  
  protected function beforeDelete() {
    parent::beforeDelete();
//     foreach ($this->_doctype_documents as $model){
//       $model->delete();
//     }
    return true;
  }
  
  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
        array('TypeName', 'required'),
        array('TypeName', 'length', 'max' => 255),
        array('idType', 'numerical', 'integerOnly'=>true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('idType, TypeName, CategoryCode', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        //'_doctype_documents' => array(self::HAS_MANY, 'Documents', 'TypeID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'idType' => 'Автоінкрементний ідентифікатор',
        'TypeName' => "Назва типу",
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
    
    $criteria->compare('t.idType', $this->idType);
    $criteria->compare('t.TypeName', $this->TypeName, true);
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'pagination' => array(
            'pageSize' => 100,
        )
    ));
  }
  
}
