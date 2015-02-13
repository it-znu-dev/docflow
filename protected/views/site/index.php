<?php
/* @var $this SiteController */
/* @var $model LoginForm */
$this->pageTitle=Yii::app()->name;
?>

<script type="text/javascript">
  $(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto({
      allow_resize: true, 
      default_height: '95%', 
      default_width: '95%', 
      social_tools: "",
      });
  });
</script>

<div class="dfbox">
  <h2 style="text-align: center;">Вітаємо в системі "<?php echo CHtml::encode(Yii::app()->name); ?>"</h2>
  <p style="text-align: center;">Запорізький національний університет</p>
  <?php
    $this->renderPartial("login",array('model'=>$model));
  ?>
  <div style="text-align: center; margin-top: 15px;">
  <a class="btn btn-info btn-lg" 
    href="<?php echo Yii::app()->request->baseUrl; ?>/docflow.pdf?iframe=true" 
    role="button" 
    rel="prettyPhoto[iframes]">
      Інформація про документообіг
  </a>
  </div>
</div>
<?php

?>