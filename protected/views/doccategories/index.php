<?php
/* @var $this DoccategoriesController */
/* @var $model Doccategories */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'CategoryName', title: 'Назва категорії'},
      {field: 'CategoryCode', title: 'Код категорії'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('doccategories/xupdate'); ?>',
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
    $("div.doccategories table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#doccategories-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("doccategories/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Категорії документів</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "doccategories-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="doccategories_pager">{pager}</div>{items}<div class="doccategories_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idCategory',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idCategory); ?>" class="identifier">
          <?php echo CHtml::encode($data->idCategory); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idCategory); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("doccategories/update",array('id'=>$data->idCategory)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("doccategories/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idCategory); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'CategoryName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'CategoryName\' data-pk=\'".$data->idCategory."\' data-name=\'CategoryName\' data-type=\'textarea\'>"
        .CHtml::encode($data->CategoryName)."</a>"'
      ),
      array(
        'name' => 'CategoryCode',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'CategoryName\' data-pk=\'".$data->idCategory."\' data-name=\'CategoryCode\' data-type=\'textarea\'>"
        .CHtml::encode($data->CategoryCode)."</a>"'
      ),
    ),
    'htmlOptions' => array(
      'class' => "doccategories"
    )
  ));
  ?>
</div>
<?php

?>