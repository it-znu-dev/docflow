<?php
/* @var $this DocumentsController */
/* @var $model Documents */
$this->pageTitle=Yii::app()->name;
$controller = $this;
?>
<script type="text/javascript">
  function beforeUpdate(){
    var fields = [
      {field: 'DocumentName', 
        title: '<?php echo $model->getAttributeLabel('DocumentName'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'TypeID', 
        title: '<?php echo $model->getAttributeLabel('TypeID'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>',
        source: [<?php
          $arr = Doctypes::model()->findAll();
          $json_arr = array();
          foreach ($arr as $val){
            $json_arr[] = "{value:".$val->idType.',text:"'.$val->TypeName.'"}';
          }
          echo implode(',',$json_arr);
        ?>]},
      <?php if(Yii::app()->user->checkAccess('_DocsAdmin')){ ?>
        {field: 'CategoryID', 
          title: '<?php echo $model->getAttributeLabel('CategoryID'); ?>', 
          xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>',
          source: [<?php
            $arr = Doccategories::model()->findAll();
            $json_arr = array();
            foreach ($arr as $val){
              $json_arr[] = "{value:".$val->idCategory.',text:"'.$val->CategoryName.' '.$val->CategoryCode.'"}';
            }
            echo implode(',',$json_arr);
          ?>]},
        {field: 'SubmissionDate', 
          title: '<?php echo $model->getAttributeLabel('SubmissionDate'); ?>', 
          xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
        {field: 'SubmissionIndex', 
          title: '<?php echo $model->getAttributeLabel('SubmissionIndex'); ?>', 
          xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      <?php } ?>
      {field: 'SubmissionInfo', 
        title: 'Дата надходження та індекс', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'ExternalIndex', 
        title: '<?php echo $model->getAttributeLabel('ExternalIndex'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'Summary', 
        title: '<?php echo $model->getAttributeLabel('Summary'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'Created', 
        title: '<?php echo $model->getAttributeLabel('Created'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'Correspondent', 
        title: '<?php echo $model->getAttributeLabel('Correspondent'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'Signed', 
        title: '<?php echo $model->getAttributeLabel('Signed'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'Resolution', 
        title: '<?php echo $model->getAttributeLabel('Resolution'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'DoneMark', 
        title: 'відмітка виконання', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'ControlMark', 
        title: 'відмітка контролю', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'ControlDate', 
        title: 'дата контролю', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
      {field: 'DoneDate', 
        title: 'дата виконання', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('documents/xupdate'); ?>'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      if (f.source == 'undefined'){
        
      }
      $('.'+f.field).editable({
          url: f.xupdate_url,
          title: f.title,
          //mode: 'inline',
          emptytext: ((['DoneDate','ControlDate','SubmissionDate'].indexOf(f.field) < 0)? 'немає':'дату не вказано'),
          success: function(response, newValue) {
            if (response){
              var response = JSON.parse(response);
              if(response.status === 'error') {
                return response.msg.join('| ');
              }
            }
          },
          format: 'YYYY-MM-DD',    
          viewformat: 'DD.MM.YYYY',    
          template: 'DD.MM.YYYY',    
          combodate: {
                  minYear: 2013,
                  maxYear: <?php echo date('Y'); ?>,
                  minuteStep: 30
          },
          source: f.source
      });
      
      $('.'+f.field).on('shown', function(e, editable) {
          editable.input.$input.attr('class','form-control input-sm area-sm');
          if (editable.input.$input.attr('rows')){
            editable.input.$input.attr('rows',3);
          }
      });
      
      $('.'+f.field).on('save', function(e, params) {
        $('#document-grid').yiiGridView('update');
      });
    }
    
    $('a.identifier').click(function(){
      $('#control-actions-'+$(this).attr('id')).slideToggle();
      return false;
    });
    $('table tbody tr td button.btn.btn-danger.btn-sm').click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm('Остаточно?')){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#document-grid').yiiGridView('update');
          }
        });
      }
      return false;
    });
    
    $('table tbody tr td a[data-action="file-hide"]').click(function(){
      var hide_file_url = $(this).attr('data-link');
      var hide_file_id = $(this).attr('data-pk');
      if (confirm('Остаточно?')){
        $.ajax({
          type: 'GET',
          url: hide_file_url,
          data: {id: hide_file_id},
          success: function(data){
            $('#document-grid').yiiGridView('update');
          }
        });
      }
      return false;
    });
    
    $("a[rel^='prettyPhoto']").prettyPhoto({
      allow_resize: true, 
      default_height: '95%', 
      default_width: '95%', 
      social_tools: "",
      });
      

    $('input[type=file]').fileupload({
        dataType: 'json',
        autoUpload: true,
        add: function(e, data) {
          var uploadErrors = [];
          var acceptFileTypes = /\/.*(pdf|rtf|odt|ods|txt|csv|jpg|gif|png|tiff|tif|bmp|jpeg|doc|docx|xls|xlsx|ppt|pptx|html|htm|js|css|zip|rar|7z|tar|gz)$/i;
          console.log(data.originalFiles[0]['type']);
          console.log(data.originalFiles[0]['size']);
          console.log("<?php echo intval(ini_get('upload_max_filesize')) * 1024 * 1024;  ?>");
          console.log("<?php echo intval(ini_get('post_max_size')) * 1024 * 1024;  ?>");
          if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
              uploadErrors.push('Файл такого типу не дозволено зберігати.');
          }
          if(data.originalFiles[0]['size'] > <?php echo intval(ini_get("post_max_size")) * 1024 * 1024; ?>
            || data.originalFiles[0]['size'] > <?php echo intval(ini_get('upload_max_filesize')) * 1024 * 1024; ?>) {
              uploadErrors.push('Файл занадто великий.');
          }
          if((uploadErrors.length === 0)
            && !confirm("Ви намагаєтесь завантажити великий файл, доведеться зачекати. Згодні?")){
              uploadErrors.push('Завантаження скасовано.');
          }
          if(uploadErrors.length > 0) {
              alert(uploadErrors.join("\n"));
          } else {
              data.submit();
          }
        },
        done: function (e, data) {
          if (data.result.status=="error"){
            alert (data.result.msg);
          } else {
            alert('OK');
            $('#document-grid').yiiGridView('update'); 
          }
        },
        error: function(e, data){
          alert('error: '+e.responseText);
        },
        fail: function(e, data){
          alert('error: '+e.responseText);
        }
    });
    
    $('[data-toggle="tooltip"]').tooltip();
  }

  $(function(){
    beforeUpdate();
  });
