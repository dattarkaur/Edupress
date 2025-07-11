
//login logout
document.addEventListener("DOMContentLoaded", function () {
isLoggedIn = ajax_object.isLoggedIn;
let loginBtn = document.querySelector(".login-btn");
let signInBtn = document.querySelector(".sign-in-btn");
let logoutBtn = document.querySelector(".logout-btn");
val = ajax_object.val;
console.log('user login',isLoggedIn);
console.log(val);
if (isLoggedIn === "true") {  
    loginBtn.style.display = "none";
    signInBtn.style.display = "none";
    logoutBtn.style.display = "inline-block";
} else {
    loginBtn.style.display = "inline-block";
    signInBtn.style.display = "inline-block";
    logoutBtn.style.display = "none";
}
})


// search on nav bar
jQuery(document).ready(function($) {
    let selectedUrl = null;
    // AJAX on typing
    $('#navbar-search').on('input', function(e) {
        e.preventDefault();
        const keyword = $(this).val().trim();
        selectedUrl = null;
        if (keyword.length < 2) {
            $('#navbar-search-results').empty();
            return;
        }
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'navbar_page_search',
                query: keyword
            },
            success: function(response) {
                if (response.success && Array.isArray(response.data.message)) {
                    const pages = response.data.message;
                    $('#navbar-search-results').empty(); 
                    pages.forEach(function(page) {
                            $('#navbar-search-results').append(
                            `<div class="navbar-search-item" data-url="${page.link}">${page.title}</div>`
                            );
                    });
                    console.log(pages);
                }else {
                $('#navbar-search-results').html('<p>No results found.</p>');
                }
            }
        });
    });
    // Store selected result
    $(document).on('click', '.navbar-search-item', function() {
     selectedUrl = $(this).data('url');
     $('#navbar-search').val($(this).text());     
     $('#navbar-search-results').empty();
    });
    $(document).on('click', '#navbar-search-button', function(e) {   
        e.preventDefault(); 
        console.log("Search button clicked.");

        if (selectedUrl) {
            window.location.href = selectedUrl;
        } else {
        const keyword = $('#navbar-search').val().trim();
        if (keyword.length < 2) {
        alert("Type at least 2 characters.");
        return;
        }
        }
    });

   
      //blog+tax-pagination
      let currentPage =1;
        function fetch_blog_posts(page = 1) {
          let searchVal = $('#ajax-blogpost-search').val();
              let taxonomyVal = $('#taxonomy-select').val(); 
              let taxVal = $('#taxonomy-role').val(); 

                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'search_blog_posts_ajax',
                        search_blog: searchVal,
                        taxonomy: taxonomyVal, 
                        tax_role: taxVal, 
                        paged: page
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#ajax-blogpost-results').html(response.data.message);
                            $('#blog-message').html('');
                        }
                        else{
                            $('#ajax-blogpost-results').empty();
                            $('#blog-message').html(response.data.message);
                            console.log(response.data.message);
                        }
                    }
                });
            }
            $('#ajax-blogpost-search-btn').on('submit', function (e) {
                e.preventDefault();
                currentPage = 1;
                fetch_blog_posts(currentPage);
            });
             $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                const page = parseInt($(this).text()) || 1;
                fetch_blog_posts(page);
            });
   

   //blog+tax-pagination
      let currentP =1;
        function prac_blog_posts(pagee = 1) {
          let searchBal = $('#search-prac').val();
              let taxonomyPrac = $('#taxonomy-prac').val(); 
              let taxRole = $('#prac-role').val(); 
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'search_prac_posts',
                        searched: searchBal,
                        taxonomy_prac: taxonomyPrac, 
                        prac_role: taxRole, 
                        paged: pagee
                    },
                    success: function (response) {
                        if (response.success) {
             
                            $('#prac-blogpost-results').html(response.data.practice);
                            $('#practice').html('');
                        }
                        else{
                        
                            $('#prac-blogpost-results').empty();
                            $('#practice').html(response.data.practice);
                        }
                    }
                });
            }
            $('#search-button').on('click', function (e) {
                e.preventDefault();
                currentP = 1;
                prac_blog_posts(currentP);
            });
             $(document).on('click', '.prac_pagination a', function (e) {
                e.preventDefault();
                const pagee = parseInt($(this).text()) || 1;
                prac_blog_posts(pagee);
            });
   


    //fixed navbar
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 1) {
        $('.main-header').addClass("sticky");
        } else {
        $('.main-header').removeClass("sticky");
        }
    }); 




   //  blog ace slick-slider
        $('.blog-post-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            infinite: true,
            dots: true,
            autoplay: true,
            autoplaySpeed: 4000,
            arrows: false,
            responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1
                }
            }
            ]
        });
        
     // ace search bar
        $('#ajax-blog-search').on("keyup", function(e) {
        e.preventDefault();
          let search_word = $('#ajax-blog-search').val();
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'search_blog_posts',
                        search_word: search_word,  
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#ajax_post_result').html(response.data.ace_result);
                            $('.ace_result').html('');
                        }
                        else{
                            $('#ajax_post_result').html('');
                            $('.ace_result').html(response.data.ace_result);
                        }
                    }
                });
            })
});

