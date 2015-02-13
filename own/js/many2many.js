/*!
Math.uuid.js (v1.4)
http://www.broofa.com
mailto:robert@broofa.com

Copyright (c) 2010 Robert Kieffer
Dual licensed under the MIT and GPL licenses.
*/

/*
 * Generate a random uuid.
 *
 * USAGE: Math.uuid(length, radix)
 *   length - the desired number of characters
 *   radix  - the number of allowable values for each character.
 *
 * EXAMPLES:
 *   // No arguments  - returns RFC4122, version 4 ID
 *   >>> Math.uuid()
 *   "92329D39-6F5C-4520-ABFC-AAB64544E172"
 *
 *   // One argument - returns ID of the specified length
 *   >>> Math.uuid(15)     // 15 character ID (default base=62)
 *   "VcydxgltxrVZSTV"
 *
 *   // Two arguments - returns ID of the specified length, and radix. (Radix must be <= 62)
 *   >>> Math.uuid(8, 2)  // 8 character ID (base=2)
 *   "01001010"
 *   >>> Math.uuid(8, 10) // 8 character ID (base=10)
 *   "47473046"
 *   >>> Math.uuid(8, 16) // 8 character ID (base=16)
 *   "098F4D35"
 */
(function() {
  // Private array of chars to use
  var CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');

  Math.uuid = function (len, radix) {
    var chars = CHARS, uuid = [], i;
    radix = radix || chars.length;

    if (len) {
      // Compact form
      for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random()*radix];
    } else {
      // rfc4122, version 4 form
      var r;

      // rfc4122 requires these characters
      uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
      uuid[14] = '4';

      // Fill in random data.  At i==19 set the high bits of clock sequence as
      // per rfc4122, sec. 4.1.5
      for (i = 0; i < 36; i++) {
        if (!uuid[i]) {
          r = 0 | Math.random()*16;
          uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
        }
      }
    }

    return uuid.join('');
  };

  // A more performant, but slightly bulkier, RFC4122v4 solution.  We boost performance
  // by minimizing calls to random()
  Math.uuidFast = function() {
    var chars = CHARS, uuid = new Array(36), rnd=0, r;
    for (var i = 0; i < 36; i++) {
      if (i==8 || i==13 ||  i==18 || i==23) {
        uuid[i] = '-';
      } else if (i==14) {
        uuid[i] = '4';
      } else {
        if (rnd <= 0x02) rnd = 0x2000000 + (Math.random()*0x1000000)|0;
        r = rnd & 0xf;
        rnd = rnd >> 4;
        uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
      }
    }
    return uuid.join('');
  };

  // A more compact, but less performant, RFC4122v4 solution:
  Math.uuidCompact = function() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
      return v.toString(16);
    });
  };
})();


//--------------------OWN -----OWN---------
//--------------------OWN -----OWN---------
//--------------------OWN -----OWN---------
//--------------------OWN -----OWN---------
//--------------------OWN -----OWN---------
//--------------------OWN -----OWN---------


