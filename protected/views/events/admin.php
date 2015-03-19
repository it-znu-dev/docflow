<script type="text/javascript" charset="utf-8">
  $(function(){
    var cur = new Date();
     mc = new MultiCalendar(
      'calendarik_in',
      'calendarik',
      'calendarik_out',
      cur.getMonth()+1,
      cur.getFullYear(),
      6,
      "<?php echo Yii::app()->CreateUrl("events/ajaxcounters"); ?>"
    );
   });
</script>
<div class="row row-nomargins" style="text-align: center;">
  <div class="dfbox col-xs-12" id="calendarik">
  
  </div>
  <div style="display:none;" id="calendarik_out">
    
  </div>
  <input type="hidden" id="calendarik_in" />
</div>
<div class="dfbox">
<?php if (isset($_GET['Events']['date_search'])){ ?>
<h2 class="dfbox centered">
Показ заходів на <?php echo date("d.m.Y",strtotime($_GET['Events']['date_search'])); ?>
</h2>
<?php } ?>
<?php 
/* @var $model Events */
/* @var $controller EventsController */
$controller = $this;
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'events-grid',
    'template' => '<div class="documents_pager col-xs-9">{pager}</div>'
    . '<div class="col-xs-3 right-side">{summary}</div><br/>'
    . '{items}'
    . '<div class="documents_pager">{pager}</div>',
    'dataProvider'=>$model->search(),
    'afterAjaxUpdate' => "function(id,data){
      pagin_locale(); $('h2.dfbox').remove();
    }",
    'filter'=>$model,
    'columns'=>array(
      array(
        'header' => '#',
        'name' => 'past',
        'filter' => array(-1 => 'усі', 0 => "буде",1 => "було",2 => "зараз"),
        'headerHtmlOptions' => array(
          'style' => 'font-size: 9pt; width: 50px;'
        ),
        'filterHtmlOptions' => array(
          'style' => 'font-size: 9pt; width: 50px;'
        ),
        'htmlOptions' => array(
          'style' => 'width: 50px;'
        ),
        "value" => function ($data){
          if($data->past == -1){
            echo "";
          }
          if($data->past == 0){
            echo "<span class='label label-success'>буде</span>";
          }
          if($data->past == 1){
            echo "<span class='label label-red'>було</span>";
          }
          if($data->past == 2){
            echo "<span class='label label-warning'>зараз</span>";
          }
        },
        'type' => 'raw'
      ),
      array(
        'header' => 'Дата/час',
        'headerHtmlOptions' => array(
          'style' => 'font-size: 9pt;'
        ),
        'value' => 'date("d.m.Y",strtotime($data->EventDate))'
          . '.(($data->_event_date_event->StartTime)? " ".mb_substr($data->_event_date_event->StartTime,0,5,"utf-8"):"")',
        'type' => 'raw',
      ),
      array(
        'header' => 'Назва заходу',
        'headerHtmlOptions' => array(
          'style' => 'font-size: 9pt;'
        ),
        'name' => 'EventName',
        'value' => 'CHtml::link((!(strpos($data->_event_date_event->EventName,"<") === false' 
          .'|| strpos($data->_event_date_event->EventName,">") ===false))? '
          .'htmlspecialchars($data->_event_date_event->EventName) : $data->_event_date_event->EventName,'
          .'Yii::app()->CreateUrl("events/index",'
              .'array("id"=>$data->_event_date_event->idEvent)),'
            .'array("title"=>"Створив користувач ".$data->_event_date_event->_event_user->info))'
          .'.(($data->_event_date_event->NewsUrl)?'
          .'CHtml::link("<i class=\'icon-share\'></i>",$data->_event_date_event->NewsUrl):"")',
        'type' => 'raw'
      ),
      array(
        'header' => 'Місце',
        'headerHtmlOptions' => array(
          'style' => 'font-size: 9pt;'
        ),
        'name' => 'Place',
        'value' => '(empty($data->_event_date_event->Place))? 
          "<span class=\'label\'>Не вказано</span>"
          : (!(strpos($data->_event_date_event->Place,"<") === false '
        .'|| strpos($data->_event_date_event->Place,">") ===false))? 
        htmlspecialchars($data->_event_date_event->Place):$data->_event_date_event->Place',
        'type' => 'raw'
      ),
      array(
        'filter' => false,
        'headerHtmlOptions' => array(
          'style' => 'font-size: 9pt; width: 185px;'
        ),
        'filterHtmlOptions' => array(
          'style' => 'font-size: 9pt; width: 185px;'
        ),
        'header' => 'Вид і рівень заходу',
        'value' => '"<span class=\'label label-".$data->_event_date_event->_event_eventkind->KindStyle."\' '
          . 'style=\'margin-left: 2px; margin-top: 2px; display: block;\'>"'
          . '.$data->_event_date_event->_event_eventkind->KindName."</span>".'
          .'"<span class=\'label label-".$data->_event_date_event->_event_eventlevel->LevelStyle."\' '
          . 'style=\'margin-left: 2px; margin-top: 2px; display: block;\'>"'
          . '.$data->_event_date_event->_event_eventlevel->LevelName."</span>"',
        'type' => 'raw',
      ),
      array(
        'header'=>'дії',
        //'deleteButtonUrl' => 'Yii::app()->CreateUrl("events/eventdatedelete",array("id"=>$data->idEventDate))',
        'value' => function ($data) use ($controller){
          /* @var $data EventDate */
          $u = Users::model()->findByPk(Yii::app()->user->id);
          if (Yii::app()->user->checkAccess('_EventsAdmin') 
            || (((Yii::app()->user->checkAccess('_EventsGeneral')) &&
                implode(",",$u->department_ids ) == 
                implode(",",$data->_event_date_event->_event_user->department_ids) 
               ))
          ){
            echo CHtml::link('<i class="glyphicon glyphicon-pencil"></i>',
              Yii::app()->CreateUrl("events/update",array("id"=>$data->EventID)));
          }
        },
        'filter' => '',
      ),
    ),
  )
); 

?>
</div>
<div class="row row-nomargins" >
<div class="dfbox">
Види заходів:
<?php
foreach (Eventkinds::model()->findAll() as $ek){
  echo '&bullet;'
    . '<span style="display: inline-block;" class="label label-'
      . $ek->KindStyle.'">'
    . $ek->KindName
    . '</span>'
    . '&bullet;';
}
?>
</div>
</div>
<div class="row row-nomargins">
<div class="dfbox">
Рівні заходів:
<?php
foreach (Eventlevels::model()->findAll() as $et){
  echo '&bullet;'
    . '<span style="display: inline-block;" class="label label-'
      . $et->LevelStyle.'">'
    .$et->LevelName
    .'</span>'
    . '&bullet;';
}
?>
</div>
</div>