<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer $idEvent
 * @property integer $UserID
 * @property integer $FileID
 * @property integer $LevelID
 * @property integer $KindID
 * @property string $NewsUrl
 * @property string $ExternalID
 * @property string $Created
 * @property string $EventName
 * @property string $DateSmartField
 * @property string $StartTime
 * @property string $FinishTime
 * @property string $Place
 * @property string $Responsible
 * @property string $Contacts
 * @property string $EventDescription
 *
  * The followings are the available model relations:
 * @property Users $_event_user
 * @property Files $_event_file
 * @property Eventkinds[] $_event_eventkind
 * @property Eventlevels[] $_event_eventlevel
 * @property EventFlow[] $_event_event_flow
 * @property Flows[] $_event_flows
 * @property EventInvited[] $_event_event_invited
 * @property Departments[] $_event_invited
 * @property Departments[] $_event_organizers
 * @property EventOrganizer[] $_event_event_organizer
 * @property EventDate[] $_event_event_date
 *
 * @property CUploadedFile $uploaded_file прикріплений файл
 * @property integer[] $organizer_ids масив іденифікаторів або -1 підрозділів організаторів
 * @property integer[] $invited_ids масив іденифікаторів або -1 підрозділів запрошених
 * @property string[] $organizer_names масив імен чи назв організаторів
 * @property string[] $invited_names масив імен чи назв запрошених
 * @property integer[] $invited_seets масив кількостей очікуваних місць для кожного запрошеного
 * @property string[] $event_dates масив дат у форматі yyyy-mm-dd
 * 
 * @property string $date_search рядок для пошуку по підрядку атрибуту DateSmartField (НЕ РЕАЛІЗОВАНО)
 * @property integer $past <ul><li>-1 - усі події</li><li>0 - буде</li><li>1 - було</li><li>2 -зараз</li></ul>
 * 
 * @property integer $_flow_to_invited чи робити розсилку запрошеним підрозділам
 * @property integer $_send_to_site чи відсилати на сайт новин ЗНУ через CURL
 * @property string $remaining_time скільки часу залишилось (днів і годин) або "подія вже відбулась"
 * @property string $datetime_rule Human Readable DateSmartField
 * 
 * @property string $url URL REST API-сервісу сайту для відправки новин через CURL
 * @property string $api_key ключ для ідентифікації в REST API-сервісі сайту 
 * @property integer $site_id ідентифікатор веб-ресурсу сайту новин, куди буде збережено захід
 */
class Events extends CActiveRecord
{
  public $uploaded_file;
  public $organizer_ids;
  public $invited_ids;
  public $organizer_names;
  public $invited_names;
  public $invited_seets;
  public $event_dates;
  
  public $date_search;
  public $past;
  
  public $_flow_to_invited = 0;
  public $_send_to_site = 0;
  public $remaining_time;
  public $datetime_rule;
  
  public $url = "http://sites.znu.edu.ua/cms/index.php";
  public $api_key = 'dksjf;aj;weio[wlooiuoiuhlk;lk\'';
  public $site_id = 89;
  // --- ДЛЯ ТЕСТУВАННЯ
  //public $url = "http://10.1.22.8/cms/index.php";
  //public $api_key = '1234567';
  //public $site_id = 62;
  
  public $wdays = array("нд","пн","вт","ср","чт","пт","сб","нд");
  public $wday_alias = array(
     "нд" => "щонеділі",
     "пн" => "щопонеділка",
     "вт" => "щовівторка",
     "ср" => "щосереди",
     "чт" => "щочетверга",
     "пт" => "щоп`ятниці",
     "сб" => "щосуботи",
  );
  public $patterns = array(
    "/^\s*(\d{1,2})\.(\d{1,2})\.(\d{4,4})\s*-\s*(\d{1,2})\.(\d{1,2})\.(\d{4,4})\s*$/",
    "/^\s*(\d{1,2})\s*-\s*(\d{1,2})\.(\d{1,2})\.(\d{4,4})\s*$/",
    "/^\s*(\d{1,2})\.(\d{1,2})\.(\d{4,4})\s*$/",
    "/^\s*(\d{1,2})\.(\d{1,2})\s*$/",
    "/^\s*(\d{1,2})\.(\d{1,2})\s*-\s*(\d{1,2})\.(\d{4,4})\s*$/",
    "/^\s*(пн|вт|ср|чт|пт|сб|нд)\s*(\/\s*(\d{1,2})\.(\d{1,2})\.(\d{4,4})\s*-\s*(\d{1,2})\.(\d{1,2})\.(\d{4,4})\s*)?$/i",
  );
  
