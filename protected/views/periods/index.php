<?php
/* @var $this PeriodsController */
/* @var $model Periods */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'PeriodName', title: 'Періодичність'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('periods/xupdate'); ?>',
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
    $("div.periods table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#periods-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("periods/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Періодичність розсилок</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "periods-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="periods_pager">{pager}</div>{items}<div class="periods_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idPeriod',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idPeriod); ?>" class="identifier">
          <?php echo CHtml::encode($data->idPeriod); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idPeriod); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("periods/update",array('id'=>$data->idPeriod)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("periods/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idPeriod); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'PeriodName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'PeriodName\' data-pk=\'".$data->idPeriod."\' data-name=\'PeriodName\' data-type=\'textarea\'>"
        .CHtml::encode($data->PeriodName)."</a>"'
      ),
    ),
    'htmlOptions' => array(
      'class' => "periods"
    )
  ));
  ?>
</div>
<?php

?>