<?php
/* @var $model Events */
/* @var $response string */
/* @var $this EventsController */
?>
<style>

h1 {
  text-align: center;
  font-family: Verdana;
  font-weight: normal;
  font-size: 20pt;
  margin-bottom: 0px;
  
}
.labelcontent {
  color: yellow;
}
.eventdetails {
  text-align: left;
  padding-top: 3px;
  padding-bottom: 3px;
}
.eventdescription{

}
.margin5{
  margin-left: 5px;
  margin-bottom: 5px;
  font-size: 10pt;
  display: inline-block;
}
.eventtypetext{
  color: blue;
}
.eventkindtext{
  color: black;
}
</style>

<div class="row row-nomargin">
<div class="col-xs-12">
<div class="dfbox">


<div class="row row-nomargin">
  <div class="col-xs-12">
  <div class="dfbox">
  <?php
    if ($model->ExternalID && strlen($model->NewsUrl) > 0){
        echo '<a href="'.$model->NewsUrl.'">Також є на сайті ЗНУ.</a>';
    } else {
      if ($response){
        echo $response;
      } else {
        echo "Немає на сайті ЗНУ.";
      }
    }
    ?>
  </div>
  </div>
  <?php
  $eflows = EventFlow::model()->findAll('EventID='.$model->idEvent);
  if (( count($eflows) > 0 && is_array($eflows) ) &&
      ( implode(",",$model->_event_user->department_ids) ==
        implode(",",Users::model()->findByPk(Yii::app()->user->id)->department_ids)
        || Yii::app()->user->checkAccess("_EventsAdmin")) ){
    ?>
    <div class="col-xs-12">
    <div class="dfbox">
    <?php
        echo '<a href="'
          .Yii::app()->CreateUrl('flows/index',array('Flows[idFlow]' => $eflows[0]->FlowID))
          .'">Розсилка запрошеним через документообіг.</a>';
    ?>
    </div>
    </div>
    <?php
  }
  ?>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="dfbox">
    <?php
    if (Yii::app()->user->checkAccess('_EventsAdmin') || (
      implode(",",$model->_event_user->department_ids) ==
      implode(",",Users::model()->findByPk(Yii::app()->user->id)->department_ids)
      && Yii::app()->user->checkAccess('_EventsGeneral')
    )){
      ?>
      &bullet;
      &bullet;
      <a 
        href="<?php echo Yii::app()->CreateUrl('events/update',array('id' => $model->idEvent)) ?>"
        class="btn btn-xs btn-primary"
      >
        <span class="glyphicon glyphicon-pencil"></span>
        Редагувати
      </a>
      <?php
    }
    ?>
    &bullet;
    &bullet;
    <a 
      href="<?php echo Yii::app()->CreateUrl('events/admin') ?>"
      class="btn btn-xs btn-primary"
    >
      <span class="glyphicon glyphicon-eye-open"></span>
      Перегляд заходів
    </a>
    &bullet;
    &bullet;
    <?php
    if (Yii::app()->user->checkAccess('_EventsAdmin')){
    ?>
    <a 
      href="<?php echo Yii::app()->CreateUrl('events/delete',array("id" => $model->idEvent)) ?>"
      class="btn btn-xs btn-danger"
    >
      <span class="glyphicon glyphicon-trash"></span>
      Видалити
    </a>
    <?php
    }
    ?>
    </div>
  </div>