  /**
  /**
   * @return string the associated database table name
   */
  public function tableName(){
    return 'events';
  }

  protected function beforeValidate() {
    parent::beforeValidate();
    $is_error = true;
    foreach ($this->patterns as $pattern){
      if (preg_match($pattern,$this->DateSmartField,$matches)){
        $is_error = false; continue;
      }
    }
    if ($is_error 
        || (is_array($this->event_dates && !count($this->event_dates)))){
      $this->addError('DateSmartField','Невірний формат правила формування дат!');
    }
    if (!preg_match("/[0-9]{1,2}:[0-9]{1,2}(:[0-9]{1,2})?/",$this->StartTime,$matches) 
      && strlen($this->StartTime) > 0){
        $this->addError('StartTime','Невірний формат часу!');
        return false; 
    }
    if (!preg_match("/[0-9]{1,2}:[0-9]{1,2}(:[0-9]{1,2})?/",$this->FinishTime,$matches) 
      && strlen($this->FinishTime)>0){
        $this->addError('FinishTime','Невірний формат часу!');
        return false; 
    }
    if ($this->uploaded_file){
      $fmodel = new Files();
      $fmodel->Visible = 1;
      $fmodel->UserID = Yii::app()->user->id;
      $fmodel->file_itself = $this->uploaded_file;
      if (!$fmodel->validate()){
        $errs = $fmodel->getErrors();
        $err = array();
        foreach ($errs as $e){
          $err[] = implode('; ',$e);
        }
        $this->addError('uploaded_file',implode('| ',$err));
        return false;
      }
    }
    $this->Created = date("Y-m-d H:i:s");
    return true;
  }
  