</script>

<div class="dfbox">
  <h2 class="centered">
  Документи </h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "document-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="documents_pager col-xs-6">{pager}</div>'
    . '<div class="col-xs-6 right-side">{summary}</div><br/>'
    . '{items}'
    . '<div class="documents_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      beforeUpdate();
    }",
    'columns' => array(
      array(
        'name' => 'idDocument',
        'header' => 'ID',
        'type' => 'raw',
        'filter' => "<div class='form group'>"
          ."<input type='text' name='Documents[idDocument]' value='".$model->idDocument."' 
            class='filter-field almost-full-width' placeholder='ID'/>
         </div>",
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idDocument); ?>" class="identifier">
          <?php echo CHtml::encode($data->idDocument); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idDocument); ?>">
            <?php if(!empty($data->file_ids)){ ?>
              <a href="<?php echo Yii::app()->CreateUrl("documents/downloadZip",array('id'=>$data->idDocument)); ?>" 
                class="btn btn-default btn-sm btn-bottom5" title="Завантажити архів усіх файлів документа">
                <span class="glyphicon glyphicon-compressed" aria-hidden="true"></span>
              </a>
            <?php } ?>
            
            <?php if(!(implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) !=
                  implode(',',$data->_document_user->department_ids) &&
                (!Yii::app()->user->checkAccess('_DocsAdmin')))){ ?>
            <a href="<?php echo Yii::app()->CreateUrl("documents/update",array('id'=>$data->idDocument)); ?>" 
              target="_blank"
              class="btn btn-primary btn-sm btn-bottom5">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <?php } ?>
            <a href="<?php echo Yii::app()->CreateUrl("flows/create",array('idDocument'=>$data->idDocument)); ?>" 
              target="_blank"
              class="btn btn-success btn-sm btn-bottom5" title="розсилка документа">
              <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
            </a>
            <a href="<?php echo Yii::app()->CreateUrl("documents/cardprint",array('id'=>$data->idDocument)); ?>"
              target="_blank"
              class="btn btn-primary btn-sm btn-bottom5" title="картка документа">
              <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
            </a>
            <?php if(Yii::app()->user->checkAccess('_DocsAdmin')){ ?>
            <button type="button" class="btn btn-danger btn-sm btn-separated"
              data-link="<?php echo Yii::app()->CreateUrl("documents/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idDocument); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
            <?php } ?>
          </div>
          <?php
         },
      ),
      array(
        'header' => 'документи',
        'type' => 'raw',
        'filter' => "<div class='form group'>"
        ."<input type='checkbox' name='Documents[WithControl]' ".(($model->WithControl)? "checked":"")." 
            class='filter-checkbox-text' title='Не знято з контролю'/><div class='very_small'>
            Не знято з контролю
            </div>"
        .CHtml::activeDropDownList($model,'SubmissionYear',$model->getYears(),
          array('class'=>'SubmissionYearFilter'))
        .CHtml::activeDropDownList($model,'CategoryID',(array(""=>"усі категорії")+Doccategories::model()->dropDown()),
          array('class'=>'CategoryFilter'))
        .CHtml::activeDropDownList($model,'TypeID',(array(""=>"усі типи")
            +CHtml::listData(Doctypes::model()->findAll("1 ORDER BY TypeName ASC"),"idType","TypeName")),
          array('class'=>'CategoryFilter'))
        ."<input type='text' name='Documents[DocumentInfo]' value='".$model->DocumentInfo."' 
            class='filter-field' placeholder='Кор. зміст та індекси'/>
          <input type='text' name='Documents[Correspondent]' value='".$model->Correspondent."' 
            class='filter-field' placeholder='Кореспондент'/>
          <input type='text' name='Documents[Signed]' value='".$model->Signed."' 
            class='filter-field' placeholder='Підписано'/>
          <input type='text' name='Documents[Resolution]' value='".$model->Resolution."' 
            class='filter-field' placeholder='На кого розписано(резолюція)'/>
          <input type='text' name='Documents[ControlMark]' value='".$model->ControlMark."' 
            class='filter-field' placeholder='Контроль'/>
          <input type='text' name='Documents[DoneMark]' value='".$model->DoneMark."' 
            class='filter-field' placeholder='Виконання'/>
          <input type='text' name='Documents[UserInfo]' value='".$model->UserInfo."'  
            class='filter-field' placeholder='Хто створив'/>
         </div>",
        'value' => function($data) use ($controller){
          $controller->renderPartial('_item',array('controller' => $controller, 'model' => $data));
        }
      ),
    ),
    'htmlOptions' => array(
      'class' => "documents"
    )
  ));
  ?>
</div>
<?php

?>