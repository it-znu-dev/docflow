<?php
  /* @var $model Flows */
  /* @var $controller FlowsController */
  ?>

<?php
  if (empty($model->_flow_user)){
  ?>
    <div class="alert alert-danger" role="alert">Помилка: відсутній власник розсилки</div>
  <?php
  return false;
  }
?>
    
<div class="dfbox" id="<?php echo $model->idFlow; ?>_flow">
  <div class="row dfmetaheader">
    <h2>
      <?php $controller->echoInfoContainer($model,'FlowName',$model->idFlow,'textarea',$model->FlowName); ?>
    </h2>
    <div class="col-xs-6 doc-info-container">
      Деталі розсилки:
      <p title="<?php echo $model->_flow_user->info . '(' . $model->_flow_user->contacts . ")"; ?>" >
      <?php $controller->echoInfoContainer($model,'FlowDescription',$model->idFlow,'textarea',$model->FlowDescription); ?>
      </p>
    </div>
    <div class="col-xs-6 doc-info-container"><ul>
      <li>
      Створено користувачем:
      <u title="<?php echo $model->_flow_user->info . '(' . $model->_flow_user->contacts . ")"; ?>" >
      <?php echo $model->_flow_user->username; ?>
      </u>
      </li><li>
      <?php $controller->echoInfoContainer($model,'Created',$model->idFlow,'text',
        $model->Created); ?>
      </li></ul>
    </div>

  </div>
  
  
    <?php 
    $answer = $controller->getAnswer($model->idFlow, Yii::app()->user->id);
    if($answer != null){ ?>
      <div class="row dfmetaheader left-side">
        <span class="glyphicon glyphicon-ok"></span>
        Відповідь надано
        <?php echo date('d.m.Y H:i',strtotime($answer->Created)); ?>
        користувачем
        <u title="<?php echo $answer->_answer_user->info . '(' . $answer->_answer_user->contacts . ")"; ?>" >
        <?php echo $answer->_answer_user->username; ?>
        </u>
      </div>
    <?php } ?>
    <?php 
    if($answer == null && $model->mode != 'from' && !Yii::app()->user->checkAccess('_FlowsAdmin')){ ?>
      <div class="row dfmetaheader centered">
        
        <table border="0" style="margin: 0px auto;">
          <tr>
            <td colspan="2">необхідно надати підтвердження</td>
          </tr>
          <tr>
            <td style="padding: 5px;"><textarea rows="2" 
                    id="<?php echo $model->idFlow; ?>-answer" 
                    placeholder="коментар"></textarea>
            </td>
            <td style="padding: 5px;">
              <a href="#" data-pk="<?php echo $model->idFlow; ?>" 
               data-url="<?php echo Yii::app()->CreateUrl("flows/answer"); ?>"
               answer-text-id="<?php echo $model->idFlow; ?>-answer"
               data-user="<?php echo Yii::app()->user->id; ?>"
               class="btn btn-danger btn-large answer"
              >
                <span class="glyphicon glyphicon-bell blinking"></span>
                інформувати
              </a>
            </td>
          </tr>
        </table>
      </div>
    <?php } ?>
  
  <div class="row row-nomargins">
    <div class="col-xs-12 col-md-7">
      
      <?php if (count($model->document_ids) > 0){ ?>
      <div class="dfblock resp-box autoheight">
        <legend><?php if(count($model->document_ids) == 1 
              && !empty($model->_flow_documents[0]->_document_files) 
              && $model->_flow_documents[0]->_document_files[0]->exists){ ?>
            <a href="<?php echo Yii::app()->CreateUrl("files/download",array('id'=>
                $model->_flow_documents[0]->_document_files[0]->idFile)); ?>" 
              target="_blank" title="Завантажити останній файл документа">
              (завантажити)
            </a>
          <?php } 
          ?>Документи</legend>
        <?php 
        foreach ($model->_flow_documents as $doc){
        ?>
        <div class="col-xs-12">
          <div class="dfblock autoheight">
            <legend><?php 
            echo CHtml::link($doc->DocumentName . 
              ((!empty($doc->_document_submit))? 
                " (".$doc->_document_submit[0]->SubmissionInfo.")" 
                : ""),
              Yii::app()->CreateUrl("documents/index",array("Documents[idDocument]"=>$doc->idDocument)),array(
                'target' => '_blank'
              )
            ). ((strlen(trim($doc->ControlMark))>0)? "<div style='color:red;'> #контроль: ".$doc->ControlMark."</div>" : ""
            ). ((strlen(trim($doc->DoneMark))>0)? "<div style='color:green;'> #виконання: ".$doc->DoneMark."</div>" : "");
            ?></legend>
            <div class="col-xs-12 col-md-8">
              <div class="dfblock autoheight"><?php 
                echo $doc->Summary;
               ?></div><!-- // Document Summary block -->
            </div><!-- // Document Summary bootstrap-column block -->
            
            <div class="col-xs-12 col-md-4">
              <div class="dfblock autoheight"><?php 
                $i = 0;
                foreach($doc->doc_visible_files as $dfile){ ?> 
                  <div>
                  <?php if ($dfile->exists){
                    $owner = (($dfile->_file_user)? $dfile->_file_user->username : "відсутній власник!");
                  ?>
                    <a href="<?php 
                      $params = array();
                      $mime = CFileHelper::getMimeTypeByExtension($dfile->folder . $dfile->FileLocation);
                      $name = pathinfo($dfile->folder . $dfile->FileLocation, PATHINFO_EXTENSION) . ' -файл';
                      $title = "Завантажено ".date('d.m.Y H:i',strtotime($dfile->Created))." (".$owner.")";
                      $params['id'] = $dfile->idFile;
                      if ((stristr($mime,'pdf') !== false) || (stristr($mime,'image') !== false)){
                        $params['iframe'] = "true";
                      }
                      echo Yii::app()->CreateUrl("files/download",$params); 
                      ?>" title="<?php echo $title; 
                      ?>" class="btn btn-sm btn-default btn-margin"
                      <?php if (isset($params['iframe']) && $params['iframe'] == "true"){ 
                      ?> rel="prettyPhoto[iframes]" 
                      <?php } 
                      ?> data-mime="<?php echo $mime; ?>"><?php echo $name; 
                        ?> <span class="glyphicon glyphicon-<?php 
                          if (isset($params['iframe']) && $params['iframe'] == "true"){ 
                            echo "eye-open";
                          } else {
                            echo "save";
                          }
                          ?>" aria-hidden="true">
                        </span></a>
                  <?php } else { ?>
                    <button type="button" class="btn btn-sm btn-warning btn-margin">
                      <span class="glyphicon glyphicon-remove-sign" title="файл не знайдено"></span>
                    </button>
                  <?php } ?>
                  </div>
                <?php $i++; 
                } ?>
                
              </div><!-- // file block -->
            </div><!-- // file bootstrap-column block -->
          </div><!-- // document block -->
        </div><!-- // document bootstrap-column block -->
        
        <hr/>    
            
        <?php
        }
        ?>
      </div><!-- // all docs block -->
      <?php } ?>
      
      <?php if (count($model->event_ids) > 0){ ?>
      <div class="dfblock resp-box autoheight">
        <legend>
          Заходи
        </legend>
        <?php 
        foreach ($model->_flow_events as $ev){
        ?>
        <div class="col-xs-12">
          <div class="dfblock autoheight">
            <legend><?php 
            echo CHtml::link($ev->EventName . 
                " (".$ev->datetime_rule.")"."<span class='glyphicon glyphicon-share'></span>",
              Yii::app()->CreateUrl("events/index",array("id"=>$ev->idEvent)),array(
                'target' => '_blank'
              )); ?>
            </legend>
            <div class="col-xs-12 col-md-8">
              <div class="dfblock autoheight">
              <?php echo 'Місце: '.((strlen($ev->Place)>0)? CHtml::encode($ev->Place):"<i>не вказано</i>") ?>
              </div><!-- // Event Place block -->
            </div><!-- // Event Place bootstrap-column block -->
            
            <div class="col-xs-12 col-md-4">
              <div class="dfblock autoheight">
                <?php 
                  echo (($ev->remaining_time == "подія вже відбулась")? 
                    "<i>".$ev->remaining_time."</i>":'До події '.$ev->remaining_time);
                ?>
              </div><!-- // rest block -->
            </div><!-- // rest bootstrap-column block -->
          </div><!-- // event block -->
        </div><!-- // event bootstrap-column block -->
        
        <hr/>    
            
        <?php
        }
        ?>
      </div><!-- // all events block -->
      <?php } ?>
      
    </div><!-- // all docs or events bootstrap-column block -->
    
    <div class="col-xs-12 col-md-5">
      <div class="dfblock resp-box autoheight">
        <legend>Респонденти</legend>
        <div class="flow-block">
        <?php
        $flow_resps = FlowRespondent::model()->findAllByAttributes(array(
          'FlowID' => $model->idFlow
        ));
        foreach($flow_resps as $flow_resp){
          if ($flow_resp->_flow_respondent_department){
          ?>
          <div class="resp resp-<?php echo ($flow_resp->_flow_respondent_answer)? "green":"brown"; ?>"
            data-toggle="tooltip" data-placement="bottom"
            title="<?php echo ($flow_resp->_flow_respondent_answer 
              && $flow_resp->_flow_respondent_answer->_answer_user)? 
              ("Надано користувачем: ".$flow_resp->_flow_respondent_answer->_answer_user->info
              ." /".$flow_resp->_flow_respondent_answer->_answer_user->contacts."/"
              ." ".date("d.m.Y H:i",strtotime($flow_resp->_flow_respondent_answer->Created))
              .((strlen(trim($flow_resp->_flow_respondent_answer->AnswerText)) > 0)? 
                " (коментар: ".trim($flow_resp->_flow_respondent_answer->AnswerText).")" 
                : ""))
              : ""; ?>">
            <span class="glyphicon glyphicon-<?php 
              echo ($flow_resp->_flow_respondent_answer)? "ok":"ban"; ?>-circle">
            </span>
              <?php if(Yii::app()->user->checkAccess('_FlowsAdmin') &&
                $flow_resp->_flow_respondent_answer ){ ?>
              <span class="glyphicon glyphicon-trash btn-del" style="cursor: pointer; color: red;"
                      data-pk="<?php echo $flow_resp->_flow_respondent_answer->idAnswer; ?>"
                      data-link="<?php echo Yii::app()->CreateUrl("flows/delanswer"); ?>">
              </span>
              <?php } ?>
            <?php 
              echo $flow_resp->_flow_respondent_department->DepartmentName; 
            ?>
            <?php
          } ?>
          </div>
        <?php } ?>
        </div>
      </div>
    </div>
    
  </div>
  
</div>