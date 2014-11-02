/* JS */

var debounce = function(func, wait, immediate) {
  var timeout, result;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) result = func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) result = func.apply(context, args);
    return result;
  };
}
/* Navigation */
function pagesize(){
    if($(window).width() >= 765){
      $("body").removeClass("mobilescreen").addClass("bigscreen");
      $(".sidebar #nav").slideDown(350);
    }
    else{
      $("body").addClass("mobilescreen").removeClass("bigscreen");
      $(".sidebar #nav").slideUp(350); 
    }
}

function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5,
        border: '1px solid #ccc',
        padding: '2px 8px',
        color: '#ccc',
        'background-color': '#000',
        opacity: 0.9
    }).appendTo("body").fadeIn(200);
}
    
var TO = false;
$(document).ready(function(){
  pagesize();
  $(window).resize(debounce(pagesize,100));

  $(".content #nav a").on('click',function(e){
      if($(this).parents(".content:first").hasClass("enlarged")){
        e.preventDefault();
        return false;
      }
      if($(this).parent().hasClass("has_sub")) {
        e.preventDefault();
      }   

      if(!$(this).hasClass("subdrop")) {
        // hide any open menus and remove all other classes
        $("ul",$(this).parents("ul:first")).slideUp(350);
        $("a",$(this).parents("ul:first")).removeClass("subdrop");
        $("#nav .pull-right i").removeClass("fa-chevron-down").addClass("fa-chevron-left");
        
        // open our new menu and add the open class
        $(this).next("ul").slideDown(350);
        $(this).addClass("subdrop");
        $(".pull-right i",$(this).parents(".has_sub:last")).removeClass("fa-chevron-left").addClass("fa-chevron-down");
        $(".pull-right i",$(this).siblings("ul")).removeClass("fa-chevron-down").addClass("fa-chevron-left");
      }else if($(this).hasClass("subdrop")) {
        $(this).removeClass("subdrop");
        $(this).next("ul").slideUp(350);
        $(".pull-right i",$(this).parent()).removeClass("fa-chevron-down").addClass("fa-chevron-left");
        //$(".pull-right i",$(this).parents("ul:eq(1)")).removeClass("fa-chevron-down").addClass("fa-chevron-left");
      } 
  });
  $("#nav > li.has_sub > a.open").addClass("subdrop").next("ul").show();
  
  $(".menubutton").click(function(){
      if(!$(".content").hasClass("enlarged")){
          $("#nav .has_sub ul").removeAttr("style");
          $("#nav .has_sub .pull-right i").removeClass("fa-chevron-left").addClass("fa-chevron-down");
          $("#nav ul .has_sub .pull-right i").removeClass("fa-chevron-down").addClass("fa-chevron-right");
          $(".content").addClass("enlarged");
      }else{
          $("#nav .has_sub .pull-right i").addClass("fa-chevron-left").removeClass("fa-chevron-down").removeClass("fa-chevron-right");
          $(".content").removeClass("enlarged");
      }
  });
  
  $(".sidebar-dropdown a").on('click',function(e){
      e.preventDefault();

      if(!$(this).hasClass("open")) {
        // hide any open menus and remove all other classes
        $(".sidebar #nav").slideUp(350);
        $(".sidebar-dropdown a").removeClass("open");
        
        // open our new menu and add the open class
        $(".sidebar #nav").slideDown(350);
        $(this).addClass("open");
      }
      
      else if($(this).hasClass("open")) {
        $(this).removeClass("open");
        $(".sidebar #nav").slideUp(350);
      }
  });
  $('.sscroll').slimScroll({wheelStep: 1,opacity:0.3});
  $(".slimScrollBar").hide();
  
});

/* Widget close */

$('.wclose').click(function(e){
  e.preventDefault();
  var $wbox = $(this).parent().parent().parent();
  $wbox.hide(100);
});

/* Widget minimize */

  $('.wminimize').click(function(e){
    e.preventDefault();
    var $wcontent = $(this).parent().parent().next('.widget-content');
    if($wcontent.is(':visible')) 
    {
      $(this).children('i').removeClass('icon-chevron-up');
      $(this).children('i').addClass('icon-chevron-down');
    }
    else 
    {
      $(this).children('i').removeClass('icon-chevron-down');
      $(this).children('i').addClass('icon-chevron-up');
    }            
    $wcontent.slideToggle(300);
  }); 



/* Scroll to Top */
  $(".totop").hide();

  $(function(){
    $(window).scroll(function(){
      if ($(this).scrollTop()>300)
      {
        $('.totop').slideDown();
      } 
      else
      {
        $('.totop').slideUp();
      }
    });

    $('.totop a').click(function (e) {
      e.preventDefault();
      $('body,html').animate({scrollTop: 0}, 500);
    });

  });

function notyAlert(mensaje) {
      noty({
          text: mensaje,
          layout:'topRight',
          type:'alert',
          timeout:4000
      });
}

function notySuccess(mensaje) {
      noty({
          text: mensaje,
          layout:'topRight',
          type:'success',
          timeout:4000
      });
}

function notyError(mensaje) {
      noty({
          text: mensaje,
          layout:'topRight',
          type:'error',
          timeout:4000
      });
}

function notyWarning(mensaje) {
      noty({
          text: mensaje,
          layout:'bottom',
          type:'warning',
          timeout:4000
      });
}

function notyInfo(mensaje) {
      noty({
          text: mensaje,
          layout:'topRight',
          type:'information',
          timeout:2000
      });
}


/* Modal fix */

$('.modal').appendTo($('body'));

