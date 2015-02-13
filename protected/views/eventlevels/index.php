<?php
/* @var $this EventlevelsController */
/* @var $model Eventlevels */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'LevelName', title: 'Назва рівня'},
      {field: 'LevelStyle', title: 'Клас стилів рівня'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('eventlevels/xupdate'); ?>',
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
    $("div.eventlevels table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#eventlevels-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("eventlevels/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Рівні заходів</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "eventlevels-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="eventlevels_pager">{pager}</div>{items}<div class="eventlevels_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idLevel',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idLevel); ?>" class="identifier">
          <?php echo CHtml::encode($data->idLevel); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idLevel); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("eventlevels/update",array('id'=>$data->idLevel)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("eventlevels/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idLevel); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'LevelName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'LevelName\' data-pk=\'".$data->idLevel."\' data-name=\'LevelName\' data-type=\'text\'>"
        .CHtml::encode($data->LevelName)."</a>"'
      ),
      array(
        'name' => 'LevelStyle',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'LevelStyle\' data-pk=\'".$data->idLevel."\' data-name=\'LevelStyle\' data-type=\'text\'>"
        .CHtml::encode($data->LevelStyle)."</a>"'
      ),
    ),
    'htmlOptions' => array(
      'class' => "eventlevels"
    )
  ));
  ?>
</div>
<?php

?>