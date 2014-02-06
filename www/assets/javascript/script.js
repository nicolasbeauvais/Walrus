Walrus.init({
    ajaxNavigation: true,
    lazyLoad: true
});

$('#post').click(function () {
    $('#post-pop').toggleClass('expand');
});

$('#post-pop form').submit(function () {
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function () {
            $('#post-pop').toggleClass('expand');
        }
    });

    return false;
});