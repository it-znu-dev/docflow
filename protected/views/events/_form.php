<?php
/* @var $model Events */
/* @var $this EventsController */
/* @var $header string */

?>

<script type="text/javascript" charset="utf-8">
    
  $(function(){
    var cur = new Date();
     mc = new MultiCalendar(
      'Events_DateSmartField',
      'calendar_preview_block',
      'calendar_msg_block',
      cur.getMonth()+1,
      cur.getFullYear(),
      2,
      "<?php echo Yii::app()->CreateUrl("events/ajaxcounters"); ?>"
    );

    var finish_time_func = function(){
      var str = $("#Events_FinishTime").val();
      var len = str.length;
      var tm;
      $("#Events_FinishTime").css('border-color','rgb(255, 0, 0)');
      if (!len){
        $("#Events_FinishTime").css('border-color','yellow');
        return false;
      }
      tm = str.match(/^\s*\d{1,2}:\d{1,2}(:\d{1,2})?\s*$/);
      if (tm && ($("#Events_StartTime").css('border-color') !== "rgb(255, 0, 0)") && $("#Events_StartTime").val().length){
        var c = new Date();
        var sDate = new Date(c.getFullYear()+'-'+MultiCalendar.prototype.padDigits(c.getMonth()+1,2)
                +'-'+MultiCalendar.prototype.padDigits(c.getDate(),2)+"T"+$("#Events_StartTime").val());
        var fDate = new Date(c.getFullYear()+'-'+MultiCalendar.prototype.padDigits(c.getMonth()+1,2)
                +'-'+MultiCalendar.prototype.padDigits(c.getDate(),2)+"T"+$("#Events_FinishTime").val());

        if (sDate <= fDate){
          $("#Events_FinishTime").css('border-color','rgb(0, 255, 0)');
        }
      }
    };
    
    var start_time_func = function(){
      var str = $("#Events_StartTime").val();
      var len = str.length;
      var tm;
      $("#Events_StartTime").css('border-color','rgb(255, 0, 0)');
      if (!len){
        $("#Events_StartTime").css('border-color','yellow');
        return false;
      }
      tm = str.match(/^\s*\d{1,2}:\d{1,2}(:\d{1,2})?\s*$/);
      if (tm && ($("#Events_FinishTime").css('border-color') !== "rgb(255, 0, 0)") || $("#Events_FinishTime").val().length){
        var c = new Date();
        var ft = ($("#Events_FinishTime").val().length)? $("#Events_FinishTime").val():"23:59:59";
        var sDate = new Date(c.getFullYear()+'-'+MultiCalendar.prototype.padDigits(c.getMonth()+1,2)
                +'-'+MultiCalendar.prototype.padDigits(c.getDate(),2)+"T"+$("#Events_StartTime").val());
        var fDate = new Date(c.getFullYear()+'-'+MultiCalendar.prototype.padDigits(c.getMonth()+1,2)
                +'-'+MultiCalendar.prototype.padDigits(c.getDate(),2)+"T"+ft);
        if (sDate <= fDate){
          $("#Events_StartTime").css('border-color','rgb(0, 255, 0)');
        }
      }
    };
    
    $("#Events_StartTime").keyup(start_time_func);
    $("#Events_StartTime").blur(start_time_func);
    $("#Events_StartTime").click(start_time_func);
    
    $("#Events_FinishTime").keyup(finish_time_func);
    $("#Events_FinishTime").blur(finish_time_func);
    $("#Events_FinishTime").click(finish_time_func);

    
    $("#events-form").submit(function(event){
      if (mc.counters[0] === 0){
        event.preventDefault();
      }
      DOC.ButtonDelay("submit__button");
      return ;
    });
  });

</script>

<?php
  //don't afraid, just test :)
  // today is 21.11.2014, Friday
  //setlocale(LC_ALL, 'Ukrainian');
  //echo iconv('windows-1251','utf-8',strftime("%B, %A : %d.%m.%Y",strtotime('Monday'))); //-> "Листопад, понеділок : 24.11.2014"
  $date_field_names = array(
  'StartYear',
  'StartMonth',
  'StartWeekDay',
  'StartDay',
  'StartHour',
  'StartMinute',
  'FinishYear',
  'FinishMonth',
  'FinishWeekDay',
  'FinishDay',
  'FinishHour',
  'FinishMinute'
  );
  $day_names = array(
    'Неділя',
    'Понеділок',
    'Вівторок',
    'Середа',
    'Четвер',
    'П\'ятниця',
    'Субота',
  );
  $day_names[-1] = "неважливо";
  /* @var $eventscreate_form CActiveForm */
  $eventscreate_form = $this->BeginWidget(
  'CActiveForm', array(
    'id' => 'events-form',
    'htmlOptions' => array(
      'enctype' => 'multipart/form-data',
    ),
    'action' => '',
    'enableAjaxValidation' => false,
   )
  );