function Many2Many(input_field,left_area,right_area,add_all_tool,add_text_tool,
  ids_nm,names_nm,
  echo_classname,
  search_url){
  
  this.$input_field = input_field;
  this.$left_area = left_area;
  this.$right_area = right_area;
  this.$add_all_tool = add_all_tool;
  this.$add_text_tool = add_text_tool;
  this.timerCheckCount = 0;
  this.timer = undefined;
  this.lastValue = "";
  this.ids_nm = ids_nm;
  this.names_nm = names_nm;
  this.echo_classname = echo_classname;
  this.search_url = search_url;
  
  var _self = this;  
  
  this.checkInputChange = function () {
    this.timerCheckCount += 1;
    if (this.lastValue !== this.$input_field.val()) {
      var without = [];
      
      $("#"+_self.$right_area.attr('id')+" .CtrLink .CtrSubBullet").each(function(){
        var dom_id = $(this).parent().attr('id');
        var ctr_id = $("#"+dom_id+" .CtrId input").val();
        //if (ctr_id > 0){
          without[without.length] = ctr_id;
        //}
      });
      $.ajax({
        type: 'GET',
        url: _self.search_url,
        async: true,
        cache: false,
        data: {q: _self.$input_field.val(), n_ids: JSON.stringify(without)},
        success: function(data){
          var receive = JSON.parse(data);
          _self.$left_area.html("");
          if (receive.length === 0){
            $("."+_self.echo_classname).stop(true, true).fadeIn(0).html(
              "Нічого не знайдено"
            ).fadeOut(2000);
            return false;
          }
          $("."+_self.echo_classname).stop(true, true).fadeIn(0).html(
            "Знайдено записів: "+receive.length
          ).fadeOut(2000);
          for (var i = 0; i < receive.length; i++){
            var append_html = 
             '<div class="CtrLink" id="'+receive[i].id+'_'+_self.$right_area.attr('id')+'">'
             +'<a href="#" class="CtrAddBullet" >'
             +'[+]</a> '
             +'<span class="CtrId">'
             +receive[i].id
             +'</span> '
             +'<span class="CtrName">'
             +receive[i].text
             +'</span>'
             +'</div>';
            _self.$left_area.append(append_html);
            
            $("#"+_self.$left_area.attr('id')+" .CtrLink .CtrAddBullet").click(function (){
              _self.moveItemToR($(this));
              return false;
            });
          }
        },
        error: function(jXHR,txt){
          $("."+_self.echo_classname).stop(true, true).fadeIn(0).html(
            txt
          ).fadeOut(2000);
        }
      });
      this.lastValue = this.$input_field.val();
    }
  }
  
  this.startTimer = function () {
    this.timer = setInterval(function(){_self.checkInputChange()}, 200); // (1/5 sec)
  }
  
  this.endTimer = function endTimer() {
    clearInterval(this.timer);
    this.timerCheckCount = 0;
  }

  this.$add_all_tool.click(function(){
    $("#"+_self.$left_area.attr('id')+" .CtrLink .CtrAddBullet").each(function(elem){
      _self.moveItemToR($(this));
    });
    return false;
  });
  
  this.$add_text_tool.click(function(){
    var text_value = _self.$input_field.val().replace(/\"/g,"“").replace(/\'/g,"“");
    var id_add = Math.uuid();
    if (text_value.length === 0){
      return false;
    }
    if ($("#"+_self.$right_area.attr('id')+" .CtrLink .CtrName.NoFlow input[value='"+text_value+"']").length !== 0){
      return false;
    }
    var append_html = 
     '<div class="CtrLink" id="'+id_add+'">'
     +'<a href="#" class="CtrSubBulletNoFlow" onclick="$(this).parent().remove();return false;">[-]</a> '
     +'<span class="CtrId">'
     +'<input type="hidden" name="'+_self.ids_nm+'[]" value="-1" />'
     +'</span> '
     +'<span class="CtrName NoFlow">'
     +'<input type="hidden" name="'+_self.names_nm+'[]" value="'+text_value+'" />'
     +'<input type="text" name="'+_self.names_nm+'_comment[]" value="" class="'+_self.names_nm+'_comment" />'
     +text_value
     +'</span>'
     +'</div>';
     if ($("#"+id_add).length === 0){
      _self.$right_area.append(append_html);
    }
    return false;
  });
  
  this.$input_field.focus(function() {
      // turn on timer
      _self.startTimer();
      $("."+_self.echo_classname).stop(true, true).fadeIn(0).html('далі...').fadeOut(2000);
  }).blur(function() {
      // turn off timer
      _self.endTimer();
  });

  this.moveItemToR = function (_bullet) {
    var dom_id = _bullet.parent().attr('id');
    var dom_id_parent_id = $("#"+dom_id).parent().attr('id');
    var bullet_class = _bullet.attr('class');
    var bullet_text = _bullet.text();
    if (bullet_class === 'CtrAddBullet'){
      var ctr_id = $("#"+dom_id+" .CtrId").text();
      var ctr_name = $("#"+dom_id+" .CtrName").text();
      $("#"+dom_id+" .CtrId").html("");
      $("#"+dom_id+" .CtrName").html("");
      $("#"+dom_id+" .CtrId").append("<input type='hidden' "
        +"name='"+_self.ids_nm+"[]' "
        +"value='"+Many2Many.prototype.trim1(ctr_id)+"' />");
      $("#"+dom_id+" .CtrName").append("<input type='hidden' "
        +"name='"+_self.names_nm+"[]' "
        +"value='"+Many2Many.prototype.trim1(ctr_name)+"' />");
      $("#"+dom_id+" .CtrName").append("<input type='text' "
        +"name='"+_self.names_nm+"_comment[]' class='"+_self.names_nm+"_comment' "
        +"value='' />");
      $("#"+dom_id+" .CtrName").append(ctr_name);
      _bullet.text("[-]");
      _bullet.attr('class','CtrSubBullet');
      
      if (ctr_id < 0){
        $("#"+dom_id).remove();
      } else {
        $("#"+dom_id).appendTo("#"+_self.$right_area.attr('id'));
      }
        
      _bullet.click(function(){
        _self.moveItemToL(_bullet);
        return false;
      });
      return false;
    } 
  };
  
  this.moveItemToL = function (_bullet) {
    var dom_id = _bullet.parent().attr('id');
    var dom_id_parent_id = $("#"+dom_id).parent().attr('id');
    var bullet_class = _bullet.attr('class');
    var bullet_text = _bullet.text();
    if (bullet_class === 'CtrSubBullet'){
      var ctr_id = $("#"+dom_id+" .CtrId input").val();
      var ctr_name = $("#"+dom_id+" .CtrName input").val();
      $("#"+dom_id+" .CtrId").text(ctr_id);
      $("#"+dom_id+" .CtrName").text(ctr_name);
      _bullet.attr('class','CtrAddBullet');
      _bullet.text("[+]");
    }
    if (ctr_id < 0){
      $("#"+dom_id).remove();
    } else {
      $("#"+dom_id).appendTo("#"+_self.$left_area.attr('id'));
    }
    
    _bullet.click(function(){
      _self.moveItemToR(_bullet);
      return false;
    });
    return false;
  };
}

Many2Many.prototype.trim1 = function(str) {
  return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

