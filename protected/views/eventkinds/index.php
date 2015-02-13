<?php
/* @var $this EventkindsController */
/* @var $model Eventkinds */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'KindName', title: 'Назва виду'},
      {field: 'KindStyle', title: 'Клас стилів виду'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('eventkinds/xupdate'); ?>',
          title: f.title,
          emptytext: "немає",
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
  }
    
  function elementEvents(){
    $("a.identifier").click(function(){
      $("#control-actions-"+$(this).attr('id')).slideToggle();
      return false;
    });
    $("div.eventkinds table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#eventkinds-grid').yiiGridView('update');
          }
        });
      }
      return false;
    });
  }
  
  $(function(){
    editableSet();
    elementEvents();
  });
</script>

<div class="dfbox">
  <h2 style="text-align: center;">
  <a href="<?php echo Yii::app()->CreateUrl("eventkinds/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Види заходів</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "eventkinds-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="eventkinds_pager">{pager}</div>{items}<div class="eventkinds_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idKind',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idKind); ?>" class="identifier">
          <?php echo CHtml::encode($data->idKind); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idKind); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("eventkinds/update",array('id'=>$data->idKind)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("eventkinds/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idKind); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'KindName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'KindName\' data-pk=\'".$data->idKind."\' data-name=\'KindName\' data-type=\'text\'>"
        .CHtml::encode($data->KindName)."</a>"'
      ),
      array(
        'name' => 'KindStyle',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'KindStyle\' data-pk=\'".$data->idKind."\' data-name=\'KindStyle\' data-type=\'text\'>"
        .CHtml::encode($data->KindStyle)."</a>"'
      ),
    ),
    'htmlOptions' => array(
      'class' => "eventkinds"
    )
  ));
  ?>
</div>
<?php

?>