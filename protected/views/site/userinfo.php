<?php
/* @var $this SiteController */
/* @var $model Users */
$this->pageTitle="Інформація про користувача СЕД";
?>
<script type="text/javascript">
  function init(){
    var fields = [
      {field: 'info', 
        title: '<?php echo $model->getAttributeLabel('info'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('site/userinfo'); ?>'},
      {field: 'contacts', 
        title: '<?php echo $model->getAttributeLabel('contacts'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('site/userinfo'); ?>'},
      {field: 'password', 
        title: '<?php echo $model->getAttributeLabel('password'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('site/userinfo'); ?>'}
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: f.xupdate_url,
          title: f.title,
          mode: 'inline',
          emptytext: 'немає',
          success: function(response, newValue) {
            if (response){
              var response = JSON.parse(response);
              if(response.status === 'error') {
                return response.msg.join('| ');
              }
            }
          },
          source: f.source
      });
      $('.'+f.field).on('shown', function(e, editable) {
          console.log(editable.input.options.scope);
      });
    }
    
    $('[data-toggle="tooltip"]').tooltip();
  }

  $(function(){
    init();
  });
</script>

<div class="dfbox">
  <h2 style="text-align: center;">Інформація</h2>
  <div class="row row-nomargins">
  <div class="col-xs-12 col-sm-6">
  <div class="dfbox">
  <?php
    $this->widget('zii.widgets.CDetailView', array(
        'data'=>$model,
        'attributes'=>array(
            'id',
            'username',
            array(
                'label'=>'ПІБ та посада',
                'type'=>'raw',
                'value'=>CHtml::link(CHtml::encode($model->info),"#",array(
                  'class' => "info",
                  'data-name'=> "info",
                  'data-pk' => $model->id,
                  'data-type' => "textarea"
                )),
            ),            
            array(
                'label'=>'Пароль',
                'type'=>'raw',
                'value'=>CHtml::link(CHtml::encode($model->password),"#",array(
                  'class' => "password",
                  'data-name'=> "password",
                  'data-pk' => $model->id,
                  'data-type' => "text"
                )),
            ),
            array(
                'label'=>'Контактні дані',
                'type'=>'raw',
                'value'=>CHtml::link(CHtml::encode($model->contacts),"#",array(
                  'class' => "contacts",
                  'data-name'=> "contacts",
                  'data-pk' => $model->id,
                  'data-type' => "text"
                )),
            ),
            array(
                'label'=>'Підрозділи',
                'type'=>'raw',
                'value'=>function($data){
                  $echo =  '<ul><li>';
                    $criteria = new CDbCriteria();
                    $criteria->with = array('_department_user_department');
                    $criteria->compare('_department_user_department.UserID',$data->id);
                    $criteria->together = true;
                    $arr_depts = array();
                    $depts = Departments::model()->findAll($criteria);
                    foreach ($depts as $dept){
                      $arr_depts[] = CHtml::encode($dept->DepartmentName);
                    }
                  $echo .= implode('</li><li>',$arr_depts).'</li></ul>';
                  return $echo;
                }
            ),
            array(
                'label'=>'Права і ролі',
                'type'=>'raw',
                'value'=>function($data){
                  $echo = '<ul>';
                    $criteria = new CDbCriteria();
                    $criteria->compare('userid',$data->id);
                    $criteria->together = true;
                    
                    $roles = Roleassignments::model()->findAll($criteria);
                    foreach ($roles as $role){
                      $echo .= '<li>'.CHtml::encode($role->itemname).'</li>';
                    }
                  $echo .= '</ul>';
                  return $echo;
                }
            )
        ),
        'htmlOptions' => array(
          'class' => 'detail-view',
          'style' => 'margin-left: 2%; margin-right: 2%; display: table; width: 96%;'
        )
    ));
  ?>
  </div><!-- dfbox -->
  </div><!-- bootstrap col -->
  </div><!-- row -->
</div>