</div>
<div class='row row-nomargin'>
  <div class="col-xs-12 col-md-12">
    <div class="dfbox">
      <h1 class='col-xs-12 col-md-12 dfmetaheader' style="margin:0px;margin-bottom: 10px;">
        <?php echo (!(strpos($model->EventName,'<') === false || strpos($model->EventName,'>') ===false))? 
        htmlspecialchars($model->EventName):$model->EventName; ?>
      </h1>
      <span class="label label-info margin5"> Місце: 
        <span class="labelcontent">
        <?php 
        if (is_null($model->Place) || (trim($model->Place) === "")){
          echo "не вказано";
        } else{
          echo (!(strpos($model->Place,'<') === false 
                  || strpos($model->Place,'>') ===false))? 
            htmlspecialchars($model->Place)
          : $model->Place; 
        }
        ?>
        </span>
      </span>
      <span class="label label-info margin5"> Дата і час: 
        <span class="labelcontent">
        <?php 
        
        echo preg_replace("/,(\d\d?)(,|$)/i",",$1 числа кожного місяця$2",
          str_replace($this->wdays,$this->wday_alias,  mb_strtolower($model->DateSmartField,'utf8')))
          . " ".(($model->StartTime)? mb_substr($model->StartTime,0,5,"utf-8"): "(час початку не вказано)")
          .(($model->FinishTime)? " - ".mb_substr($model->FinishTime,0,5,"utf-8"): ""); 
        ?>
        </span>
      </span>
      <span
        class="label label-<?php echo $model->_event_eventkind->KindStyle; ?> margin5">
        <?php echo $model->_event_eventkind->KindName; ?>
      </span>
      <span
        class="label label-<?php echo $model->_event_eventlevel->LevelStyle; ?> margin5">
        <?php echo $model->_event_eventlevel->LevelName; ?>
      </span>
  </div>
  </div>
</div>

<div class="row row-nomargin">
  <div class="col-xs-12 col-md-12">
  <div class="dfbox">
    <div class="col-xs-12 col-md-12">
      Опис заходу
    </div>
    <div class="col-xs-12 col-md-12">
    <div class="dfbox">
    <?php
      echo (!(strpos($model->EventDescription,'<') === false || strpos($model->EventDescription,'>') ===false))? 
        htmlspecialchars($model->EventDescription)
        :((strlen(trim($model->EventDescription))>0)? 
          $model->EventDescription:"<i>немає</i>");
    ?>
    </div>
    </div>
    
    <div class="row row-nomargin">
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="col-xs-12 col-md-12">
          Прикріплений файл
        </div>
        <div class="col-xs-12 col-md-12">
          <div class="dfbox">
          <?php
          if ($model->FileID){
            $_image = $this->embedImageFromAttachment($model->idEvent);
            if ($_image !== false){
              ?>
              <img src="<?php echo $_image; ?>" alt="зображення" style="max-width: 100%;"/>
              <?php
            } else {
              echo CHtml::link("[завантажити]",
                Yii::app()->CreateUrl('events/attachment',
                  array('id' => $model->idEvent)));
            }
          } else {
            echo "<i>відсутній</i>";
          }
          ?>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="col-xs-12 col-md-12">
          Контактні дані
        </div>
        <div class="col-xs-12 col-md-12">
          <div class="dfbox">
          <?php
          echo (!(strpos($model->Contacts,'<') === false || strpos($model->Contacts,'>') ===false))? 
            htmlspecialchars($model->Contacts)
            :((strlen(trim($model->Contacts))>0)? 
              $model->Contacts:"<i>немає</i>");
          ?>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="col-xs-12 col-md-12">
          Відповідальні особи
        </div>
        <div class="col-xs-12 col-md-12">
        <div class="dfbox">
        <?php
          echo (!(strpos($model->Responsible,'<') === false || strpos($model->Responsible,'>') ===false))? 
            htmlspecialchars($model->Responsible)
            :((strlen(trim($model->Responsible))>0)? 
              $model->Responsible:"<i>немає</i>");
        ?>
        </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>

<div class="row row-nomargin">
  <div class="col-xs-12 col-md-12">
  <div class="dfbox">
    <div class="col-xs-12 col-sm-6 col-md-6">
      <div class="col-xs-12 col-md-12">
        Запрошені
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="dfbox">
        <?php
          $ilist = $model->invitedHtmlList();
          echo (($ilist == "<ul></ul>")?
            "<i>немає</i>" : $ilist
          );
        ?>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
      <div class="col-xs-12 col-md-12">
        Організатори
      </div>
      <div class="col-xs-12 col-md-12">
        <div class="dfbox">
        <?php
          $olist = $model->organizerHtmlList();
          echo (($olist == "<ul></ul>")?
            "<i>немає</i>" : $olist
          );
        ?>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>

</div>
</div>
</div>