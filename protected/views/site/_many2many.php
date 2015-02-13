<?php
  /* @var $fieldIdName string */
  /* @var $fieldInfoName string */
  /* @var $ajaxUrl string */
  /* @var $relatedModels mixed */
  /* @var $foreignId string */
  /* @var $_title string */
  /* @var $ajaxGroupUrl string */

    $uid = uniqid();
    ?>
<script type="text/javascript">
  $(function(){
    var var_<?php echo $uid; ?> = new Many2Many(
      $('#<?php echo 'search_'.$uid; ?>'),$('#<?php echo 'found_area_'.$uid; ?>'),
      $('#<?php echo 'added_area_'.$uid; ?>'),$('#<?php echo 'add_all_'.$uid; ?>'),
      $('#_AddText_hidden_<?php echo $uid; ?>'),
      "<?php echo $fieldIdName; ?>",
      "<?php echo $fieldInfoName; ?>",
      "<?php echo 'msg_class_'.$uid; ?>",
      '<?php echo $ajaxUrl; ?>'
      );
    var added_area = "#<?php echo 'added_area_'.$uid; ?>";
    var found_area = "#<?php echo 'found_area_'.$uid; ?>";
      
    $("#<?php echo 'added_area_'.$uid; ?> .CtrLink .CtrSubBullet").click(function (){
      var_<?php echo $uid; ?>.moveItemToL($(this));
      return false;
    });
    
<?php 
if (strlen(trim($ajaxGroupUrl)) > 0){
  $js_added_area_html = uniqid("added_area_html_");
?>
    var sel = "#group-<?php echo $uid; ?>";
    var sel_link = "#group-click-<?php echo $uid; ?>";
    var sel_link_id = "group-click-<?php echo $uid; ?>";
    var wait_id = "wait-<?php echo $uid; ?>";
    
    var group_ajax = function(resp_data){
      var dat = ((typeof(resp_data) !== "object")? {} : resp_data) ;
      var without = [];
      $(added_area+" .CtrLink .CtrSubBullet").each(function(){
        var dom_id = $(this).parent().attr('id');
        var ctr_id = $("#"+dom_id+" .CtrId input").val();
        without[without.length] = ctr_id;
      });
      dat.n_ids = JSON.stringify(without);
      $.ajax({
        type: 'GET',
        url: '<?php echo $ajaxGroupUrl; ?>',
        async: true,
        cache: false,
        dataType: 'json',
        data: dat,
        beforeSend: function() {
          if ($(sel).length > 0){
            $(sel).after("<div id='"+wait_id+"'>зачекайте...</div>");
            $(sel).remove();
          }
        },
        error: function(x,e){
          alert(e);
          if ($("#"+wait_id).length > 0){
            $("#"+wait_id).text("Помилка: "+e+" - перезавантажте сторінку");
          }
        },
        fail: function(x,e){
          alert(e);
          if ($("#"+wait_id).length > 0){
            $("#"+wait_id).text("Помилка: "+e+" - перезавантажте сторінку");
          }
        },
        success: function(req){
          if ($("#"+wait_id).length > 0){
            $("#"+wait_id).after("<a href='#' id='"+sel_link_id+"' style='display: block; width: 100%;'>[+] група</a>");
            $(sel_link).click(function(){
              group_ajax({});
              return false;
            });
            $("#"+wait_id).remove();
          }
          if (typeof(req.items) != "undefined"){
            for (var i = 0; i < req.items.length; i++){
              var ctrLinkId = Math.uuid();
              var append_html = '<div class="CtrLink GroupItem" id="'+ctrLinkId+'" >'
                  +'<a href="#" class="CtrAddBullet" '
                  //  +' onclick="$(\'#'+ctrLinkId+'\').remove();return false;"'
                  +'>'
                  +'[+]'
                  +'</a> '
                  +'<span class="CtrId">'
                    +req.items[i].id
                  +'</span>'
                  +'<span class="CtrName">'
                    +req.items[i].text
                  +'</span>'
                +'</div>';
              $(found_area).append(
               append_html
              );
            }
            $(found_area+" .CtrLink.GroupItem .CtrAddBullet").each(function(){
              var_<?php echo $uid; ?>.moveItemToR($(this));
            });
          } else {
            $(sel_link).after("<select id='group-<?php echo $uid; ?>' style='display: block; width: 100%;'></select>");
            $(sel_link).remove();
            for (var i = 0; i < req.options.length; i++){
              $(sel).append("<option value='"+req.options[i].id+"'>"
                      +req.options[i].text
                      +"</option>");
            }
            $(sel).change(function(){
              var _id = $("#group-<?php echo $uid; ?> option:selected").val();
              group_ajax({id: _id});
              $(found_area).html("");
            });
          }
        }
      });
    };
    
    $(sel_link).click(function(){
      group_ajax({});
      return false;
    });
    
    $(added_area).bind("DOMSubtreeModified",function(){
      if ($(sel).length > 0){
        $(sel).after("<a href='#' id='"+sel_link_id+"' style='display: block; width: 100%;'>[+] група</a>");
        $(sel_link).click(function(){
          group_ajax({});
          return false;
        });
        $(sel).remove();
      }
      return false;
    });
    
<?php 
}
?>
  });
