<?php
/* @var $this UsersController */
/* @var $model Users */
$this->pageTitle=Yii::app()->name;
?>

<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> користувача
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'user-form',
      'enableAjaxValidation'=>false,
    )); 
    
    ?>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
    <div class="col-xs-6 col-md-3 form-group">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username',array(
            'placeholder' => "Логін",
            'class' => "form-control"
        )); ?>
    </div>
    <div class="col-xs-6 col-md-3 form-group">
      <?php echo $form->labelEx($model,'password'); ?>
      <?php echo $form->textField($model,'password',array(
          'placeholder' => "Пароль",
          'class' => "form-control"
      )); ?>
    </div>
    
    <div class="col-xs-6 col-md-3 form-group">
        <?php echo $form->labelEx($model,'contacts'); ?>
        <?php echo $form->textArea($model,'contacts',array(
            'placeholder' => "Контактні дані",
            'class' => "form-control",
            'style' => "height: 100px;"
        )); ?>
    </div>
    <div class="col-xs-6 col-md-3 form-group">
      <?php echo $form->labelEx($model,'info'); ?>
      <?php echo $form->textArea($model,'info',array(
          'placeholder' => "ПІБ та посада",
          'class' => "form-control",
          'style' => "height: 100px;"
      )); ?>
    </div>
    </div>
    
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
    <div class="col-sm-6 col-xs-12 form-group dept_selector">
      <?php
      $this->many2ManyPicker(
        'Users[department_ids]', 
        'DepartmentName', 
        Yii::app()->CreateUrl('users/getDepartments'),
        $model->_user_departments, 
        'idDepartment',
        "Відношення до підрозділів"
      );
      ?>
    </div>
    
    <div class="col-sm-6 col-xs-12 form-group role_selector">
      <?php
      $this->many2ManyPicker(
        'Users[role_ids]', 
        'name', 
        Yii::app()->CreateUrl('users/getRoles'),
        $model->_user_roles, 
        'name',
        "Права та ролі користувачів"
      );
      ?>
    </div>
    </div>
    <div class="centred-buttons">
      <?php echo CHtml::submitButton('Зберегти', 
        array("class"=>"btn btn-large btn-primary")); ?>
    </div>
    <?php
    echo $form->errorSummary($model);
    $this->endWidget(); 
  ?>
</div>
<?php

?>