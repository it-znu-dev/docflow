<script>
  $(function(){
    $('.datepicker').datepicker({
        format: 'dd.mm.yyyy',
        weekStart: 1,
        language: 'uk',
        autoclose: true,
        todayHighlight: true
    });
  });
</script>

  <div class="row row-nomargins">
    <div class="col-md-4 col-xs-12">
      <div class="dfbox">
      <h2 style="text-align: center;">ДОВІДКА ПРО ОБСЯГ</h2>
      <?php
      $form = $this->beginWidget('CActiveForm', array(
         'id' => 'rept1-form',
         'action' => Yii::app()->createUrl("site/rept1"),
         'enableAjaxValidation' => false,
         'method' => 'GET',
      ));
      ?>
      <div class="row row-nomargins">
        <div class="col-xs-5">
          <?php echo CHtml::label('Рік',
            'year1', array(
            'class' => 'col-xs-12',
          )); ?>
          <?php echo CHtml::dropDownList('year', date("Y"), Documents::model()->getYears(), array(
            'id' => 'year1',
            'class' => 'form-control col-xs-12',
          ));
          ?>
        </div>
        <div class="col-xs-7">
          <?php echo CHtml::label('*',
            'rept1-submit', array(
            'class' => 'col-xs-12',
          )); ?>
          <input type="submit" 
                 class="form-control btn btn-sm btn-primary col-xs-12" 
                 id="rept1-submit"
                 value="Завантажити таблицю Excel" />
        </div>
      </div>
      <?php
      $this->endWidget();
      ?>
      </div>
    </div>
    
    <div class="col-md-4 col-xs-12 " id="doclisttoxls-block">
      <div class="dfbox">
      <h2 style="text-align: center;">ЖУРНАЛ</h2>
      <?php
      $form = $this->beginWidget('CActiveForm', array(
         'id' => 'doclisttoxls-form',
         'action' => Yii::app()->createUrl("site/doclisttoxls"),
         'enableAjaxValidation' => false,
         'method' => 'GET',
      ));
      ?>
      <div class="row row-nomargins">
        <?php
        $category_list = Doccategories::model()->dropDown();
        $category_list[0] = 'Усі категорії';
        ?>
        <div class="col-xs-12 dfheader">Категорія</div>
        <?php echo CHtml::dropDownList('category', 0, $category_list, array(
          'class' => 'form-control col-xs-12',
        )); ?>
        <div class="row row-nomargins">
          <div class="col-xs-6">
            <?php echo CHtml::label('Від дати', 'doclisttoxls-datefrom',array(
              'class' => 'col-xs-12 dfheader',
            )); ?>
            <?php
            echo CHtml::textField('datefrom', str_replace('-', '.', date('d-m-Y')),
              array('id' => 'doclisttoxls-datefrom',
               'class' => 'form-control col-xs-12 datepicker'));
            ?>
          </div>
          <div class="col-xs-6">
            <?php echo CHtml::label('До дати', 'doclisttoxls-dateto',array(
              'class' => 'col-xs-12 dfheader',
            )); ?>
            <?php
            echo CHtml::textField('dateto', str_replace('-', '.', date('d-m-Y')),
              array('id' => 'doclisttoxls-dateto',
               'class' => 'form-control col-xs-12 datepicker'));
            ?>
          </div>
        </div>
      </div>
      <div class="row row-nomargins">
        <div class="col-xs-5">
          <?php echo CHtml::label('Максимум',
            'doclisttoxls-limit', array(
            'class' => 'col-xs-12',
          )); ?>
          <?php echo CHtml::textField('limit', '1000', array(
            'id' => 'doclisttoxls-limit',
            'class' => 'form-control col-xs-12',
          ));
          ?>
        </div>
        <div class="col-xs-7">
          <?php echo CHtml::label('*',
            'doclisttoxls-submit', array(
            'class' => 'col-xs-12',
          )); ?>
          <input type="submit" 
                 class="form-control btn btn-sm btn-primary col-xs-12" 
                 id="doclisttoxls-submit"
                 value="Завантажити таблицю Excel" />
        </div>
      </div>
      <?php
      $this->endWidget();
      ?>
      </div>
    </div>
    
    <div class="col-md-4 col-xs-12">
      <div class="dfbox">
      <h2 style="text-align: center;">ЗВЕДЕННЯ</h2>
      <?php
      $form = $this->beginWidget('CActiveForm', array(
         'id' => 'rept2-form',
         'action' => Yii::app()->createUrl("site/rept2"),
         'enableAjaxValidation' => false,
         'method' => 'GET',
      ));
      ?>
      <div class="row row-nomargins">
        <div class="col-xs-5">
          <?php echo CHtml::label('Рік',
            'year2', array(
            'class' => 'col-xs-12',
          )); ?>
          <?php echo CHtml::dropDownList('year', date("Y"), Documents::model()->getYears(), array(
            'id' => 'year2',
            'class' => 'form-control col-xs-12',
          ));
          ?>
        </div>
        <div class="col-xs-7">
          <?php echo CHtml::label('*',
            'rept2-submit', array(
            'class' => 'col-xs-12',
          )); ?>
          <input type="submit" 
                 class="form-control btn btn-sm btn-primary col-xs-12" 
                 id="rept2-submit"
                 value="Завантажити таблицю Excel" />
        </div>
      </div>
      <?php
      $this->endWidget();
      ?>
      </div>
    </div>
  </div>

  <div class="row row-nomargins">
    <div class="col-md-4 col-xs-12">
      <div class="dfbox">
      <h2 style="text-align: center;">Документи з не унікальними індексами</h2>
      <?php
      $form = $this->beginWidget('CActiveForm', array(
         'id' => 'docsWithNotUniqueIndexes-form',
         'action' => Yii::app()->createUrl("site/docsWithNotUniqueIndexes"),
         'enableAjaxValidation' => false,
         'method' => 'GET',
      ));
      ?>
      <div class="row row-nomargins">
        <div class="col-xs-12">
          <?php echo CHtml::label('*',
            'rept1-submit', array(
            'class' => 'col-xs-12',
          )); ?>
          <input type="submit" 
                 class="form-control btn btn-sm btn-primary col-xs-12" 
                 id="docsWithNotUniqueIndexes-submit"
                 value="Показати" />
        </div>
      </div>
      <?php
      $this->endWidget();
      ?>
      </div>
    </div>
  </div>