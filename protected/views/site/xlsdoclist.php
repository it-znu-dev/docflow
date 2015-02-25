<?php
/* @var $models Documents[] */
/* @var $doc Documents */

error_reporting(E_ERROR);

$file_name = 'Report_'.date('d.m.Y_h-i-s') . '.xls';
$workbook = new Spreadsheet_Excel_Writer();
$workbook->send($file_name);
//solution from http://stackoverflow.com/questions/2674489/spreadsheet-excel-writer-data-output-is-damaged
$workbook->_codepage =  0x04E3 ; 

//need for win
$out_encoding = "windows-1251";

// Creating a worksheet
$worksheet =& $workbook->addWorksheet('Report');
$worksheet->setInputEncoding('windows-1251');
$worksheet->setLandscape();
$worksheet->setMarginBottom(0.5);

$doc = new Documents;
// put text at the top and center it horizontally
$format_top_center =& $workbook->addFormat();
$format_top_center->setAlign('top');
$format_top_center->setAlign('center');
$format_top_center->setBold();
$format_top_center->setTextWrap();
$format_top_center->setBorder(1);
//solution from http://stackoverflow.com/questions/2674489/spreadsheet-excel-writer-data-output-is-damaged
$format_top_center->_font_charset = 0xCC;

$format_wordwrap =& $workbook->addFormat();
$format_wordwrap->setTextWrap();
$format_wordwrap->setAlign('top');
$format_wordwrap->setBorder(1);
//solution from http://stackoverflow.com/questions/2674489/spreadsheet-excel-writer-data-output-is-damaged
$format_wordwrap->_font_charset = 0xCC;//solution from stackoverflow.com


// The actual data
$worksheet->setColumn(0, 0, 16);
$worksheet->setColumn(1, 1, 16);
$worksheet->setColumn(2, 2, 18);
$worksheet->setColumn(3, 3, 24);
$worksheet->setColumn(4, 4, 17);
$worksheet->setColumn(5, 5, 11);
$worksheet->setColumn(6, 6, 21);

$worksheet->write(0, 0, iconv("utf-8", $out_encoding,"дата надходж. та індекс"),
        $format_top_center);
$worksheet->write(0, 1, iconv("utf-8", $out_encoding,"дата та індекс"),
        $format_top_center);
$worksheet->write(0, 2, iconv("utf-8", $out_encoding,$doc->getAttributeLabel('Correspondent')),
        $format_top_center);
$worksheet->write(0, 3, iconv("utf-8", $out_encoding,$doc->getAttributeLabel('Summary')),
        $format_top_center);
$worksheet->write(0, 4, iconv("utf-8", $out_encoding,$doc->getAttributeLabel('Resolution')),
        $format_top_center);
$worksheet->write(0, 5, iconv("utf-8", $out_encoding,"тип документа"),
        $format_top_center);
$worksheet->write(0, 6, iconv("utf-8", $out_encoding,"відмітка виконання"),
        $format_top_center);
$i = 1;


    for ($j = 0; $j < count($models); $j++){
      /* @var $model Documents */
      $model = $models[$j];
      $input_num = "(відсутні)";
      if (!empty($model->_document_submit)){
        $input_num = $model->_document_submit[0]->SubmissionInfo;
      }
      $worksheet->write($i, 0, iconv("utf-8", $out_encoding, $input_num),
              $format_wordwrap);
      $worksheet->write($i, 1, iconv("utf-8", $out_encoding,$model->ExternalIndex),
              $format_wordwrap);
      $worksheet->write($i, 2, iconv("utf-8", $out_encoding,$model->Correspondent),
              $format_wordwrap);
      $worksheet->write($i, 3, iconv("utf-8", $out_encoding,$model->Summary),
              $format_wordwrap);
      $for_whom = (strlen(trim(
        str_replace("\n"," ",
          str_replace("\r"," ",$model->Resolution)))) > 0) ? 
              $model->Resolution . '; ' : "";
      if (trim($model->Signed)){
        $for_whom .= 'підписано: '.$model->Signed;
      }
      $worksheet->write($i, 4, iconv("utf-8", $out_encoding,$for_whom),
              $format_wordwrap);
      $worksheet->write($i, 5, iconv("utf-8", $out_encoding,$model->_document_doctype->TypeName),
              $format_wordwrap);
      $mark = implode(" ; ",array($model->ControlMark, $model->DoneMark));
      if (str_replace("\n","",str_replace("\r","",trim($mark))) == ";"){
        $mark = "";
      }
      $worksheet->write($i, 6, iconv("utf-8", $out_encoding,$mark),
              $format_wordwrap);
      $i++;
      
      $index_diff = 0;
      
      if ($j < count($models)-1){
        $index_diff = intval($models[$j]->SubmissionIndex)
          - intval($models[$j+1]->SubmissionIndex);
      }
      
      if ($j < count($models)-1 && ($index_diff > 1) && strlen($model->_document_doccategory->CategoryCode) > 0){
        for ($k = intval($models[$j]->SubmissionIndex)-1; $k >= intval($models[$j+1]->SubmissionIndex)+1; $k--){
          $worksheet->write($i, 0, iconv("utf-8", $out_encoding, 
            "№ ".$k.'/'.$model->_document_doccategory->CategoryCode),
                  $format_wordwrap);
          $worksheet->write($i, 1, iconv("utf-8", $out_encoding,"------"),
                  $format_wordwrap);
          $worksheet->write($i, 2, iconv("utf-8", $out_encoding,"------"),
                  $format_wordwrap);
          $worksheet->write($i, 3, iconv("utf-8", $out_encoding,"------"),
                  $format_wordwrap);
          $worksheet->write($i, 4, iconv("utf-8", $out_encoding,"------"),
                  $format_wordwrap);
          $worksheet->write($i, 5, iconv("utf-8", $out_encoding,"------"),
                  $format_wordwrap);
          $worksheet->write($i, 6, iconv("utf-8", $out_encoding,"------"),
                  $format_wordwrap);
          $i++;
        }
      }
    }

// Let's send the file
$workbook->close();
?>


