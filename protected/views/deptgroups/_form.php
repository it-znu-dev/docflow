<?php
/* @var $this DeptgroupsController */
/* @var $model Deptgroups */
$this->pageTitle=Yii::app()->name;
?>

<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> групи підрозділів
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'deptgroup-form',
      'enableAjaxValidation'=>false,
    )); 
    
    ?>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
    
    <div class="col-xs-12 form-group">
        <?php echo $form->labelEx($model,'DeptGroupName'); ?>
        <?php echo $form->textField($model,'DeptGroupName',array(
            'placeholder' => "Назва",
            'class' => "form-control"
        )); ?>
    </div>

    <div class="col-sm-12 col-xs-12 form-group _selector">
        <?php
        $this->many2ManyPicker( 'Deptgroups[Depts]', 'DepartmentName', Yii::app()->CreateUrl('deptgroups/getDepartments'),
                                $model->_deptgroup_departments, 'idDepartment',
                                "Відношення до підрозділів");
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