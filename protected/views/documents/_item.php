<?php
  /* @var $model Documents */
  /* @var $controller DocumentsController */
  ?>

<?php
  if (empty($model->_document_user)){
  ?>
    <div class="alert alert-danger" role="alert">Помилка: відсутній власник документа</div>
  <?php
  return false;
  }
?>
<div class="dfbox" id="<?php echo $model->idDocument; ?>_doc">
  <div class="row dfmetaheader">
    
    <h2>
      <?php $controller->echoInfoContainer($model,'DocumentName',$model->idDocument,'text',$model->DocumentName); ?>
    </h2>
    
    <div class="col-md-4 col-xs-12 col-sm-6 doc-info-container">
      категорія документа: <ul><li> 
      <?php 
      echo $model->_document_doccategory->CategoryName 
        . " " .$model->_document_doccategory->CategoryCode; ?>
      </li></ul>
    </div>
    
    <div class="col-md-4 col-xs-12 col-sm-6 doc-info-container">
      тип документа: <ul><li>
      <?php 
      echo $model->_document_doctype->TypeName; ?>
      </li></ul>
    </div>
    
    <?php if(Yii::app()->user->checkAccess('_DocsAdmin')){ ?>
    <div class="col-md-4 col-xs-12 col-sm-12 doc-info-container"><ul>
      <li>
      Створено користувачем:
      <u title="<?php echo $model->_document_user->info . '(' . $model->_document_user->contacts . ")"; ?>" >
      <?php echo $model->_document_user->username; ?>
      </u>
      </li><li>
      <?php $controller->echoInfoContainer($model,'Created',$model->idDocument,'text',
        $model->Created); ?>
      </li></ul>
    </div>
    <?php } ?>
  </div>
  
  
  <div class="row row-nomargins">
    <div class="col-xs-12">
      <div class="dfblock autoheight">
        <legend>Кореспондент</legend>
          <?php $controller->echoInfoContainer($model,'Correspondent',$model->idDocument,'textarea',
            $model->Correspondent); ?>
      </div>
    </div>
  </div>
  
  <div class="row row-nomargins">
    
    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="dfblock">
        <legend>Дата надходження та індекс документа</legend>
        <?php 
        //якщо користувач має права адмініструання документів
        if(Yii::app()->user->checkAccess('_DocsAdmin')){ ?>
        <ul><li>
        номер: 
        <?php //if ($model->SubmissionIndex){ ?>
        <?php $controller->echoInfoContainer($model,'SubmissionIndex',
          $model->idDocument,'text',$model->SubmissionIndex); ?>
         </li>
        <?php //} ?>
        <li>категорія:
        <?php $controller->echoInfoContainer($model,'CategoryID',
          $model->idDocument,'select',
          $model->_document_doccategory->CategoryName . ' ' 
          .$model->_document_doccategory->CategoryCode); ?>
        </li><li>
        дата надходження:
        <?php $controller->echoInfoContainer($model,'SubmissionDate',
          $model->idDocument,'text',$model->SubmissionDate); ?>
        </li></ul>
        <?php  }
          //якщо користувач не має права адмініструання документів, показати лише зібрану інформацію
          else if (!empty($model->_document_submit)) { ?>
          <?php echo "<ul class='doc-index-in'><li>"
            .$model->_document_submit[0]->SubmissionInfo
            ."</li></ul>"; ?>
        <?php } else { ?>
          <i>немає</i>
        <?php } ?>
      </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-4">
      <div class="dfblock">
        <legend>Дата та індекс документа</legend>
        <?php 
        if (strlen($model->ExternalIndex) > 0) { ?>
        <ul><li>
        <?php $controller->echoInfoContainer($model,'ExternalIndex',$model->idDocument,'text',
          $model->ExternalIndex); ?>
        </li></ul>
        <?php } else { ?>
          <i>немає</i>
        <?php } ?>
      </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-4">
      <div class="dfblock">
        <legend>
        <?php
          if(!(
                implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) !=
                  implode(',',$model->_document_user->department_ids) &&
                (!Yii::app()->user->checkAccess('_DocsAdmin'))
              )){
        ?>
        <input id="<?php echo $model->idDocument; ?>ytDocuments_file" 
               type="hidden" 
               value="" 
               name="Documents[file]" />
        <input id="<?php echo $model->idDocument; ?>fileupload" 
            type="file" 
            name="Documents[file]" 
            data-url="<?php 
              echo Yii::app()->CreateUrl("documents/update",array(
                'id' => $model->idDocument,
                'Documents[file]'=>'1'
              )); ?>"
            style="display: none;" />
        <a href="#" onclick="$('#<?php echo $model->idDocument; ?>fileupload').click();return false;" 
           title="Додати файл">
          <span class="glyphicon glyphicon-plus" style="font-size: 10pt;">додати</span>
        </a>
        <?php } ?>
        Файли</legend>
        <ol>
        <?php 
        $i = 0;
        foreach($model->doc_visible_files as $dfile){ ?> 
              <?php if ($dfile->exists){ 
                $owner = (($dfile->_file_user)? $dfile->_file_user->username : "відсутній власник!");
              ?>
              <li>
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
                  ?>" 
                  title="<?php echo $title; ?>" 
                  class="btn btn-sm btn-default btn-margin"
                  <?php if (isset($params['iframe']) && $params['iframe'] == "true"){ ?>
                  rel="prettyPhoto[iframes]" 
                  <?php } ?>
                  data-mime="<?php echo $mime; ?>">
                        <?php echo $name; ?>
                        <span class="glyphicon glyphicon-<?php 
                          if (isset($params['iframe']) && $params['iframe'] == "true"){ 
                            echo "eye-open";
                          } else {
                            echo "save";
                          }
                          ?>" aria-hidden="true">
                        </span>
                </a>
              <?php } else { ?>
                <button type="button" class="btn btn-sm btn-warning btn-margin">
                  <span class="glyphicon glyphicon-remove-sign" title="файл не знайдено"></span>
                </button>
              <?php } ?>
            
            <?php 
            if ($dfile->exists && ($dfile->UserID == Yii::app()->user->id)){ ?>
              <a class="btn btn-sm btn-danger btn-margin" title="приховати" 
                  data-pk="<?php  echo $dfile->idFile; ?>"
                  data-link="<?php echo Yii::app()->CreateUrl("files/hide"); ?>"
                  data-action="file-hide">
                <span class="glyphicon glyphicon-eye-close" ></span>
              </a>
            <?php  } ?>
            </li>
        <?php $i++; } ?>
        </ol>
      </div>
    </div>
    
  </div>
  
  
  <div class="row row-nomargins">
    <div class="col-xs-12 col-sm-12 col-md-6">
      <div class="dfblock docsummary">
        <legend>Короткий зміст документа</legend>
          <?php $controller->echoInfoContainer($model,'Summary',$model->idDocument,'textarea',
            $model->Summary); ?>
      </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="dfblock">
        <legend>Підписано</legend>
          <?php $controller->echoInfoContainer($model,'Signed',$model->idDocument,'text',
            $model->Signed); ?>
      </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="dfblock">
        <legend style="font-size: 8pt;">Резолюція або кому надіслано документ</legend>
          <?php $controller->echoInfoContainer($model,'Resolution',$model->idDocument,'text',
            $model->Resolution); ?>
      </div>
    </div>
   
  </div>
  
  <div class="row row-nomargins">
    
    <div class="col-xs-12 col-sm-12 col-md-6" >
      <div class="dfblock autoheight" style="min-height: 100px;">
        <legend class="show" id="<?php echo $model->idDocument; ?>-show">
          <span class="glyphicon glyphicon-send"></span>
          Розсилки
        </legend>
        
          <div class="nowrap">
          <?php for($i = 0; $i < count($model->_document_flows); $i++){  ?>
            <div class="flow-block">
              <legend><?php 
              echo CHtml::link(
                date("d.m.Y H:i",strtotime($model->_document_flows[$i]->Created)),
                Yii::app()->CreateUrl('flows/index',array(
                  'Flows[idFlow]'=>$model->_document_flows[$i]->idFlow
                )),
                array('target'=>"_blank")
              ); ?> - 
                <?php echo "<u title='"
                  .$model->_document_flows[$i]->_flow_user->info."("
                  .$model->_document_flows[$i]->_flow_user->contacts.")'>"
                    .$model->_document_flows[$i]->_flow_user->username
                  ."</u>"; ?>
              </legend>
            <?php foreach($model->_document_flows[$i]->_flow_flow_respondent as $flow_resp){ ?>
              <div class="resp resp-<?php echo ($flow_resp->AnswerID)? "green":"brown"; ?>"
                data-toggle="tooltip" data-placement="bottom"
                title="<?php echo ($flow_resp->AnswerID)? 
                    "Надано користувачем: "
                    .$flow_resp->_flow_respondent_answer->_answer_user->info
                    ." ".date("d.m.Y H:i",strtotime($flow_resp->_flow_respondent_answer->Created))
                    .((strlen(trim($flow_resp->_flow_respondent_answer->AnswerText)) > 0)? 
                      " (коментар: ".trim($flow_resp->_flow_respondent_answer->AnswerText).")" : "")
                  : ""; ?>">
                <span class="glyphicon glyphicon-<?php 
                  echo ($flow_resp->AnswerID)? "ok":"ban"; ?>-circle">
                </span>
                <?php echo $flow_resp->_flow_respondent_department->DepartmentName; ?>
              </div>
            <?php } ?>
            </div>
          <?php } 
          if (count($model->_document_flows) == 0){
            echo "<i>немає</i>";
          }
          ?>
          </div>
        
      </div>
    </div>
    
    <?php 
      //підготовка виведення даних контролю та виконання
      $oncontrol = "";
      $done = "";
      if(((strlen(trim($model->ControlDate)) > 0) || (strlen(trim($model->ControlMark)) > 0)) 
        && (strlen(trim($model->DoneMark)) > 0)){
        $done = "done";
      }
      if(((strlen(trim($model->ControlDate)) > 0) || (strlen(trim($model->ControlMark)) > 0)) 
        && (strlen(trim($model->DoneMark)) == 0)){
        $oncontrol = "oncontrol";
        $done = "";
      }
    ?>
    
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="dfblock <?php echo $oncontrol; ?>">
        <legend>Контроль</legend>
          <?php 
            if(((strlen(trim($model->ControlDate)) > 0) || (strlen(trim($model->ControlMark)) > 0)) || 
                !(implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) !=
                  implode(',',$model->_document_user->department_ids) &&
                (!Yii::app()->user->checkAccess('_DocsAdmin')))){
              echo "<ul class='controls'><!--li>дата контролю: ";
              $controller->echoInfoContainer($model,'ControlDate',$model->idDocument,'text',
                $model->ControlDate); 
              echo "</li--><li> ";
              $controller->echoInfoContainer($model,'ControlMark',$model->idDocument,'textarea',
                $model->ControlMark);
              echo "</li></ul>";
            } ?>
      </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="dfblock <?php echo $done; ?>">
        <legend>Відмітка про виконання документа</legend><ul class='marks'>
          <?php 
            if((strlen(trim($model->DoneMark)) > 0) ||
                !(implode(',',Users::model()->findByPk(Yii::app()->user->id)->department_ids) !=
                  implode(',',$model->_document_user->department_ids) &&
                (!Yii::app()->user->checkAccess('_DocsAdmin')))){
            ?>
            <li>
            <?php $controller->echoInfoContainer($model,'DoneMark',$model->idDocument,'textarea',
              $model->DoneMark); ?>
            </li>
          <?php } ?></ul>
      </div>
    </div>

  </div>
  
</div>

