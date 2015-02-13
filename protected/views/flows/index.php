<?php
/* @var $this FlowsController */
/* @var $model Flows */
$this->pageTitle=Yii::app()->name;
$controller = $this;
?>
<script type="text/javascript">
  function beforeUpdate(){
    var fields = [
      {field: 'FlowName', 
        title: '<?php echo $model->getAttributeLabel('FlowName'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('flows/xupdate'); ?>'},
      <?php if(Yii::app()->user->checkAccess('_FlowsAdmin')){ ?>
        {field: 'Created', 
          title: '<?php echo $model->getAttributeLabel('Created'); ?>', 
          xupdate_url: '<?php echo Yii::app()->CreateUrl('flows/xupdate'); ?>'},
      <?php } ?>
      {field: 'FlowDescription', 
        title: '<?php echo $model->getAttributeLabel('FlowDescription'); ?>', 
        xupdate_url: '<?php echo Yii::app()->CreateUrl('flows/xupdate'); ?>'}
    ];
    for (var i = 0; i < fields.length; i++){
      var f = fields[i];
      $('.'+f.field).editable({
          url: f.xupdate_url,
          title: f.title,
          //mode: 'inline',
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
          editable.input.$input.attr('class','form-control input-sm area-sm');
          if (editable.input.$input.attr('rows')){
            editable.input.$input.attr('rows',3);
          }
      });
      
      $('.'+f.field).on('save', function(e, params) {
        $('#flow-grid').yiiGridView('update');
      });
    }
    
    $('a.identifier').click(function(){
      $('#control-actions-'+$(this).attr('id')).slideToggle();
      return false;
    });
    
    $('button.btn.btn-danger.btn-sm.btn-del, span.glyphicon.glyphicon-trash.btn-del').click(function(){
      var delete_url = $(this).attr('data-link');
      var delete_id = $(this).attr('data-pk');
      if (confirm('Остаточно?')){
        $.ajax({
          type: 'POST',
          url: delete_url,
          data: {id: delete_id},
          success: function(data){
            $('#flow-grid').yiiGridView('update');
          }
        });
      }
      return false;
    });
    
    $('a.answer').click(function(){
      var _url = $(this).attr('data-url');
      if (_url.length === 0){
        return false;
      }
      var _id = $(this).attr('data-pk');
      var _answer_id = $(this).attr('answer-text-id');
      var _user_id = $(this).attr('data-user');
      var $_this = $(this);
      //if (confirm('Остаточно?')){
        $.ajax({
          type: 'POST',
          url: _url,
          dataType: 'json',
          data: {
            idFlow: _id, 
            AnswerText: $("#"+_answer_id).val(),
            UserID: _user_id
          },
          success: function(response){
            if (response.status === "error"){
              alert(response.msg);
            }
            $('#flow-grid').yiiGridView('update');
          },
          error: function(x,e){
            alert(e);
          },
          fail: function(x,e){
            alert(e);
          },
          beforeSend: function(){
            $_this.attr('data-url',"");
            $_this.html("Зачекайте...");
          }
        });
      //}
      return false;
    });
    
    
    $("a[rel^='prettyPhoto']").prettyPhoto({
      allow_resize: true, 
      default_height: '95%', 
      default_width: '95%', 
      social_tools: ""
      });
    
    $('[data-toggle="tooltip"]').tooltip();
    
    /**
     * Мигання усіх елементів класу class_name
     * @param {String} class_name
     * @returns {undefined}
     */
    blinking = function(class_name){
      _class = '.' + class_name;
        $.fn.wait = function(time, type){
          time = time || 10;
          type = type || "fx";
          return this.queue(type, function(){
            var self = this;
            setTimeout(function(){
              $(self).dequeue();
            }, time);
          });
        };
        function runIt() {
          $(_class).wait()
            .animate({"opacity": 0.1},1200)
            .wait()
            .animate({"opacity": 1},300,runIt);
        }
        runIt();
    };
    
    blinking("blinking");
  }

  $(function(){
    beforeUpdate();
  });
</script>