</script>

        <label for="<?php echo 'search_'.$uid; ?>>"><?php echo $_title; ?></label>
        <div class="row row-nomargins">
          <div class="col-xs-8 ">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1-<?php echo $uid; ?>" >
              <span class="glyphicon glyphicon-search"></span>
              </span>
              <input type="text" id="<?php echo 'search_'.$uid; ?>" class="form-control"  />
              <span class="input-group-addon" id="basic-addon2-<?php echo $uid; ?>"
              style="<?php 
                echo ((Yii::app()->controller->uniqueID == "events")? "":"display:none;"); 
                ?>">
              <a href="#" id="_AddText_hidden_<?php echo $uid; ?>" title="Додати">
                <span class="label label-success">+</span>
              </a>
              </span>
            </div>
          </div>
          <div class="col-xs-4">
            <span class="<?php echo 'msg_class_'.$uid; ?>"></span>
          </div>
        </div>
        <div class="row row-nomargins">
          <div class="col-xs-6">
            <div class="dfheader">Запропоновані
            <a href="#" id="<?php echo 'add_all_'.$uid; ?>" 
               title="Перенесті усі"><span class="label label-info">&gt;&gt;</span>
            </a>
            </div>
            <div id = "<?php echo 'found_area_'.$uid; ?>" class="found-area">
            </div>
          </div>
          <div class="col-xs-6">
            <div class="dfheader">
            <?php 
            if (strlen(trim($ajaxGroupUrl)) > 0){
              ?>
              <a href='#' id="group-click-<?php echo $uid; ?>" style='display: block; width: 100%;'>[+] група</a>
            <?php } else { echo "Обрані"; } ?>
            </div>

            <div id = "<?php echo 'added_area_'.$uid; ?>" class="added-area">
            <?php
            for ($i = 0; ($i < count($relatedModels) && is_array($relatedModels)) ; $i++){
              ?>
              <div class="CtrLink" id="<?php 
                echo uniqid(); ?>">
                <a href="#" class="CtrSubBullet<?php 
                  echo (($relatedModels[$i][$foreignId] < 0)? " CtrSubBulletNoFlow":""); ?>"
                >[-]</a> 
                <span class="CtrId">
                  <input type="hidden" name="<?php echo $fieldIdName; ?>[]" 
                    value="<?php echo $relatedModels[$i][$foreignId]; ?>" />
                  <input type="hidden" name="<?php echo $fieldInfoName; ?>[]" 
                    value="<?php echo $relatedModels[$i][$fieldInfoName]; ?>" />
                </span>
                <span class="CtrName">
                  <?php if (Yii::app()->controller->uniqueID == "events" 
                    && $fieldInfoName == "InvitedComment"){ ?>
                  <input type="text" name="<?php echo $fieldInfoName; ?>_comment[]" 
                    class="<?php echo $fieldInfoName; ?>_comment" 
                    value="<?php echo $relatedModels[$i]["Seets"]; ?>" />                    
                  <?php } ?>

                  <?php echo $relatedModels[$i][$fieldInfoName]; ?>
                </span>
              </div>
              <?php
            }
            ?>
            </div>
          </div>
        </div>
