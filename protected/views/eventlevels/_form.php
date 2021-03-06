<?php
/* @var $this EventlevelsController */
/* @var $model Eventlevels */
$this->pageTitle=Yii::app()->name;
?>


<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> рівня заходів
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'eventlevels-form',
      'enableAjaxValidation'=>false,
    )); 
    
    ?>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
    
      <div class="col-xs-12 col-sm-6 form-group">
          <?php echo $form->labelEx($model,'LevelName'); ?>
          <?php echo $form->textField($model,'LevelName',array(
              'placeholder' => "Назва рівня",
              'class' => "form-control"
          )); ?>
      </div>
    
      <div class="col-xs-12 col-sm-6 form-group">
          <?php echo $form->labelEx($model,'LevelStyle'); ?>
          <?php echo $form->textField($model,'LevelStyle',array(
              'placeholder' => "Клас стилів",
              'class' => "form-control"
          )); ?>
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