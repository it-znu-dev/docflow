<?php
/* @var $this UsersController */
/* @var $model Users */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  function getDescendantProp(desc, obj) {
      obj = obj || window;
      var arr = desc.split(".");
      while (arr.length && (obj = obj[arr.shift()]));
      return obj;
  }
  
  function editableSet(){
    var fields = [
      {field: 'username', title: 'логін'},
      {field: 'password', title: 'пароль'},
      {field: 'info', title: 'ПІБ та посада'},
      {field: 'contacts', title: 'контактні дані'}
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: '<?php echo Yii::app()->CreateUrl('users/xupdate'); ?>',
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
    $("div.users table tbody tr td button.btn.btn-danger.btn-sm").click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm("Остаточно?")){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#users-grid').yiiGridView('update');
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
  <a href="<?php echo Yii::app()->CreateUrl("users/create"); ?>" class="btn btn-lg btn-success">
  <span class="glyphicon glyphicon-plus"></span>
  </a>
  Користувачі системи "<?php echo CHtml::encode(Yii::app()->name); ?>"</h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "users-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="users_pager">{pager}</div>{items}<div class="users_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      editableSet();
      elementEvents();
    }",
    'columns' => array(
      array(
        'name' => 'id',
        'header' => 'ID',
        'type' => 'raw',
        'value' => function ($data){
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->id); ?>" class="identifier">
          <?php echo CHtml::encode($data->id); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->id); ?>">
            <a href="<?php echo Yii::app()->CreateUrl("users/update",array('id'=>$data->id)); ?>" 
              class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <button type="button" class="btn btn-danger btn-sm" 
              data-link="<?php echo Yii::app()->CreateUrl("users/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->id); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
          </div>
          <?php
         },
      ),
      array(
        'name' => 'username',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'username\' data-pk=\'".$data->id."\' data-name=\'username\' data-type=\'text\'>"
        .CHtml::encode($data->username)."</a>"'
      ),
      array(
        'name' => 'info',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'info\' data-pk=\'".$data->id."\' data-name=\'info\' data-type=\'textarea\'>".
        CHtml::encode($data->info)."</a>"'
      ),
      array(
        'name' => 'contacts',
        'type' => 'raw',
        'value' => '"<a href=\'#\' class=\'contacts\' data-pk=\'".$data->id."\' data-name=\'contacts\' data-type=\'text\'>"
        .CHtml::encode($data->contacts)."</a>"'
      ),
      array(
        'name' => 'Department',
        'type' => 'raw',
        'value' => function($data){
          echo '<ul><li>';
            $criteria = new CDbCriteria();
            $criteria->with = array('_department_user_department');
            $criteria->compare('_department_user_department.UserID',$data->id);
            $criteria->together = true;
            $arr_depts = array();
            $depts = Departments::model()->findAll($criteria);
            foreach ($depts as $dept){
              $arr_depts[] = CHtml::encode($dept->DepartmentName);
            }
          echo implode('</li><li>',$arr_depts).'</li></ul>';
        },
      ),
      array(
        'name' => 'Role',
        'type' => 'raw',
        'value' => function($data){
          echo '<ul>';
            $criteria = new CDbCriteria();
            $criteria->compare('userid',$data->id);
            $criteria->together = true;
            
            $roles = Roleassignments::model()->findAll($criteria);
            foreach ($roles as $role){
              echo '<li>'.CHtml::encode($role->itemname).'</li>';
            }
          echo '</ul>';
        },
      ),
    ),
    'htmlOptions' => array(
      'class' => "users"
    )
  ));
  ?>
</div>
<?php

?>