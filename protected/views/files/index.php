<?php
/* @var $this FilesController */
/* @var $model Files */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">

  function beforeUpdate(){
    var fields = [
      {field: 'FileName', title: 'Назва файлу'},
      {field: 'FileLocation', title: 'Розміщення'},
      {field: 'Created', title: 'Створено'}
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('files/xupdate'); ?>',
          title: f.title,
          emptytext: 'немає',
          mode: 'inline',
          showbuttons: false,
          savenochange: true,
          success: function(response, newValue) {
            if (response){
              var response = JSON.parse(response);
              if(response.status === 'error') {
                return response.msg.join('| ');
              }
            }
          }
      });
      $('.'+f.field).on('shown', function(e, editable) {
          editable.input.$input.attr('class','form-control small');
      });
    }
    $('.Visible').editable({
        url: '<?php echo Yii::app()->CreateUrl('files/xupdate'); ?>',
        title: 'Видимий?',
        emptytext: "ні",
        mode: 'inline',
        showbuttons: false,
        savenochange: true,
        source: [
              {value: 0, text: 'ні'},
              {value: 1, text: 'так'}
        ],
        success: function(response, newValue) {
          if (response){
            var response = JSON.parse(response);
            if(response.status === 'error') {
              return response.msg.join('| ');
            }
          }
        }
    });
    $('.Visible').on('shown', function(e, editable) {
        editable.input.$input.attr('class','form-control small');
    });
    <?php
      if (!Yii::app()->user->checkAccess('_FilesAdmin')){
    ?>
    $('td:nth-of-type(2) a.editable-click').each(function( index ) { $(this).before($(this).text()); });
    $('td:nth-of-type(4) a.editable-click').each(function( index ) { $(this).before($(this).text()); });
    $('td:nth-of-type(7) a.editable-click').each(function( index ) { $(this).before($(this).text()); });
    
    $('td:nth-of-type(2) a.editable-click').remove();
    $('td:nth-of-type(4) a.editable-click').remove();
    $('td:nth-of-type(7) a.editable-click').remove();
    <?php
    }
    ?>
    
    $('a.identifier').click(function(){
      $('#control-actions-'+$(this).attr('id')).slideToggle();
      return false;
    });
    $('div.files table tbody tr td button.btn.btn-danger.btn-sm').click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm('Остаточно?')){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#files-grid').yiiGridView('update');
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
  }
  
  $(function(){
    beforeUpdate();
    $('.input-daterange').datepicker({
        format: 'dd.mm.yyyy',
        weekStart: 1,
        language: 'uk',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function(e){
      if(!(($('#DateStart').val().length == 0) || ($('#DateEnd').val().length == 0))){
        $('#files-grid').yiiGridView('update');
      }
    });
  });
</script>

<div class="dfbox">
  <h2 style="text-align: center;">
  <a href="<?php echo Yii::app()->CreateUrl("files/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Завантажені файли</h2>
  <div id="datepicker">
    Пошук із вказанням проміжку дат
    <div class="input-daterange input-group tb-datarange" >
      <input type="text" id="DateStart" class="input-sm form-control daterange-field" name="Files[DateStart]" />
      <span class="input-group-addon"> - </span>
      <input type="text" id="DateEnd" class="input-sm form-control daterange-field" name="Files[DateEnd]" />
    </div>
  </div>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "files-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="files_pager">{pager}</div>{items}<div class="files_pager">{pager}</div>',
    'beforeAjaxUpdate' => "function(id,data){
      var sdaterange = \$('.input-daterange :input').serialize();
      data.url = data.url + '&' + sdaterange;
    }",
    'afterAjaxUpdate' => "function(id,data){
      beforeUpdate();   
    }",
    'columns' => array(
      array(
        'name' => 'idFile',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idFile); ?>" class="identifier">
          <?php echo CHtml::encode($data->idFile); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idFile); ?>">
            <?php if ($data->exists){ ?>
            <a href="<?php 
            $params = array();
            $mime = CFileHelper::getMimeTypeByExtension($data->folder . $data->FileLocation);
            $params['id'] = $data->idFile;
            if ((stristr($mime,'pdf') !== false) || (stristr($mime,'image') !== false)){
              $params['iframe'] = "true";
            }
            echo Yii::app()->CreateUrl("files/download",$params); 
            ?>" 
              class="btn btn-success btn-sm" style="margin-bottom: 2px;" title="Завантажити" 
              <?php if (isset($params['iframe']) && $params['iframe'] == "true"){ ?>
              rel="prettyPhoto[iframes]" 
              <?php } ?>
              data-mime="<?php echo $mime; ?>" 
            >
              <span class="glyphicon glyphicon-<?php 
              if (isset($params['iframe']) && $params['iframe'] == "true"){ 
                echo "eye-open";
              } else {
                echo "save";
              }
              ?>" aria-hidden="true"></span>
            </a>
            <?php } else { ?>
              <div class="btn btn-warning btn-sm" style="margin-bottom: 3px;" >
                <span class="glyphicon glyphicon-remove-sign"></span>
              </div>
            <?php } ?>
            <a href="<?php echo Yii::app()->CreateUrl("files/update",array('id'=>$data->idFile)); ?>" 
              class="btn btn-primary btn-sm" title="Редагувати">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <?php
              if (Yii::app()->user->checkAccess('_FilesAdmin')){
            ?>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("files/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idFile); ?>"  title="Видалити">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
            <?php
              }
            ?>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'FileName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'FileName\' data-pk=\'".$data->idFile."\' data-name=\'FileName\' data-type=\'text\'>"
        .CHtml::encode($data->FileName)."</a>"'
      ),
      array(
        'name' => 'Document',
        'type' => 'raw',
        'value' => function($data){
          $criteria = new CDbCriteria();
          $criteria->with = array('_document_document_file');
          $criteria->group = "t.idDocument";
          $criteria->together = true;
          $criteria->compare("_document_document_file.FileID",$data->idFile);
          
          $models = Documents::model()->findAll($criteria);
          echo "<ul>";
          foreach ($models as $model){
            echo "<li>".CHtml::encode($model->DocumentInfo)."</li>";
          }
          echo "</ul>";
        },
      ),
      array(
        'name' => 'Created',
        'filter' => '',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'Created\' data-pk=\'".$data->idFile."\' data-name=\'Created\' data-type=\'text\'>"
        .CHtml::encode($data->Created)."</a>"'
      ),
      array(
        'name' => 'Visible',
        'type' => 'raw',
        'filter' => array(0=>"ні",1=>"так"), 
        'value' => '"<a href=\'#\' class=\'Visible\' data-pk=\'".$data->idFile."\' data-name=\'Visible\' data-type=\'select\'>".
        (($data->Visible == 1)? "так":"ні")."</a>"'
      ),
      array(
        'name' => 'UserInfo',
        'type' => 'raw',
        'value' => function($data){
          if ($data->_file_user){
          echo "<div class='dblock'>".CHtml::encode($data->_file_user->username)."</div>";
          echo "<div class='dblock'>".CHtml::encode($data->_file_user->info)."</div>";
          echo "<div class='dblock'>".CHtml::encode($data->_file_user->contacts)."</div>";
          } else {
            echo "<span class='label label-danger'>відсутні дані</span>";
          }
        },
      ),
      array(
        'name' => 'FileLocation',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'FileLocation\' data-pk=\'".$data->idFile."\' data-name=\'FileLocation\' data-type=\'text\'>"
        .CHtml::encode($data->FileLocation)."</a>"'
      ),
    ),
    'htmlOptions' => array(
      'class' => "files"
    )
  ));
  ?>
</div>
<?php

?>