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
    return false;
});

Walrus.pollingRegister('posts', function (data) {
    var tpl = '', item;
    for (item in data) {
        tpl += Walrus.compile(document.getElementById('templating-msg').innerHTML, data[item]);
    }
    return tpl;
});
Walrus.polling('api/polling/run');
