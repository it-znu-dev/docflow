<?php
/* @var $this FlowsController */
/* @var $model Flows */
$this->pageTitle=Yii::app()->name;
?>

<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> розсилки
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'user-form',
      'enableAjaxValidation'=>false,
    )); 
    
    ?>
  
    <div class="row row-nomargins">
      <div class="col-xs-12 form-group dept_selector">
        <?php
        $this->many2ManyPicker( 
          'Flows[dept_ids]', 
          'DepartmentName', 
          Yii::app()->CreateUrl('flows/getDepartments'),
          $model->_flow_departments, 
          'idDepartment',
          "Респонденти",
          Yii::app()->CreateUrl('deptgroups/x')
        );
        ?>
      </div>
    </div>
  
    <div class="row row-nomargins" >
    <div class="col-xs-6 col-sm-2 form-group">
        <?php echo $form->labelEx($model,'FlowName'); ?>
        <?php echo $form->textField($model,'FlowName',array(
            'placeholder' => "автоматично",
            'class' => "form-control",
            "readonly" => true
        )); ?>
    </div>
    <?php if(Yii::app()->user->checkAccess("_DocsExtended")) {?>
    <div class="col-xs-6 col-sm-2 form-group">
        <?php echo CHtml::label('відмітки контролю',"ControlMark"); ?>
        <?php echo CHtml::textField('ControlMark',"",array(
            'placeholder' => "дата, особливості...",
            'class' => "form-control",
            'id' => 'ControlMark'
        )); ?>
    </div>
    <div class="col-xs-6 col-md-2 form-group">
      <?php echo $form->labelEx($model,'PeriodID'); ?>
      <?php echo $form->dropDownList($model,'PeriodID',
        CHtml::listData(Periods::model()->findAll(), 'idPeriod', 'PeriodName'),
        array(
          'class' => "form-control"
      )); ?>
    </div>
    <?php } ?>
    <div class="col-xs-12 col-sm-6 form-group">
        <?php echo $form->labelEx($model,'FlowDescription'); ?>
        <?php echo $form->textField($model,'FlowDescription',array(
            'placeholder' => "особливості розсилки",
            'class' => "form-control",
        )); ?>
    </div>

    </div>
    
    <div class="row row-nomargins">
      <div class="col-xs-12 form-group dept_selector">
        <?php
        $this->many2ManyPicker( 
          'Flows[document_ids]', 
          'DocumentInfo', 
          Yii::app()->CreateUrl('flows/getDocuments'),
          $model->__flow_documents, 
          'idDocument',
          "Документи"
        );
        ?>
      </div>
    </div>
  
  <div class="col-xs-12">
  <?php 
    if (Yii::app()->user->checkAccess('_FlowsAdmin')){
      echo $form->dropDownList($model,'UserID',CHtml::listData(Users::model()->findAll(),'id','username'),
        array(
          'class' => "form-control",
          'style'=>'width: 200px;'
        )
      );
    }
  ?>
  </div>
  <div class="centered">
    <?php if(!$model->isNewRecord){ ?>
      Застереження: у разі збереження усі відповіді  респондентів будуть знищені
    <?php } ?>
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