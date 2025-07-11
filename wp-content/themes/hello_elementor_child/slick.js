add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');
function enqueue_slick_slider_assets() {
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);

   wp_add_inline_script('slick-js', "
        jQuery(document).ready(function($) {
          if ($(window).width() > 768) {    
            $('.testimonial-wrapper').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 300,
                slidesToShow: 1
            });
          }
        });
    ");
}
