<?php
/* @var $this DeptgroupsController */
/* @var $model Deptgroups */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'DeptGroupName', title: 'Група підрозділів'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('deptgroups/xupdate'); ?>',
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
    $("div.deptgroups table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#deptgroups-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("deptgroups/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Групи підрозділів системи "<?php echo CHtml::encode(Yii::app()->name); ?>"</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "deptgroups-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="deptgroups_pager">{pager}</div>{items}<div class="deptgroups_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idDeptGroup',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idDeptGroup); ?>" class="identifier">
          <?php echo CHtml::encode($data->idDeptGroup); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idDeptGroup); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("deptgroups/update",array('id'=>$data->idDeptGroup)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("deptgroups/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idDeptGroup); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'DeptGroupName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'DeptGroupName\' data-pk=\'".$data->idDeptGroup."\' data-name=\'DeptGroupName\' data-type=\'textarea\'>"
        .CHtml::encode($data->DeptGroupName)."</a>"'
      ),
      array(
        'name' => 'Dept',
        'type' => 'raw',
        'value' => function($data){
          echo '<ol>';
            $criteria = new CDbCriteria();
            $criteria->with = array('_department_deptgroups');
            $criteria->compare('_department_department_deptgroup.DeptGroupID',$data->idDeptGroup);
            $criteria->order = 't.DepartmentName ASC';
            $criteria->together = true;

            $models = Departments::model()->findAll($criteria);
            foreach ($models as $model){
              echo "<li>".CHtml::encode($model->DepartmentName)."</li>";
            }
          echo "</ol>";
        },
      ),
    ),
    'htmlOptions' => array(
      'class' => "deptgroups"
    )
  ));
  ?>
</div>
<?php

?>