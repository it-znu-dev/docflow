<?php
/* @var $this DepartmentsController */
/* @var $model Departments */
$this->pageTitle=Yii::app()->name;
?>

<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> підрозділу
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'departments-form',
      'enableAjaxValidation'=>false,
    )); 
    
    ?>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
    
    <div class="col-xs-6 col-sm-3 form-group">
        <?php echo $form->labelEx($model,'DepartmentName'); ?>
        <?php echo $form->textField($model,'DepartmentName',array(
            'placeholder' => "Назва",
            'class' => "form-control"
        )); ?>
    </div>
    <div class="col-xs-6 col-sm-3 form-group">
      <?php echo $form->checkBox($model,'Hidden',array(
//         'checked'=>
//           (!$model->isNewRecord || $model->Hidden)? 'checked':false
      )); ?>
      <?php echo $form->labelEx($model,'Hidden'); ?>
    </div>
    
    <div class="col-sm-6 col-xs-12 form-group _selector">
      <input type="hidden" name="Departments[Groups][]" value="" />
          <?php
          $this->many2ManyPicker( 'Departments[Groups]', 'DeptGroupName', Yii::app()->CreateUrl('departments/getDeptGroups'),
                                  $model->_department_deptgroups, 'idDeptGroup',
                                  "Відношення до груп підрозділів");
          ?>
    </div>
    
    </div>
    

    

    <div class="centred-buttons">
      <?php echo CHtml::submitButton('Зберегти', array("class"=>"btn btn-large btn-primary")); ?>
    </div>
    <?php
    echo $form->errorSummary($model);
    $this->endWidget(); 
  ?>
</div>
<?php

?>