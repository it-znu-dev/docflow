<?php

/**
 * This is the model class for table "deptgroups".
 *
 * The followings are the available columns in table 'deptgroups':
 * @property integer $idDeptGroup
 * @property string $DeptGroupName
 * 
 * The followings are the available model relations:
 * @property DepartmentDeptgroup[] $_deptgroup_department_deptgroup
 * @property Departments[] $_deptgroup_departments
 *
 * @property integer[] $Depts
 * @property string $Dept
 *
 */
class Deptgroups extends CActiveRecord {
  public $Depts;
  public $Dept;
  /**
   * Returns the static model of the specified AR class.
   * @param string $className active record class name.
   * @return Deptgroups the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'deptgroups';
  }

  protected function afterSave() {
    parent::afterSave();
    if (is_array($this->Depts)){
      foreach ($this->_deptgroup_department_deptgroup as $model){
        $model->delete();
      }
      foreach ($this->Depts as $_id){
        $m = new DepartmentDeptgroup();
        $m->DepartmentID = $_id;
        $m->DeptGroupID = $this->idDeptGroup;
        $m->save();
      }
    }
    return true;
  }
  
  protected function beforeDelete() {
    parent::beforeDelete();
    foreach ($this->_deptgroup_department_deptgroup as $model){
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
        array('DeptGroupName', 'required'),
        array('DeptGroupName', 'length', 'max' => 255),
        array('idDeptGroup', 'numerical', 'integerOnly'=>true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('idDeptGroup, DeptGroupName', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
        '_deptgroup_department_deptgroup' => array(self::HAS_MANY, 'DepartmentDeptgroup', 'DeptGroupID'),
        '_deptgroup_departments' => array(self::HAS_MANY, 'Departments', 'DepartmentID', 
          'through' => '_deptgroup_department_deptgroup')
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
        'idDeptGroup' => 'Автоінкрементний ідентифікатор групи підрозділів',
        'DeptGroupName' => "Назва групи підрозділів",
        'Dept' => 'Підрозділи',
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
    $criteria->with = array('_deptgroup_departments');
    $criteria->group = 't.idDeptGroup';
    $criteria->together = true;
    
    $criteria->compare('t.idDeptGroup', $this->idDeptGroup);
    $criteria->compare('t.DeptGroupName', $this->DeptGroupName, true);
    $criteria->compare('_deptgroup_departments.DepartmentName', $this->Dept, true);
    return new CActiveDataProvider($this, array(
        'criteria' => $criteria,
        'pagination' => array(
            'pageSize' => 10,
        )
    ));
  }
  
  /**
   * Повертає масив назв груп підрозділів або підрозділів (якщо не усі увійшли в групу)
   *  по мірі входження підмасивів ідентифікаторів підрозділів у групу
   * @param array $dept_ids масив ідентифікаторів підрозділів
   * @return array
   */
  public static function getDeptGroupNamesOrDeptNamesByIds($dept_ids){
    if (!is_array($dept_ids)){
      return array();
    }
    $dept_group_counters = array();
    $names = array();
    $names['groups'] = array();
    $names['departments'] = array();
    foreach ($dept_ids as $dept_id){
      /* @var $dept_id integer */
      $dept = Departments::model()->findByPk(intval($dept_id));
      if (!$dept){
        continue;
      }
      $dept_dept_groups = $dept->_department_department_deptgroup;
      if (count($dept_dept_groups) == 0 || !is_array($dept_dept_groups)){
        $names['departments'][$dept->idDepartment] = $dept->DepartmentName;
        continue;
      }
      foreach ($dept_dept_groups as $dept_g){
        if (!isset($dept_group_counters[$dept_g->DeptGroupID])){
          $dept_group_counters[$dept_g->DeptGroupID] = 1;
        } else {
          $dept_group_counters[$dept_g->DeptGroupID]++;
        }
      }
    }
    foreach ($dept_group_counters as $dept_group_id => $cnt){
      $dept_group = Deptgroups::model()->findByPk($dept_group_id);
      $dept_group_depts = $dept_group->_deptgroup_departments;
//      var_dump(
//        array(
//          'count($dept_group_depts)' => count($dept_group_depts),
//          'cnt' => $cnt
//        )
//      );
      if ($cnt >= count($dept_group_depts)){
        $names['groups'][$dept_group->idDeptGroup] = $dept_group->DeptGroupName;
      } else {
        foreach ($dept_group_depts as $dept){
          $names['departments'][$dept->idDepartment] = $dept->DepartmentName;
        }
      }
    }
    return $names;
  }
  
}
