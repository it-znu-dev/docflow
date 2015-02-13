<?php
/* @var $this DocumentsController */
/* @var $model Documents */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
   $(function(){
//     $('#plus_file').click(function(){
//       $("input[type='file']:last").after(
//         "<input type='file' name='files[]' class='form-group'/>"
//       );
//       return false;
//     });
  
    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        weekStart: 1,
        language: 'uk',
        autoclose: true,
        todayHighlight: true
    });
    
    $('.typeahead').each(function(){
      var $this = $(this);
      $(this).typeahead({
        source: function (query, process) {
            $.ajax({
              url: '<?php echo Yii::app()->CreateUrl('documents/typeahead'); ?>', 
              data: { q: query, name: $this.attr('data-name') }, 
              type: 'GET',
              dataType: 'json',
              success: function (data) {
                console.log(data);
                return process(data);
              },
              error: function(x,e){
                alert('error : '+e);
              },
              fail: function (x,e){
                alert('error : '+e);
              }
            });
        }
      });
    });
    
    <?php if ($model->isNewRecord){ ?>
    setInterval(function()
    { 
        $.ajax({
          type:"get",
          url:"<?php echo Yii::app()->CreateUrl("documents/expectedIndex"); ?>",
          dataType:"json",
          data: {
            "CategoryID":$("#Documents_CategoryID").val(),
            "SubmissionDate":$("#Documents_SubmissionDate").val(),
            "UserID":(($("#Documents_UserID").length)? $("#Documents_UserID").val():0),
            "cache" : false
          },
          success:function(data){
              $("#DocumentsSubmissionIndex").val(data.expected_index);
              console.log(data.expected_index);
          }
        });
    }, 1500);//time in milliseconds 
    <?php } ?>
  });
</script>

