<?php
/* @var $this DoctypesController */
/* @var $model Doctypes */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'TypeName', title: 'Назва типу'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('doctypes/xupdate'); ?>',
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
    $("div.doctypes table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#doctypes-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("doctypes/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Типи документів</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "doctypes-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="doctypes_pager">{pager}</div>{items}<div class="doctypes_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idType',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idType); ?>" class="identifier">
          <?php echo CHtml::encode($data->idType); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idType); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("doctypes/update",array('id'=>$data->idType)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("doctypes/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idType); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'TypeName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'TypeName\' data-pk=\'".$data->idType."\' data-name=\'TypeName\' data-type=\'textarea\'>"
        .CHtml::encode($data->TypeName)."</a>"'
      ),
    ),
    'htmlOptions' => array(
      'class' => "doctypes"
    )
  ));
  ?>
</div>
<?php

?>