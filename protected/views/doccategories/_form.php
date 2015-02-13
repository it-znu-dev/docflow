<?php
/* @var $this DoccategoriesController */
/* @var $model Doccategories */
$this->pageTitle=Yii::app()->name;
?>


<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> категорії документа
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'doccategories-form',
      'enableAjaxValidation'=>false,
    )); 
    
    ?>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
    
      <div class="col-xs-12 col-sm-6 form-group">
          <?php echo $form->labelEx($model,'CategoryName'); ?>
          <?php echo $form->textField($model,'CategoryName',array(
              'placeholder' => "Назва категорії",
              'class' => "form-control"
          )); ?>
      </div>
      <div class="col-xs-12 col-sm-6 form-group">
          <?php echo $form->labelEx($model,'CategoryCode'); ?>
          <?php echo $form->textField($model,'CategoryCode',array(
              'placeholder' => "Код категорії",
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