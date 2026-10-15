document.addEventListener("DOMContentLoaded", function(){
  window.addEventListener('scroll', function() {
      if (window.scrollY > 450) {
      	$('.sticky').addClass('fixed-top');
        // navbar_height = document.querySelector('.navbar').offsetHeight;
        // document.body.style.paddingTop = navbar_height + 'px';
      } else {
        $('.sticky').removeClass('fixed-top');
        document.body.style.paddingTop = '0';
      } 
  });
});