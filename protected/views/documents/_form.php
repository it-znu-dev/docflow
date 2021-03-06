<?php
/* @var $this DocumentsController */
/* @var $model Documents */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  $(function(){
     
    $("#_confirmed").change(function(){
      if ($("#_confirmed").is(":checked")){
        $("#_confirmed_block").css('color','#008800');
      } else {
        $("#_confirmed_block").css('color','#CC3333');
      }
      
    });
    
    $("#document-form").submit(function(e){
      if (!$("#_confirmed").is(":checked")){
        e.preventDefault();
        $("#document-form input[type=submit]").attr("disabled",false);
        return false;
      }
    });
  
    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        weekStart: 1,
        language: 'uk',
        autoclose: true,
        todayHighlight: true
    });
    
    $("#Documents_Visible").change(function(){
      if (!$("#Documents_Visible").is(":checked")){
        $("input[type=text]").attr("disabled","disabled");
        $("select").attr("disabled","disabled");
        $("textarea").attr("disabled","disabled");
      } else {
        $("input[type=text]").attr("disabled",false);
        $("textarea").attr("disabled",false);
        $("select").attr("disabled",false);
        if ($("#DocumentsSubmissionIndex").length > 0){
          $("#DocumentsSubmissionIndex").attr("disabled","disabled");
         }
      }
    });
    
<?php if(Yii::app()->user->checkAccess('_DocsAdmin') 
  || Yii::app()->user->checkAccess('_DocsExtended')){ ?>
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
    
    var timer = 0;
    var SubmissionIndexCurrentValue = "";

    var checkInputChange = function(_SubmissionIndex){
      var SubmissionIndex = _SubmissionIndex;
      if (SubmissionIndexCurrentValue !== SubmissionIndex){
        SubmissionIndexCurrentValue = SubmissionIndex;
        $.ajax({
          type:"get",
          url:"<?php echo Yii::app()->CreateUrl("documents/searchIndexAndShowInfo"); ?>",
          dataType:"json",
          data: {
            "DocID":"<?php echo ((!$model->idDocument)? "0": $model->idDocument); ?>",
            "CategoryID":$("#Documents_CategoryID").val(),
            "SubmissionDate":$("#Documents_SubmissionDate").val(),
            "SubmissionIndex":SubmissionIndex,
            "UserID":(($("#Documents_UserID").length)? $("#Documents_UserID").val():0),
            "__" : Math.random()
          },
          cache : false,
          success:function(data){
              if (data.msg.length){
                $("#SubmissionIndexMsg").text(data.msg);
                <?php if($model->idDocument){ ?>
                $("#SubmissionIndexMsg").removeClass("alert-warning");
                $("#SubmissionIndexMsg").addClass("alert-danger");
                <?php } ?>
              } else {
                $("#SubmissionIndexMsg").text("Будьте уважні при виборі категорії документа");
                <?php if($model->idDocument){ ?>
                $("#SubmissionIndexMsg").removeClass("alert-danger");
                $("#SubmissionIndexMsg").addClass("alert-warning");
                <?php } ?>
              }
          }
        });
      }
    };
    
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
            "__" : Math.random()
          },
          "cache" : false,
          success:function(data){
              $("#DocumentsSubmissionIndex").val(data.expected_index);
              if (data.expected_index){
                SubmissionIndexCurrentValue = "";
                checkInputChange(data.expected_index-1);
              }
              console.log(data.expected_index);
          }
        });
    }, 1500);//time in milliseconds 
    <?php } else { ?>
            
      var startTimer = function () {
      	timer = setInterval(function(){
          checkInputChange($("#Documents_SubmissionIndex").val());
        }, 50); // (50 ms)
      };
      
      var endTimer = function () {
        clearInterval(timer);
      };
      
      $("#Documents_SubmissionIndex").focus(function() {
        // turn on timer
        startTimer();
      }).blur(function() {
        // turn off timer
        endTimer();
        SubmissionIndexCurrentValue = "";
      });
      
      $("#Documents_SubmissionDate").change(function(){
        SubmissionIndexCurrentValue = "";
        checkInputChange($("#Documents_SubmissionIndex").val());
      });
      $("#Documents_CategoryID").change(function(){
        SubmissionIndexCurrentValue = "";
        checkInputChange($("#Documents_SubmissionIndex").val());
      });

  
    <?php } ?>
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
        'class'=>' more-dark',
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
        <div class="col-xs-12 alert alert-warning" id="SubmissionIndexMsg">
          Будьте уважні при виборі категорії документа
        </div>
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
              <label for="DocumentsSubmissionIndex">Індекс документа</label>
              <input type="text" 
                     id="DocumentsSubmissionIndex" readonly 
                     class="form-control" />
            </div>
            <?php } else { ?>
            <div class="col-xs-2 form-group" style="padding-left: 0px;">
              <label for="Documents_SubmissionIndex">індекс</label>
              <?php echo $form->textField($model,'SubmissionIndex',array(
                  'class' => "form-control",
                  'data-name' => 'SubmissionIndex',
                  'autocomplete'=>"off"
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
      <div class="col-xs-8 form-group">
      </div>
      <div class="col-xs-4 form-group" style="text-align: right;<?php
        echo ((!$model->isNewRecord &&(
          Yii::app()->user->checkAccess('_DocsAdmin') || Yii::app()->user->checkAccess('_DocsExtended')))?
          "":"display:none;");
      ?>">
        <?php echo $form->checkBox($model,'Visible',(($model->isNewRecord)? array(
          'checked' => 'checked'
        ) : array())); ?>
        <?php echo $form->labelEx($model,'Visible'); ?>
      </div>
    </div>
    
    <div class="centred-buttons">
      <div class="checkbox" id="_confirmed_block">
        <label>
        <input type="checkbox" name="_confirmed" id="_confirmed" />
        Усі дані правильні
        </label>
      </div>
      <?php echo CHtml::submitButton('Зберегти', array("class"=>"btn btn-large btn-primary")); ?>
    </div>
    <?php
    echo $form->errorSummary($model);
    $this->endWidget(); 
  ?>

</div>