  protected function afterSave() {
    parent::afterSave();
    if (isset($this->uploaded_file)){
      $fmodel = new Files();
      $fmodel->Visible = 1;
      $fmodel->UserID = Yii::app()->user->id;
      $fmodel->file_itself = $this->uploaded_file;
      if(!$fmodel->save()){
        $errs = $fmodel->getErrors();
        $err = array();
        foreach ($errs as $e){
          $err[] = implode('; ',$e);
        }
        $this->addError('uploaded_file',implode('| ',$err));
          return false;
      }
      $self_event = Events::model()->findByPk($this->idEvent);
      $self_event->FileID = $fmodel->idFile;
      $self_event->uploaded_file = null;
      $self_event->save();
    }
    if (is_array($this->organizer_ids) 
      && is_array($this->organizer_names)){
      $q = EventOrganizer::model()->deleteAll("EventID=".intval($this->idEvent));
      for ($i = 0; $i < count($this->organizer_ids); $i++){
        $m = new EventOrganizer();
        $m->EventID = $this->idEvent;
        if ($this->organizer_ids[$i] > 0){
          $m->DeptID = $this->organizer_ids[$i];
        } else {
          $m->DeptID = -1;
        }
        $m->OrganizerComment = $this->organizer_names[$i];
        if(!$m->save()){
          var_dump($m->getErrors());exit();
        }
      }
    }
    if (is_array($this->invited_ids) 
      && is_array($this->invited_names) 
      && is_array($this->invited_seets)){
      $r = EventInvited::model()->deleteAll("EventID=".intval($this->idEvent));
      for ($i = 0; $i < count($this->invited_ids); $i++){
        $m = new EventInvited();
        $m->EventID = $this->idEvent;
        if ($this->invited_ids[$i] > 0){
          $m->DeptID = $this->invited_ids[$i];
        } else {
          $m->DeptID = -1;
        }
        $m->InvitedComment = $this->invited_names[$i];
        $m->Seets = $this->invited_seets[$i];
        if(!$m->save()){
          var_dump($m->getErrors());exit();
        }
      }
    }
    if (is_array($this->event_dates)){
      $s = EventDate::model()->deleteAll("EventID=".intval($this->idEvent));
      for ($i = 0; $i < count($this->event_dates); $i++){
        if(strtotime($this->event_dates[$i]) > 0){
          $date = date("Y-m-d",strtotime($this->event_dates[$i]));
          $event_date = new EventDate();
          $event_date->EventID = $this->idEvent;
          $event_date->EventDate = $date;
          if(!$event_date->save()){
            $errs = $event_date->getErrors();
            $err = array();
            foreach ($errs as $e){
              $err[] = implode('; ',$e);
            }
            $this->addError('DateSmartField',implode('| ',$err));
              return false;
          }
        }//end if
      }//end for
    }//end if is_array
    $this->genFlow();
    $response = $this->SendToService();
    if (!$response){
      return true;
    }
    $jr = json_decode($response);
    $m = Events::model()->findByPk($this->idEvent);
    if ((!isset($jr->calendar))? true: !isset($jr->calendar->id)){  
      $m->ExternalID = null; $m->NewsUrl = null; $m->save();
    } else {
      $m->ExternalID = $jr->calendar->id;
      $m->NewsUrl = str_replace('{year}',date("Y",strtotime($m->event_dates[0])),
        str_replace('{month}',date("m",strtotime($m->event_dates[0])),
          str_replace('{day}',date("d",strtotime($m->event_dates[0])),
            $jr->calendar->url)));
      $m->save();
    }
    return true;
  }
  
  
  protected function afterFind() {
    parent::afterFind();
    $this->organizer_ids = array();
    $this->invited_ids = array();
    $this->invited_names = array();
    $this->organizer_names = array();
    $this->event_dates = array();
    $this->invited_seets = array();

    foreach ($this->_event_event_invited as $model){
      /* @var $model EventInvited */
      $this->invited_ids[] = $model->DeptID;
      $this->invited_names[] = $model->InvitedComment;
      $this->invited_seets[] = $model->Seets;
    }
    foreach ($this->_event_event_organizer as $model){
      /* @var $model EventOrganizer */
      $this->organizer_ids[] = $model->DeptID;
      $this->organizer_names[] = $model->OrganizerComment;
    }
    foreach ($this->_event_event_date as $model){
      $this->event_dates[] = $model->EventDate;
    }
    if (!$this->isNewRecord){
      $datetime_rule = preg_replace("/,(\d\d?)(,|$)/i",",$1 числа кожного місяця$2",
      str_replace($this->wdays,$this->wday_alias,  mb_strtolower($this->DateSmartField,'utf8')))
        . " ".(($this->StartTime)? mb_substr($this->StartTime,0,5,"utf-8"): "(час початку не вказано)")
        .(($this->FinishTime)? " - ".mb_substr($this->FinishTime,0,5,"utf-8"): "");
      $time_to_event = -1;
      if (!$this->StartTime){
        $this->StartTime = "00:00:00";
      }
      for ($i = 0; ($time_to_event < 0 && $i < count($this->_event_event_date)); $i++){
        $time_to_event = strtotime($this->_event_event_date[$i]->EventDate.' '.$this->StartTime) -
          strtotime(date('Y-m-d H:i:s'));
      }
      $rest = "";
      if($time_to_event < 0){
        $rest = 'подія вже відбулась';
      } else {
        $rest = 'залишилось днів: ' . (floor($time_to_event / (24.0*3600.0)))
          . ', годин: ' . (floor($time_to_event / (3600.0)) % 24);
      }
      $this->datetime_rule = $datetime_rule;
      $this->remaining_time = $rest;
    }
    return true;
  }
  
