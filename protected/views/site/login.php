<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name;

?>
<?php $form=$this->beginWidget('CActiveForm', array(
  'id'=>'login-form',
  'enableClientValidation'=>false,
  'clientOptions'=>array(
    'validateOnSubmit'=>true,
  ),
  'action' => Yii::app()->CreateUrl("site/login"),
  'htmlOptions'=>array(
      'class'=>"form-signin dfbox",
  ),
)); ?>

  
  <h2 style="text-align: center;">Авторизація</h2>
  <div class="form-group">
      <?php echo $form->labelEx($model,'username'); ?>
      <?php echo $form->textField($model,'username',array(
          'placeholder' => "Логін",
          'class' => "form-control"
      )); ?>
  </div>
  <div class="form-group">
    <?php echo $form->labelEx($model,'password'); ?>
    <?php echo $form->passwordField($model,'password',array(
        'placeholder' => "Пароль",
        'class' => "form-control"
    )); ?>
  </div>
    
  <div class="row-fluid centred-buttons">
    <?php echo CHtml::submitButton('Увійти', array("class"=>"btn btn-large btn-primary")); ?>
  </div>

<?php $this->endWidget(); ?>