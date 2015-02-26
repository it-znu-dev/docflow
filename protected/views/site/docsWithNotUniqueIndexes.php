<?php
/* @var $data CActiveDataProvider*/
/* @var $model Documents*/

?>
<script type="text/javascript">
  function beforeUpdate(){
    $(function(){
      var cval = "";
      var label_classes = ["danger","default"];
      var label_index = 0;
      $("h3 span.label").each(function( index, elem ) {
        if ($(elem).text() !== cval){
          label_index = ((++label_index) % label_classes.length);
          cval = $(elem).text();
        }
        $(elem).addClass("label-"+label_classes[label_index]);
      });
    });
    
  }
  $(function(){
    beforeUpdate();
  });
</script>
<div class="dfbox">
  <h2 class="centered">
  Документи з не унікальними індексами </h2>
  <?php
  $this->widget('zii.widgets.grid.CGridView', array(
    'id' => "no-uniq-docs-grid",
    'dataProvider' => $data,
    'filter' => $model,
    'afterAjaxUpdate' => "function(id,data){
      beforeUpdate();
    }",
    'template' => '<div class="documents_pager col-xs-6">{pager}</div>'
    . '<div class="col-xs-6 right-side">{summary}</div><br/>'
    . '{items}'
    . '<div class="documents_pager">{pager}</div>',
    'columns' => array(
      array(
        'name' => 'idDocument',
        'header' => 'ID',
        'type' => 'raw',
        'value' => '"<a href=\"".Yii::app()->CreateUrl("documents/update"'
          .',array("id"=>$data->idDocument))."\">[редагувати]</a>"'
      ),
      array(
        'name' => 'SubmissionIndex',
        'header' => "Індекс",
        'type' => "raw",
        'value' => '"<h3><span class=\'label\'>".'
        .'$data->SubmissionIndex."</span></h3>"'
      ),
      array(
        'name' => 'CategoryID',
        'header' => 'Категорія',
        'value' => '$data->_document_doccategory->CategoryName . " " . $data->_document_doccategory->CategoryCode',
      ),
      array(
        'name' => 'SubmissionDate',
        'value' => 'date("d.m.Y",strtotime($data->SubmissionDate))',
      ),
      array(
        'name' => 'Summary',
      )
    ),
    'htmlOptions' => array(
      'class' => "docs"
    )
  ));
  ?>
</div>
<?php

?>