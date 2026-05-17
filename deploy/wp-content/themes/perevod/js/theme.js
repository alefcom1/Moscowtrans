jQuery(function ($) {
  $('.citys li a').click(function () {
    $('.citys li.is-active').each(function () {
      $(this).removeClass('is-active');
    });
    $('.conaddr div').each(function () {
      $(this).hide();
    });
    $('.phone-link').each(function () {
      $(this).hide();
    });
    $(this).parent().addClass('is-active');
    $('.langmenu .h6-style').text($(this).text());
    $('.conaddr div[contact="' + $(this).attr('data-cont') + '"]').show();
    $('.phone-link[contact="' + $(this).attr('data-cont') + '"]').show();
    $('.langmenu222.is-open').removeClass('is-open');

    return false;
  });

  $('.h6-style').click(function () {
    if ($(this).attr('data-open') == 'yes') {
      $('.citys').css('z-index', '0');
      $('.citys').css('display', 'none');
      $(this).attr('data-open', 'no');
    } else {
      $('.citys').css('z-index', '100');
      $('.citys').css('display', 'block');
      $(this).attr('data-open', 'yes');
    }
  });
  $('.citys li a').click(function () {
    console.log('123');
    $('.citys').css('z-index', '0');
    $('.citys').css('display', 'none');
    $('.h6-style').attr('data-open', 'no');
  });

  $('.citys li a').click(function () {
    $('.citys li.is-active').each(function () {
      $(this).removeClass('is-active');
    });
    $('.conaddr div').each(function () {
      $(this).hide();
    });
    $('.phone-link').each(function () {
      $(this).hide();
    });
    $(this).parent().addClass('is-active');
    $('.langmenu .h6-style').html('<span>' + $(this).text() + '</span>');
    $('.conaddr div[contact="' + $(this).attr('data-cont') + '"]').show();
    $('.phone-link[contact="' + $(this).attr('data-cont') + '"]').show();
    $('.langmenu222.is-open').removeClass('is-open');
    $('.header-top-right').css('display', 'flex');

    return false;
  });
  $('.langmenu .h6-style').html('<span>' + 'Москва Химки' + '</span>');

  // slick
  $('.custom-reviews').slick({
    dots: true,
    infinite: true,
    slidesToShow: 2,
    slidesToScroll: 2,
    arrows: true,
    loop: true,
    responsive: [
      {
        breakpoint: 800,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: false,
          arrows: false,
        },
      },
    ],
  });
});

// lazyload scripts
function lazyCode(lazyCode) {
  'use strict';
  var loadedScripts = false,
    timerId;

  window.addEventListener('scroll', loadScripts, { passive: true });
  window.addEventListener('touchstart', loadScripts);
  document.addEventListener('mouseenter', loadScripts);
  document.addEventListener('click', loadScripts);

  document.addEventListener('DOMContentLoaded', loadFallback);

  function loadFallback() {
    timerId = setTimeout(loadScripts, 4000);
  }

  function loadScripts(e) {
    if (loadedScripts) {
      return;
    }
    lazyCode();
    loadedScripts = true;
    clearTimeout(timerId);
    window.removeEventListener('scroll', loadScripts);
    window.removeEventListener('touchstart', loadScripts);
    document.removeEventListener('mouseenter', loadScripts);
    document.removeEventListener('click', loadScripts);
    document.removeEventListener('DOMContentLoaded', loadFallback);
  }
}