<div class="row row-nomargins dfbox" >
  <?php
    $form = $this->beginWidget('CActiveForm',array(
      'id'=>'document-form',
      'enableAjaxValidation'=>false,
      'htmlOptions' => array(
        'enctype'=>'multipart/form-data',
      ),
    )); 
    
    ?>
    <h1 class='row dfmetaheader'>Форма <?php 
      if ($model->isNewRecord){
        echo "створення";
      } else {
        echo "редагування";
      }
    ?> документа</h1>
    
    <?php if(Yii::app()->user->checkAccess('_DocsAdmin') 
      || Yii::app()->user->checkAccess('_DocsExtended')){ ?>
      <div class='row row-nomargins'>
        <div class="col-xs-12 form-group">
          <?php echo $form->labelEx($model,'Correspondent'); ?>
          <?php echo $form->textField($model,'Correspondent',array(
              'class' => "form-control typeahead",
              'data-name' => 'Correspondent',
              'autocomplete'=>"off"
          )); ?>
        </div>
      </div>
      
      <div class='row row-nomargins'>
        <div class="col-xs-12 col-sm-6">
            <?php if ($model->isNewRecord){ ?>
            <div class="col-xs-2 form-group" style="padding-left: 0px;">
              <label for="DocumentsSubmissionIndex">індекс</label>
              <input type="text" 
                     id="DocumentsSubmissionIndex" readonly 
                     class="form-control" />
            </div>
            <?php } else { ?>
            <div class="col-xs-2 form-group" style="padding-left: 0px;">
              <label for="Documents_SubmissionIndex">індекс</label>
              <?php echo $form->textField($model,'SubmissionIndex',array(
                  'class' => "form-control",
              )); ?>
            </div>
            <?php } ?>
            <div class="col-xs-4 form-group" style="padding-left: 0px;">
              <?php echo $form->labelEx($model,'SubmissionDate'); ?>
              <?php echo $form->textField($model,'SubmissionDate',array(
                  'class' => "form-control datepicker",
              )); ?>
            </div>
            <div class="col-xs-6 form-group">
              <?php echo $form->labelEx($model,'CategoryID'); ?>
              <?php echo $form->dropDownList($model,'CategoryID',
                Doccategories::model()->dropDown(),
                array(
                  'class' => "form-control"
                )
              ); ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <?php echo $form->labelEx($model,'ExternalIndex'); ?>
          <?php echo $form->textField($model,'ExternalIndex',array(
              'class' => "form-control typeahead",
              'data-name' => 'ExternalIndex',
              'autocomplete'=>"off"
          )); ?>
        </div>
      </div>
      
      <div class='row row-nomargins'>
        <div class="col-xs-12 form-group">
          <?php echo $form->labelEx($model,'Summary'); ?>
          <?php echo $form->textArea($model,'Summary',array(
              'class' => "form-control"
          )); ?>
        </div>
      </div>
      
      <div class='row row-nomargins'>
        <div class="col-xs-12 form-group">
          <?php echo $form->labelEx($model,'Resolution'); ?>
          <?php echo $form->textField($model,'Resolution',array(
              'class' => "form-control typeahead",
              'data-name' => 'Resolution',
              'autocomplete'=>"off"
          )); ?>
        </div>
      </div> 
       
      <div class='row row-nomargins'>

        <div class="col-sm-3 col-xs-6 form-group">
          <?php echo $form->labelEx($model,'Signed'); ?>
          <?php echo $form->textField($model,'Signed',array(
              'class' => "form-control typeahead",
              'data-name' => 'Signed',
              'autocomplete'=>"off"
          )); ?>
        </div>
        <div class="col-sm-3 col-xs-6 form-group">
          <?php echo $form->labelEx($model,'TypeID'); ?>
          <?php echo $form->dropDownList($model,'TypeID',
            CHtml::listData(Doctypes::model()->findAll(), 'idType', 'TypeName'),
            array(
              'class' => "form-control"
            )
          ); ?>
        </div>
        
        <?php if(Yii::app()->user->checkAccess('_DocsAdmin')){ ?>
        <div class="col-sm-6 col-xs-12 form-group">
          <?php echo $form->labelEx($model,'UserID'); ?>
          <?php echo $form->dropDownList($model,'UserID',
            CHtml::listData(Users::model()->findAll(), 'id', 'username'),
            array(
              'class' => "form-control"
            )
          ); ?>
        </div>
        <?php } ?>
        
      </div>
      
    <?php } ?>
      
    <div class='row row-nomargins'>
     
     <div class="col-sm-6 col-xs-12 form-group">
        <?php echo $form->labelEx($model,'DocumentName'); ?>
        <?php echo $form->textField($model,'DocumentName',array(
            'class' => "form-control typeahead",
            'data-name' => 'DocumentName',
            'autocomplete'=>"off"
        )); ?>
      </div>
      
      <div class="col-sm-3 col-xs-6">
          <?php echo $form->labelEx($model,'file'); ?>
          <?php echo $form->fileField($model,'file',array(
          )); ?>
      </div>
      
      <div class="col-sm-3 col-xs-6">
          <?php if(!$model->isNewRecord){
            foreach ($model->doc_visible_files as $dfile){
              $download_url = "#";
              $name = "Файл не існує";
              if ($dfile->exists){
                $name = pathinfo($dfile->folder . $dfile->FileLocation, PATHINFO_EXTENSION) . ' - файл';
                $download_url = Yii::app()->CreateUrl("files/download",array('id'=>$dfile->idFile));
              }
              ?>
              <a href="<?php echo $download_url; ?>" class="btn btn-sm btn-default btn-margin">
                <?php echo  $name; ?>
              </a>
              <?php
            }
          }
          ?>
      </div>

    </div>
    
    <div class="row"  style="margin-left: 0px; margin-right: 0px;">
      <div class="col-xs-4 form-group">
      </div>
      <div class="col-xs-4 form-group" style="text-align: center;<?php
        echo ((Yii::app()->user->checkAccess('_DocsAdmin') || Yii::app()->user->checkAccess('_DocsExtended'))? "":"display:none;");
      ?>">
        <?php echo $form->checkBox($model,'Visible',(($model->isNewRecord)? array(
          'checked' => 'checked'
        ) : array())); ?>
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