<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

  /**
   * @var string the default layout for the controller view. Defaults to '//layouts/column1',
   * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
   */
  public $layout = '//layouts/main';

  /**
   * @var array context menu items. This property will be assigned to {@link CMenu::items}.
   */
  public $menu = array();

  /**
   * Ініціатор усіх контролерів 
   */
  public function init() {
    parent::init();
    if (Yii::app()->request->getIsAjaxRequest()) {
      $this->layout = '//layouts/clear';
    }
  }
  
/**
  * Метод обрізання строки до заданої довжини без розривання слів
  * @param string $str
  * @param integer $size
  * @return string
  */
  public function cutStrByWords($str, $size){
    if (!strlen($str)){
      return $str;
    }
    $line = wordwrap($str, $size, "_#_");
    $lines = explode('_#_',$line);
    if (!strlen($lines[0])){
      return $str;
    }
    return $lines[0];
  }
  
  /**
   * Вставляє код для встановлення відношень "багато до багатьох" для певного елементу
   * @param string $fieldIdName назва поля - джерела ідентифікаторів (e.g. Users[department_ids])
   * @param string $fieldInfoName назва поля - джерела назв (e.g. DepartmentName)
   * @param string $ajaxUrl URL-адреса, з якої повертається json-закодований масив даних
   * @param string $relatedModels масив моделей у відношенні many_many або has_many through (e.g. $model->_user_departments)
   * @param string $foreignId ідентифікатор (первинний ключ) у пов`язаній таблиці (e.g. idDepartment)
   * @param string $_title заголовок вставленого блоку
   * @param string $ajaxGroupUrl URL-адреса для вибору групи елементів
   */
  protected function many2ManyPicker( $fieldIdName, $fieldInfoName, $ajaxUrl,
                                      $relatedModels, $foreignId,
                                      $_title="Відношення",
                                      $ajaxGroupUrl=""){
    $this->renderPartial('/site/_many2many',array(
      'fieldIdName' => $fieldIdName,
      'fieldInfoName' => $fieldInfoName,
      'ajaxUrl' => $ajaxUrl,
      'relatedModels' => $relatedModels,
      'foreignId' => $foreignId,
      '_title' => $_title,
      'ajaxGroupUrl' => $ajaxGroupUrl
    ));
  }

}
