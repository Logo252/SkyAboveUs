$(window).load(function() {

    $('.image-container').each(function (i , obj){

        var boxheight = $(this).children('.picture-image').innerHeight();
        $(this).children('.picture-text').outerHeight(boxheight);
    });
});