<div class="dfbox">
  <h2 class="centered">
  Розсилки </h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "flow-grid",
    'dataProvider' => $model->search(),
    'filter' => $model,
    'template' => '<div class="documents_pager col-xs-6">{pager}</div>'
    . '<div class="col-xs-6 right-side">{summary}</div><br/>'
    . '{items}'
    . '<div class="documents_pager">{pager}</div>',
    'afterAjaxUpdate' => "function(id,data){
      beforeUpdate();
    }",
    'columns' => array(
      array(
        'name' => 'idFlow',
        'header' => 'ID',
        'type' => 'raw',
        'filter' => "<div class='form group'>"
          ."<input type='text' name='Flows[idFlow]' value='".$model->idFlow."' 
            class='filter-field almost-full-width' placeholder='ID'/>
         </div>",
        'value' => function ($data){
          /* @var $data Flows */
          ?>
          <a href="#" id="<?php echo CHtml::encode($data->idFlow); ?>" class="identifier">
          <?php echo CHtml::encode($data->idFlow); ?>
          </a>
          <div class="control-actions" id="control-actions-<?php echo CHtml::encode($data->idFlow); ?>">
            <?php if(count($data->document_ids) == 1 
                && !empty($data->_flow_documents[0]->_document_files) 
                && $data->_flow_documents[0]->_document_files[0]->exists){ ?>
              <a href="<?php echo Yii::app()->CreateUrl("files/download",array('id'=>
                  $data->_flow_documents[0]->_document_files[0]->idFile)); ?>" 
                class="btn btn-default btn-sm btn-bottom5" title="Завантажити останній файл документа">
                <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
              </a>
            <?php } ?>
            
            <?php if(!(implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) !=
                  implode(',',$data->_flow_user->department_ids) &&
                (!Yii::app()->user->checkAccess('_FlowsAdmin')))){ ?>
            <a href="<?php echo Yii::app()->CreateUrl("flows/update",array('id'=>$data->idFlow)); ?>" 
              class="btn btn-primary btn-sm btn-bottom5">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <?php } ?>
            <?php if(count($data->document_ids) > 0 && !Yii::app()->user->checkAccess('_FlowsAdmin')){ ?>
            <a href="<?php echo Yii::app()->CreateUrl("flows/create",array('idFlow'=>$data->idFlow)); ?>" 
              class="btn btn-default btn-sm btn-bottom5" title="переслати">
              <span class="glyphicon glyphicon-random" aria-hidden="true"></span>
            </a>
            <?php } ?>
            <?php if(!(implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) !=
                  implode(',',$data->_flow_user->department_ids) &&
                (!Yii::app()->user->checkAccess('_FlowsAdmin')))){ ?>
            <button type="button" class="btn btn-danger btn-sm btn-del btn-separated"
              data-link="<?php echo Yii::app()->CreateUrl("flows/delete"); ?>" 
              data-pk="<?php echo CHtml::encode($data->idFlow); ?>">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
            <?php } ?>
          </div>
          <?php
         },
      ),
      array(
        'header' => 'розсилки',
        'type' => 'raw',
        'filter' => "<div class='form group'>"
        ."<input type='text' name='Flows[DocumentInfo]' value='".$model->DocumentInfo."' 
            class='filter-field' placeholder='Зміст або індекси документа'/>
          <input type='text' name='Flows[Created]' value='".$model->Created."' 
            class='filter-field' placeholder='Створено'/>
          <input type='text' name='Flows[UserInfo]' value='".$model->UserInfo."'  
            class='filter-field' placeholder='Користувач'/>
          <input type='text' name='Flows[Respondent]' value='".$model->Respondent."'  
            class='filter-field' placeholder='Респондент'/>"
        .CHtml::dropDownList('Flows[mode]', ((strlen($model->mode) > 0)? $model->mode:"in"),
          array('in' => 'вхідні', 'from' => 'вихідні', 'without_answer' => "без відповіді"),
          array('class'=>'ModeFilter'))
        ."</div>",
        'value' => function($data) use ($controller){
          $controller->renderPartial('_item',array('controller' => $controller, 'model' => $data));
        }
      ),
    ),
    'htmlOptions' => array(
      'class' => "flows"
    )
  ));
  ?>
</div>
<?php

?>