  protected function beforeDelete(){
    parent::beforeDelete();
    $q = EventOrganizer::model()->deleteAll("EventID=".intval($this->idEvent));
    $r = EventInvited::model()->deleteAll("EventID=".intval($this->idEvent));
    $file = Files::model()->findByPk("FileID=".intval($this->FileID));
    if ($file){
      $file->delete();
    }
    $event_flows = EventFlow::model()->findAllByAttributes(array(
      'EventID' => intval($this->idEvent)
    ));
    $event_dates = EventDate::model()->findAllByAttributes(array(
      'EventID' => intval($this->idEvent)));
    foreach ($event_dates as $ed){
      $ed->delete();
    }
    foreach ($event_flows as $f_e){
      $f = Flows::model()->findByPk($f_e->FlowID);
      if (!$f){
        $f_e->delete();
        continue;
      }
      if (count($f->_flow_documents)+count($f->_flow_events) == 1){
        $f->delete();
      } else {
        $f_e->delete();
      }
    }
    if ($this->ExternalID){
      //видалення заходу на сайті ЗНУ
      $ch = curl_init($this->url);
      $data = array(
        'api_key' => $this->api_key,
        'action' => 'calendar/api/delete',
        'lang' => 'ukr',
        'site_id' => $this->site_id,
        'nazva' => $this->EventName,
        'vis' => 1,
        'categories' => implode(',',
          array(
            $this->_event_eventkind->KindName,
            $this->_event_eventlevel->LevelName
          )
        ),
        'dates' => array(),
        'description' => ''
      );
      if ($this->ExternalID > 0){
        $data['id'] = $this->ExternalID;
      }
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_exec($ch);
      curl_close($ch);
    }
    return true;
  }
  