?>
<div style="text-align: right !important;">
<?php
if ((Yii::app()->user->checkAccess('EventsAdmin') ||
  Yii::app()->user->checkAccess('EventsGeneral'))
  && !$model->isNewRecord
){
  
?>
  <a href= "<?php echo Yii::app()->CreateUrl('events/delete',array('id' => $model->idEvent)); ?>" 
     class="btn btn-danger btn-sm" 
     onclick="if(!confirm('Остаточно?')){return false;}">
    <span class="glyphicon glyphicon-trash"></span>
    Видалити
  </a>
<?php
}
?>
</div>

<div class='row row-nomargins'>
  <div class="dfbox">
    <div class='row row-nomargins'>
      <h1 class='dfmetaheader'><?php echo $header; ?></h1>
    </div>
    <?php echo $eventscreate_form->errorSummary($model); ?>
    <div class='row row-nomargins'>
      <div class="col-xs-12 col-sm-12 col-md-6">
        <div class='row row-nomargins'>
        <?php 
          echo $eventscreate_form->labelEx($model, 'EventName', array(
            'class' => 'col-xs-12 col-sm-12 dfheader',
          ));
        ?>
        </div>
        <div class='row row-nomargins'>
        <?php
          echo $eventscreate_form->textField($model,'EventName',array(
            'class' => 'col-xs-12 col-sm-12 form-control', 
          ));
        ?>
        </div>
        <div class="row row-nomargins">
          <div class="col-xs-12 col-sm-6">
          <?php
            echo $eventscreate_form->labelEx($model, 'KindID', array(
              'class' => 'col-xs-12 col-sm-12 dfheader',
            ));
            echo $eventscreate_form->dropDownList($model,'KindID',
              CHtml::listData(Eventkinds::model()->findAll(), 'idKind', 'KindName'),array(
              'class' => 'col-xs-12 col-sm-12 form-control'
            ));
          ?>
          </div>
          <div class="col-xs-12 col-sm-6">
          <?php
            echo $eventscreate_form->labelEx($model, 'LevelID', array(
              'class' => 'col-xs-12 col-sm-12 dfheader',
            ));
            echo $eventscreate_form->dropDownList($model,'LevelID',
              CHtml::listData(Eventlevels::model()->findAll(), 'idLevel', 'LevelName'),array(
              'class' => 'col-xs-12 col-sm-12 form-control'
            ));
          ?>
          </div>
        </div>
        <div class='row row-nomargins'>
        <?php 
          echo $eventscreate_form->labelEx($model, 'Place', array(
            'class' => 'col-xs-12 col-sm-12 dfheader', 'id'=>"PlaceLabel"
          ));
          echo $eventscreate_form->textArea($model,'Place',array(
            'class' => 'col-xs-12 col-sm-12 form-control', 
          ));
        ?>
        </div>
        <div class="row row-nomargins">
        <?php 
          echo $eventscreate_form->labelEx($model, 'EventDescription', array(
            'class' => 'col-xs-12 col-sm-12 dfheader',
          ));
          echo $eventscreate_form->textArea($model,'EventDescription',array(
            'class' => 'col-xs-12 col-sm-12 form-control', 'rows' => "4"
          ));
        ?>
        </div>
        <div class="row row-nomargins">
          <div class="col-xs-12 col-sm-8">
          <?php
            echo $eventscreate_form->labelEx($model, 'Responsible', array(
              'class' => 'col-xs-12 col-sm-12 dfheader',
            ));
            echo $eventscreate_form->textField($model,'Responsible',array(
              'class' => 'col-xs-12 col-sm-12 form-control', 
            ));
          ?>
          </div>
          <div class="col-xs-12 col-sm-4">
          <?php
            echo $eventscreate_form->labelEx($model, 'Contacts', array(
              'class' => 'col-xs-12 col-sm-12 dfheader',
            ));
            echo $eventscreate_form->textField($model,'Contacts',array(
              'class' => 'col-xs-12 col-sm-12 form-control', 
            ));
          ?>
          </div>
        </div>
      </div>
      
      <div class="col-xs-12 col-sm-12 col-md-6">
        <div class="row row-nomargins" id="DateRulesInfo" style="display: none;">
          Допустимі вирази для правила формування дат (приклади):
          <ul>
          <li>проміжок дат: 29.12.2014 - 18.01.2015 , 01.01.2015-07.01.2015</li>
          <li>проміжок у межах місяця: 19-29.12.2014 , 01-07.01.2015</li>
          <li>конкретна дата: 29.12.2014 , 01.01.2015</li>
          <li>один раз у рік: 29.12 , 01.01</li>
          <li>один раз у місяць: 29.10-12.2014 , 01.01-05.2015</li>
          <li>кожного тижня у проміжку: ср/29.12.2014-18.01.2015, сб/01.01.2015-01.02.2015</li>
          <li>всі допустимі вище вирази через кому</li>
          </ul>
        </div>
        <div class="row row-nomargins">
          <div class="col-xs-12 col-sm-6">
            <div class="row row-nomargins">
              <?php
                echo $eventscreate_form->labelEx($model, 'DateSmartField', array(
                  'class' => 'col-xs-12 col-sm-10 dfheader',
                ));
              ?>
              <span class="col-xs-12 col-sm-1 dfheader" id="DateSmartInfo" >
              <a href="#" class="glyphicon glyphicon-info-sign" 
                 onclick="$('#DateRulesInfo').slideToggle();return false;"></a></span>
            </div>
            <div class="row row-nomargins">
            <?php
              echo $eventscreate_form->textField($model,'DateSmartField', 
                array('class' => 'col-xs-12 col-sm-12 form-control'));
            ?>
            </div>
          </div>
          <div class="col-xs-12 col-sm-3">
            <div class="row row-nomargins">
              <?php
                echo $eventscreate_form->labelEx($model, 'StartTime', array(
                  'class' => 'col-xs-12 col-sm-12 dfheader',
                ));
              ?>
            </div>
            <div class="row row-nomargins">
            <?php
              echo $eventscreate_form->textField($model,'StartTime',
                  array( 'class' => 'col-xs-12 col-sm-12 form-control'));
            ?>
            </div>
          </div>
          <div class="col-xs-12 col-sm-3">
            <div class="row row-nomargins">
              <?php
                echo $eventscreate_form->labelEx($model, 'FinishTime', array(
                  'class' => 'col-xs-12 col-sm-12 dfheader',
                ));
              ?>
            </div>
            <div class="row row-nomargins">
            <?php
              echo $eventscreate_form->textField($model,'FinishTime',
                  array( 'class' => 'col-xs-12 col-sm-12 form-control'));
            ?>
            </div>
          </div>
        </div>
        
        <div class="row row-nomargins">
          <!-- CALENDAR PLACEHOLDER -->
          <div class="col-xs-12 col-sm-10" id="calendar_preview_block">
          </div>
          <div class="col-xs-12 col-sm-2" id="calendar_msg_block">
          </div>
        </div>
      
      </div>
    </div>
  </div>
    
  <div class="row row-nomargins dfbox">
    <div class="row row-nomargins">
      <div class="col-xs-12 col-sm-12 col-md-6">
        <?php
        $this->many2ManyPicker( 
          'Events[invited_ids]', 
          'InvitedComment', 
          Yii::app()->CreateUrl('events/getDepartments'),
          $model->_event_event_invited, 
          'DeptID',
          "Запрошені",
          Yii::app()->CreateUrl('deptgroups/x')
        );
        ?>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6">
        <?php
        $this->many2ManyPicker( 
          'Events[organizer_ids]', 
          'OrganizerComment', 
          Yii::app()->CreateUrl('events/getDepartments'),
          $model->_event_event_organizer,
          'DeptID',
          "Організатори",
          Yii::app()->CreateUrl('deptgroups/x')
        );
        ?>
      </div>
    </div>
    <hr/>
    <div class='row row-nomargins'>
      <div class="col-xs-12 col-sm-6">
        <?php
        if (!$model->FileID){
        ?>
        <div class="dfheader">Файл</div>
        <?php
        } else {
        ?>
        <div class="dfheader">
        <?php
        echo CHtml::link("Файл",Yii::app()->CreateUrl('/events/attachment',array('id' => $model->idEvent)));
        ?> прикріплено ,можна замінити 
        <?php
          if ($model->_event_file->UserID == Yii::app()->user->id){
            echo "чи ".CHtml::link("видалити",Yii::app()->CreateUrl('/events/attachmentrm',array('id' => $model->idEvent)),
              array("style" => "color: red;"));
          }
        ?> (необов`язково)
        </div>
        <?php
        }
        echo $eventscreate_form->fileField($model,"uploaded_file");
        ?>
      </div>
      <div class="col-xs-12 col-sm-6">
        <div class="row row-nomargins">
        <?php
          echo $eventscreate_form->checkbox($model,'_send_to_site')." відправити на сайт";
        ?>
        </div>
        <div class="row row-nomargins">
        <?php
          echo $eventscreate_form->checkbox($model,'_flow_to_invited')." розіслати запрошеним";
        ?>
        </div>
      </div>
      <div class='row row-nomargins' style="text-align: center;">
      <?php
      if ($model->isNewRecord){
        echo CHtml::submitButton('Створити', array(
          "class"=>"btn btn-large btn-primary",
          "id" => "submit__button"
        )); 
      } else {
        echo CHtml::submitButton('Зберегти', array(
          "class"=>"btn btn-large btn-primary",
          "id" => "submit__button"
        )); 
      }
      ?>
      </div>
    </div>
  </div>
<?php 
  $this->endWidget();
?>
</div>
<!--div class="badge badge-info">1</div>
<div class="badge badge-warning">1</div>
<div class="badge badge-important">1</div>
<div class="badge badge-inverse">1</div>
<div class="badge badge-red">1</div-->
