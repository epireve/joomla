(function($) {
  $.jomSelect = {
    options: {
      selectClass:   'selector',
      focusClass: 'focus',
      activeClass: 'active',
      hoverClass: 'hover',
      useID: true,
      idPrefix: 'jomSelect',
      resetSelector: false,
      autoHide: true,
      spanClass: 'attend'
    },
    elements: []
  };

  if($.browser.msie && $.browser.version < 7){
    $.support.selectOpacity = false;
  }else{
    $.support.selectOpacity = true;
  }

  $.fn.jomSelect = function(options) {

    options = $.extend($.jomSelect.options, options);

    var el = this;
    //code for specifying a reset button
    if(options.resetSelector != false){
      $(options.resetSelector).mouseup(function(){
        function resetThis(){
          $.jomSelect.update(el);
        }
        setTimeout(resetThis, 10);
      });
    }
    
    function doSelect(elem){
      var $el = $(elem);

      var divTag = $('<div />'),
          spanTag = $('<span />');
      
      if(!$el.css("display") == "none" && options.autoHide){
        divTag.hide();
      }

      divTag.addClass(options.selectClass);

      if(options.useID && elem.attr("id") != ""){
        divTag.attr("id", options.idPrefix+"-"+elem.attr("id"));
      }
      
      var selected = elem.find(":selected:first");
      if(selected.length != 0){
          options.spanClass = selected.prop('class');
      }
      if(selected.length == 0){
        selected = elem.find("option:first");
      }
      spanTag.html(selected.html());
      
      elem.css('opacity', 0);
      elem.wrap(divTag);
      elem.before(spanTag);

      //redefine variables
      divTag = elem.parent("div");
      spanTag = elem.siblings("span");

      elem.bind({
        "change.jomSelect": function() {
          spanTag.text(elem.find(":selected").html());
          divTag.removeClass(options.activeClass);
        },
        "focus.jomSelect": function() {
          divTag.addClass(options.focusClass);
        },
        "blur.jomSelect": function() {
          divTag.removeClass(options.focusClass);
          divTag.removeClass(options.activeClass);
        },
        "mousedown.jomSelect touchbegin.jomSelect": function() {
          divTag.addClass(options.activeClass);
        },
        "mouseup.jomSelect touchend.jomSelect": function() {
          divTag.removeClass(options.activeClass);
        },
        "click.jomSelect touchend.jomSelect": function(){
          divTag.removeClass(options.activeClass);
        },
        "mouseenter.jomSelect": function() {
          divTag.addClass(options.hoverClass);
        },
        "mouseleave.jomSelect": function() {
          divTag.removeClass(options.hoverClass);
          divTag.removeClass(options.activeClass);
        },
        "keyup.jomSelect": function(){
          spanTag.text(elem.find(":selected").html());
        }
      });
      
      //handle disabled state
      if($(elem).attr("disabled")){
        //box is checked by default, check our box
        divTag.addClass(options.disabledClass);
      }
      $.jomSelect.noSelect(spanTag);
      
      storeElement(elem);

    }

    function storeElement(elem){
      //store this element in our global array
      elem = $(elem).get();
      if(elem.length > 1){
        $.each(elem, function(i, val){
          $.jomSelect.elements.push(val);
        });
      }else{
        $.jomSelect.elements.push(elem);
      }
    }
    
    //noSelect v1.0
    $.jomSelect.noSelect = function(elem) {
      function f() {
       return false;
      };
      $(elem).each(function() {
       this.onselectstart = this.ondragstart = f; // Webkit & IE
       $(this)
        .mousedown(f) // Webkit & Opera
        .css({ MozUserSelect: 'none' }) // Firefox
        .addClass(options.spanClass); //Add class for span
      });
     };

   return this.each(function() {
      if($.support.selectOpacity){
        var elem = $(this);

        if(elem.is("select")){
          //element is a select
          if(elem.attr("multiple") != true){
            //element is not a multi-select
            if(elem.attr("size") == undefined || elem.attr("size") <= 1){
              doSelect(elem);
            }
          }
        }          
      }
    });
  };
})(joms.jQuery);