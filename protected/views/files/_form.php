<?php
/* @var $this FilesController */
/* @var $model Files */
$this->pageTitle=Yii::app()->name;
?>



<div class="dfbox">
  <h2 style="text-align: center;">
    <?php if($model->isNewRecord){ ?>
      Створення 
    <?php } else { ?>
      Редагування <?php } ?> запису про файл
  </h2>
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'files-form',
      'enableAjaxValidation'=>false,
      'htmlOptions' => array(
        'enctype'=>'multipart/form-data',
      ),
    )); 
    
    ?>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class='col-xs-6'>
        <div class="col-xs-12 form-group">
            <?php echo $form->labelEx($model,'file_itself'); ?>
            <?php echo $form->fileField($model,'file_itself',array(
            )); ?>
        </div>
        <?php if (Yii::app()->user->checkAccess('_FilesAdmin') && !$model->isNewRecord) {
        ?>
        <div class="col-xs-12 form-group">
            <?php echo $form->labelEx($model,'FileName'); ?>
            <?php echo $form->textField($model,'FileName',array(
                'placeholder' => "Назва",
                'class' => "form-control"
            )); ?>
        </div>
        <div class="col-xs-12 form-group">
            <?php echo $form->labelEx($model,'FileLocation'); ?>
            <?php echo $form->textField($model,'FileLocation',array(
                'placeholder' => "Розміщення",
                'class' => "form-control"
            )); ?>
        </div>
        <div class="col-xs-12 form-group">
            <?php echo $form->labelEx($model,'Created'); ?>
            <?php echo $form->textField($model,'Created',array(
                'placeholder' => "Створено",
                'class' => "form-control"
            )); ?>
        </div>
        <?php
        }
        if (Yii::app()->user->checkAccess('_FilesAdmin')) {
        ?>
        <div class="col-xs-12 form-group">
            <?php echo $form->labelEx($model,'UserID'); ?>
            <?php echo $form->dropDownList($model,'UserID',
              CHtml::listData(Users::model()->findAll(), 'id', 'username'),
              array(
                'class' => "form-control"
              )
            ); ?>
        </div>
        <?php
        }
        ?>
      </div>
      
      <div class='col-xs-6'>
        <input type="hidden" name="Files[document_ids][]" value="0" />
        <?php
        $this->many2ManyPicker( 'Files[document_ids]', 'DocumentInfo', Yii::app()->CreateUrl('files/getDocuments'),
                                $model->_file_documents, 'idDocument',
                                "Відношення до документів");
        ?>
      </div>
    
    </div>
    <?php
    if (Yii::app()->user->checkAccess('_FileAdmin') || $model->isNewRecord) {
    ?>
    <div class="row"  style="margin-left: 0px; margin-right: 0px;">

    </div>
    <?php
    }
    ?>
    
    <div class="row"  style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-4 form-group">
      </div>
      <div class="col-xs-4 form-group" style="text-align: center;">
        <?php echo $form->checkBox($model,'Visible',array(
        )); ?>
        <?php echo $form->labelEx($model,'Visible'); ?>
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