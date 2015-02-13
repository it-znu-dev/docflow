<?php

/**
 * This is the model class for table "doccategories".
 *
 * The followings are the available columns in table 'doccategories':
 * @property integer $idCategory
 * @property string $CategoryName
 * @property string $CategoryCode
 * 
 * The followings are the available model relations:
 * @property Documents[] $_doccategory_documents
 *
 *
 */
class Doccategories extends CActiveRecord {
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Doccategories the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'doccategories';
  }

  
  protected function beforeDelete() {
    parent::beforeDelete();
//     foreach ($this->_doccategory_documents as $model){
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
        array('CategoryName', 'required'),
        array('CategoryName', 'length', 'max' => 255),
        array('CategoryCode', 'length', 'max' => 32),
        array('idCategory', 'numerical', 'integerOnly'=>true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('idCategory, CategoryName, CategoryCode', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        //'_doccategory_documents' => array(self::HAS_MANY, 'Documents', 'CategoryID'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'idCategory' => 'Автоінкрементний ідентифікатор',
        'CategoryName' => "Назва категорії",
        'CategoryCode' => 'Код категорії',
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
    
    $criteria->compare('t.idCategory', $this->idCategory);
    $criteria->compare('t.CategoryName', $this->CategoryName, true);
    $criteria->compare('t.CategoryCode', $this->CategoryCode, true);
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'pagination' => array(
            'pageSize' => 100,
        )
    ));
  }
  
  public function dropDown(){
    $criteria = new CDbCriteria;
    $criteria->order = "CategoryName ASC";
    $models = $this->findAll($criteria);
    $arr = array();
    foreach ($models as $model){
      $arr[$model->idCategory] = $model->CategoryName. 
        (($model->CategoryCode)? " [".$model->CategoryCode."]":"");
    }
    return $arr;
  }
  
}
