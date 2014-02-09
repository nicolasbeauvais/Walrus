Walrus.init({
    ajaxNavigation: true,
    lazyLoad: true
});

$('#post').click(function () {
    $('#post-pop').toggleClass('expand');
});

$('#post-pop form').submit(function (e) {
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function () {
            $('#post-pop').toggleClass('expand');
            var node = $('#userBar').find('.infos .stat:first .value');
            node[0].innerHTML = parseInt(node[0].innerHTML) + 1;
        }
    });

    e.preventDefault();
    e.stopPropagation();
    return false;
});


var polling = {};

function poll() {
    polling = $.ajax({
        type: "POST",
        url: 'api/polling/run/'
    });

    polling.done(function (data) {
        if (data.status === 200) {
            data = data.data;

            if (!data) { return; }
            if (data.posts) {
                console.log(data.posts);
            }
        }

        setTimeout(poll, 1000);
    });
}

poll();