
/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 07:51 21/01/14.
 */

if (!window.jQuery) {
    throw new Error('jQuery is needed to run Walrus.js');
}

var Walrus = {};

(function (Walrus) {

    // walrus is a ninja
    'use strict';

    Walrus.config = {};

    /**
     * Walrus.js constructor.
     *
     * attributes:
     *
     * ajaxNavigation: true|false
     * lazyLoad: true|false
     *
     * @param {Object} config
     */
    Walrus.init = function (config) {

        // default config
        if (!config.ajaxNavigation) {config.ajaxNavigation = false; }
        if (!config.pageContainer) {config.pageContainer = 'container'; }
        if (!config.lazyLoad) {config.lazyLoad = false; }
        Walrus.config = config;

        if (Walrus.config.ajaxNavigation) { Walrus.ajaxNavigationInit(); }

        Walrus.bootstrap();
    };

    Walrus.bootstrap = function () {
        if (Walrus.config.lazyLoad) { Walrus.checkLazy(); }
    };

    Walrus.ajaxNavigationInit = function () {

        $(document).click(Walrus.catchLinks);
        $(window).on("popstate", function (event) {
            if (event.originalEvent.state !== null) {
                Walrus.ajaxNavigation(event, location.href, true);
            }
        });
    };

    /**
     * Catch all clicked link.
     */
    Walrus.catchLinks = function (event) {

        var elem, $link, href;

        elem = event.target;

        if (elem.tagName !== 'A') {
            $link = $(elem).parents('a:first');
        } else {
            $link = $(elem);
        }

        if ($link[0] === undefined) { return; }

        href = $link.attr('href');

        if (Walrus.isntExternal(href)) {

            Walrus.ajaxNavigation(event, href);
            event.stopPropagation();
            event.preventDefault();
        } else { return; }
    };

    /**
     * Check if an url belongs to the current website.
     *
     * @param url
     * @returns {boolean}
     */
    Walrus.isntExternal = function (url) {
        var local = location.href.replace(/^((https?:)?\/\/)?(www\.)?(\.)?/gi, '').split('/')[0];
        url = url.replace(/^((https?:)?\/\/)?(www\.)?(\.)?/gi, '').split('/')[0];
        return local === (url === '' ? local : url);
    };

    /**
     * Handle the AJAX navigation.
     *
     * @param event
     * @param url
     */
    Walrus.ajaxNavigation = function (event, url, back) {

        $.ajax({
            url: url,
            dataType: 'html',
            async: false
        }).done(function (data) {
            var id,
                $data,
                $dataContainer,
                $currentContainer;

            id = Walrus.config.pageContainer;
            $data = $('<div></div>').html(data);

            $dataContainer = $data.find('#' + id);
            $currentContainer = $('#' + id);

            if ($dataContainer.length === 1 && $currentContainer.length === 1) {
                if (!back) { history.pushState({url: url}, '', url); }
                $currentContainer.html($dataContainer.html());
                document.title = $data.find('title:first').text();
            } else {
                console.log('bad content');
            }

            $(document).trigger('pageLoaded');

            Walrus.bootstrap();
            event.preventDefault();
        });
    };

    Walrus.checkLazy = function checkLazy() {
        var $nodes;

        // @TODO: find y data
        $nodes = $(document).find('[data-lazyload]');
        $nodes.each(function () {
            $(this).load($(this).data('lazyload'));
        });
    };

}(Walrus));

/** @TODO:
 * 2. lazyload
 * 3. compile
 * 4. long polling
 * 5. breadcrumb
 */