  /**
   * @return array validation rules for model attributes.
   */
  public function rules(){
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('UserID, LevelID, KindID, Created, EventName, DateSmartField', 'required'),
      array('UserID, FileID, LevelID, KindID', 'numerical', 'integerOnly'=>true),
      array('NewsUrl, EventName, DateSmartField, Place, Responsible, Contacts', 'length', 'max'=>255),
      array('ExternalID', 'length', 'max'=>30),
      array('StartTime, FinishTime, EventDescription', 'safe'),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('idEvent, UserID, FileID, LevelID, KindID, NewsUrl, ExternalID, Created, 
      EventName, DateSmartField, StartTime, FinishTime, 
      Place, Responsible, Contacts, EventDescription', 'safe', 'on'=>'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations(){
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      '_event_user' => array(self::BELONGS_TO, 'Users', 'UserID'),
      '_event_file' => array(self::BELONGS_TO, 'Files', 'FileID'),
      '_event_eventkind' => array(self::BELONGS_TO, 'Eventkinds', 'KindID'),
      '_event_eventlevel' => array(self::BELONGS_TO, 'Eventlevels', 'LevelID'),
      '_event_event_flow' => array(self::HAS_MANY, 'EventFlow', 'EventID'),
      '_event_flows' => array(self::HAS_MANY, 'Flows', 'FlowID', 
        'through' => '_event_event_flow', 'order' => '_event_flows.Created DESC'),
      '_event_event_invited' => array(self::HAS_MANY, 'EventInvited', 'EventID'),
      '_event_invited' => array(self::HAS_MANY, 'Departments', 'DeptID', 
        'through' => '_event_event_invited', 'order' => '_event_invited.DepartmentName ASC'),
      '_event_event_organizer' => array(self::HAS_MANY, 'EventOrganizer', 'EventID'),
      '_event_organizers' => array(self::HAS_MANY, 'Departments', 'DeptID', 
        'through' => '_event_event_organizer', 'order' => '_event_organizers.DepartmentName ASC'),
      '_event_event_date' => array(self::HAS_MANY, 'EventDate', 'EventID', 
        'order' => '_event_event_date.EventDate ASC'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels(){
    return array(
      'idEvent' => 'ID',
      'UserID' => 'користувач',
      'FileID' => 'файл',
      'LevelID' => 'рівень заходу',
      'KindID' => 'вид заходу',
      'NewsUrl' => 'посилання на зовнішній ресурс (сайт ЗНУ)',
      'ExternalID' => 'ідентифікатор події на сайті ЗНУ',
      'Created' => 'дата і час створення',
      'EventName' => 'назва заходу',
      'DateSmartField' => 'правило формування дат',
      'StartTime' => 'час початку',
      'FinishTime' => 'закінчення',
      'Place' => 'місце заходу',
      'Responsible' => 'відповідальні особи',
      'Contacts' => 'контактні дані',
      'EventDescription' => 'опис і деталі заходу',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   *
   * Typical usecase:
   * - Initialize the model fields with values from filter form.
   * - Execute this method to get CActiveDataProvider instance which will filter
   * models according to data in model fields.
   * - Pass data provider to CGridView, CListView or any similar widget.
   *
   * @return CActiveDataProvider the data provider that can return the models
   * based on the search/filter conditions.
   */
  public function search(){
    return EventDate::model()->search($this);
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Events the static model class
   */
  public static function model($className=__CLASS__){
    return parent::model($className);
  }
  
  /**
   * Створення розсилки запрошеним підрозділам з видаленням старої
   * @return boolean
   * @throws CHttpException
   */
  protected function genFlow(){
    if (!$this->_flow_to_invited || empty($this->invited_ids)){
      return false;
    }
    $no_invited = true;
    foreach ($this->invited_ids as $invited_id){
      if ($invited_id > 0){
        $no_invited = false;
        break;
      }
    }
    if ($no_invited){
      return false;
    }
    $old_docflowevents = EventFlow::model()->findAllByAttributes(
      array('EventID' => $this->idEvent)
    );
    if (count($old_docflowevents) > 0){
      $old_docflowevents[0]->_event_flow_flow->delete();
    }
    $docflow = new Flows();
    $docflow->FlowName = $this->EventName;
    $docflow->FlowDescription = $this->EventDescription;
    $docflow->PeriodID = 1;
    $docflow->Created =  date('Y-m-d H:i:s');
    $docflow->event_ids = array($this->idEvent);
    $docflow->dept_ids = array();
    for ($i = 0; $i < count($this->invited_ids); $i++){
      if ($this->invited_ids[$i] > 0){
        $docflow->dept_ids[] = $this->invited_ids[$i];
      }
    }
    $docflow->UserID = $this->UserID;
    if (!$docflow->save()){
      $msg = "";
        foreach ($docflow->getErrors() as $attr => $a_err){
          $msg .= $attr." :: ".implode(",",$a_err)." | ";
        }
        throw new CHttpException(400, 
          'Помилка збереження розсилки : '.$msg);
    }
    return true;
  }
  
  /**
   * Відправлення даних на веб-сервіс через CURL POST-запитом
   * @return string відповідь сервера сайту у форматі json
   */
  protected function SendToService(){
    if (!$this->_send_to_site || empty($this->_send_to_site)){
      return false;
    }
    $date_intervals = array();
    // підключення
    $url = $this->url;
    $api_key = $this->api_key;
    $site_id = $this->site_id;
    $ch = curl_init($url);
    $invited = "";
    $organizers = "";
    $date_time =  preg_replace("/,(\d\d?)(,|$)/i",",$1 числа кожного місяця$2",
          str_replace($this->wdays,$this->wday_alias, mb_strtolower($this->DateSmartField,'utf8')))
        . " ".(($this->StartTime)? mb_substr($this->StartTime,0,5,"utf-8"): "(час початку не вказано)")
        .(($this->FinishTime)? " - ".mb_substr($this->FinishTime,0,5,"utf-8"): "");
    for ($i = 0; ($i < count($this->invited_names) 
      && is_array($this->invited_names)); $i++){
      if ($i == 0){
        $invited .= '<ul>';
      }
      $invited .= '<li>'.$this->invited_names[$i]
        .'</li>';
      if ($i == count($this->invited_names) - 1){
        $invited .= "</ul>";
      }
    }
    for ($i = 0; ($i < count($this->organizer_names)
      && is_array($this->organizer_names)); $i++){
      if ($i == 0){
        $organizers .= '<ul>';
      }
      $organizers .= '<li>'.$this->organizer_names[$i]
        .'</li>';
      if ($i == count($this->organizer_names) - 1){
        $organizers .= "</ul>";
      }
    }
    //формування часових інтервалів для кожної дати
    for ($i = 0; $i < count($this->event_dates) && is_array($this->event_dates); $i++){
      $begin_time = (strlen($this->StartTime) > 0)? $this->StartTime : "00:00:00";
      $end_time = (strlen($this->FinishTime) > 0)? $this->FinishTime : "23:59:59";
      $begin_timestamp = strtotime($this->event_dates[$i] . ' ' . $begin_time);
      $end_timestamp = strtotime($this->event_dates[$i] . ' ' . $end_time);
      $date_intervals[] = array(
         'pochrik' => date('Y',$begin_timestamp),
         'pochmis' => date('m',$begin_timestamp),
         'pochtyzh' => -1,
         'pochday' => date('d',$begin_timestamp),
         'pochgod' => date('H',$begin_timestamp),
         'pochhv' => date('i',$begin_timestamp),
         
         'kinrik' => date('Y',$end_timestamp),
         'kinmis' => date('m',$end_timestamp),
         'kintyzh' => -1,
         'kinday' => date('d',$end_timestamp),
         'kingod' => date('H',$end_timestamp),
         'kinhv' => date('i',$end_timestamp)
      );
    }
    // дані для відправки
    $data = array(
      'api_key' => $api_key,
      'action' => 'calendar/api/'
        .(($this->ExternalID)? 'update':'create'),
      'lang' => 'ukr',
      'site_id' => $site_id,
      'nazva' => $this->EventName,
      'vis' => 1,
      'categories' => implode(',',
        array(
          $this->_event_eventkind->KindName,
          $this->_event_eventlevel->LevelName
        )
      ),
      'dates' => $date_intervals, 
      'description' => ''
        . '<div class="EventPlaceHeader">Місце проведення: </div>'
        . '<div class="EventPlace">'
            .((empty($this->Place))? 
            "не вказано":$this->Place) . '</div>'
        . '<div class="DateTimeHeader">Дата і час: </div> '
        . '<div class="DateTime">'.$date_time . '</div>'
        . '<div class="EventDescription">'. $this->EventDescription . '</div>'
        . '<div class="InvitedHeader">Запрошені: </div>'
        . '<div class="InvitedList">'.((empty($invited))? "не вказано":$invited).'</div>'
        . '<div class="OrganizersHeader">Організатори: </div>'
        . '<div class="OrganizersList">'.((empty($organizers))? "не вказано":$organizers).'</div>'
        . '<div class="ResponsibleHeader">'.'Відповідальні особи: </div>'
        . '<div class="Responsible">'
            .((!strlen(trim($this->Responsible)))? 
            "не вказано":$this->Responsible) . '</div>'
        . '<div class="ContactsHeader">'.'Контактні дані: </div>'
        . '<div class="Contacts">'
            .((!strlen(trim($this->Contacts)))? 
            "не вказано":$this->Contacts) . '</div>'
    );
    if ($this->ExternalID > 0){
      $data['id'] = $this->ExternalID;
    }
    //var_dump($data);exit();
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    // треба отримати результат
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // запит...
    $response = curl_exec($ch);
    curl_close($ch);
    $fp = fopen(Yii::app()->basePath.'/logs/curl.log','a+');
    fwrite($fp,$response."\n");
    fclose($fp);
    return $response;
  }
  
  /**
   * Список організаторів як назв підрозділів чи груп у вигляді html-списку (ul)
   * @return string
   */
  public function organizerHtmlList(){
    $names = Deptgroups::getDeptGroupNamesOrDeptNamesByIds($this->organizer_ids);
    $organizers = '<ul>';
    for ($i = 0; ($i < count($this->organizer_names)
      && is_array($this->organizer_names)); $i++){
      if ($this->organizer_ids[$i] < 0){
        $organizers .= '<li>'.$this->organizer_names[$i]
          .'</li>';
      } else if (isset($names['departments'][$this->organizer_ids[$i]])){
        $organizers .= '<li>'.$this->organizer_names[$i]
          .'</li>';
        unset($names['departments'][$this->organizer_ids[$i]]);
      }
    }
    foreach ($names['groups'] as $group_name){
      $organizers .= '<li>'.$group_name
        .'</li>';
    }
    $organizers .= "</ul>";
    return $organizers;
  }
  
  /**
   * Список запрошених як назв підрозділів чи груп у вигляді html-списку (ul)
   * @return string
   */
  public function invitedHtmlList(){
    $names = Deptgroups::getDeptGroupNamesOrDeptNamesByIds($this->invited_ids);
    $invited = '<ul>';
    for ($i = 0; ($i < count($this->invited_names)
      && is_array($this->invited_names)); $i++){
      if ($this->invited_ids[$i] < 0){
        $invited .= '<li>'.$this->invited_names[$i]
          .'</li>';
      } else if (isset($names['departments'][$this->invited_ids[$i]])){
        $invited .= '<li>'.$this->invited_names[$i]
          .'</li>';
        unset($names['departments'][$this->invited_ids[$i]]);
      }
    }
    foreach ($names['groups'] as $group_name){
      $invited .= '<li>'.$group_name
        .'</li>';
    }
    $invited .= "</ul>";
    return $invited;
  }

}
