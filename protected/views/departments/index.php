<?php
/* @var $this DepartmentsController */
/* @var $model Departments */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  
  function editableSet(){
    var fields = [
      {field: 'DepartmentName', title: 'Підрозділ'},
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('departments/xupdate'); ?>',
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
    $('.Hidden').editable({
        url: '<?php echo Yii::app()->CreateUrl('departments/xupdate'); ?>',
        title: "Прихований?",
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
    $('.Hidden').on('shown', function(e, editable) {
        editable.input.$input.attr('class','form-control small');
    });
  }
    
  function elementEvents(){
    $("a.identifier").click(function(){
      $("#control-actions-"+$(this).attr('id')).slideToggle();
      return false;
    });
    $("div.departments table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#departments-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("departments/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Підрозділи системи "<?php echo CHtml::encode(Yii::app()->name); ?>"</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "departments-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="departments_pager">{pager}</div>{items}<div class="departments_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'idDepartment',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idDepartment); ?>" class="identifier">
          <?php echo CHtml::encode($data->idDepartment); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idDepartment); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("departments/update",array('id'=>$data->idDepartment)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("departments/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idDepartment); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'DepartmentName',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'DepartmentName\' data-pk=\'".$data->idDepartment."\' data-name=\'DepartmentName\' data-type=\'textarea\'>"
        .CHtml::encode($data->DepartmentName)."</a>"'
      ),
      array(
        'name' => 'Hidden',
        'type' => 'raw',
        'filter' => array(0=>"ні",1=>"так"), 
        'value' => '"<a href=\'#\' class=\'Hidden\' data-pk=\'".$data->idDepartment."\' data-name=\'Hidden\' data-type=\'select\'>".
        (($data->Hidden)? "так":"ні")."</a>"'
      ),
      array(
        'name' => 'GroupName',
        'type' => 'raw',
        'value' => function($data){
          echo '<ul>';
            $criteria = new CDbCriteria();
            $criteria->with = array('_deptgroup_departments');
            $criteria->compare('_deptgroup_department_deptgroup.DepartmentID',$data->idDepartment);
            $criteria->together = true;

            $deptgroups = Deptgroups::model()->findAll($criteria);
            foreach ($deptgroups as $deptgroup){
              echo "<li>".CHtml::encode($deptgroup->DeptGroupName)."</li>";
            }
          echo "</ul>";
        },
      ),
    ),
    'htmlOptions' => array(
      'class' => "departments"
    )
  ));
  ?>
</div>
<?php

?>