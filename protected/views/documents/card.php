<?php
/* @var $this DocumentsController */
/* @var $model Documents */

?>
<html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" type="text/css" 
    href="<?php echo Yii::app()->request->baseUrl; ?>/own/css/documents/card.css" />
</head>

<body>
  <table class="card">
    <tr>
      <td colspan="2" class="common last">
        <div class="header-nums">
        <?php
          for ($i = 1; $i <=30; $i++){
            echo $i . ' ';
          }
        ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="common last">
        <?php echo $model->Correspondent; ?>
        <br/>
        <div class='slabel'><b>Кореспондент</b></div>
      </td>
    </tr>
    <tr class="indexes">
      <td class="common half">
        <?php
        $submission_info_models = $model->_document_submit;
        if (count($submission_info_models)){
          echo $submission_info_models[0]->SubmissionInfo;
        }
        ?>
        <br/>
        <div class='slabel'><b>Дата надходження та індекс документа</b></div>
      </td>
      <td class="common last">
        <?php echo $model->ExternalIndex; ?>
        <br/>
        <div class='slabel'><b>Дата та індекс документа</b></div>
      </td>
    </tr>
    <tr>
     <td colspan="2" class="common last Summary">
        <?php echo $model->Summary; ?>
        <br/>
        <div class='slabel'><b>Короткий зміст</b></div>
      </td>
    </tr>
    <tr class="Resolution">
     <td colspan="2" class="common last">
        <?php echo $model->Resolution . ((strlen(trim($model->Signed)) > 0)? ' ( ' .$model->Signed . ') ' : ''); ?>
        <br/>
        <div class='slabel'><b>Резолюція або кому надіслано документ</b></div>
      </td>
    </tr>
    <tr class="ControlMarks">
     <td colspan="2" class="common last">
        <?php 
          echo $model->ControlMark;
          if (strlen(trim($model->ControlMark)) > 0 && strlen(trim($model->ControlDate)) > 0){
            echo ' ('.$model->ControlDate.')';
          }
          if (strlen(trim($model->DoneMark)) > 0 && strlen(trim($model->ControlMark)) > 0){
            echo ' ; '.$model->DoneMark;
          }
        ?>
        <br/>
        <div class='slabel'><b>Відмітка про виконання документа</b></div>
      </td>
    </tr>
  </table>
</body>
</html>