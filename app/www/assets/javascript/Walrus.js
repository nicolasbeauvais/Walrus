
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
        if (!config.nolink) {config.nolink = false; }

        Walrus.config = config;

        Walrus.bootstrap();
    };

    Walrus.uload = function () {
        Walrus.bootstrapUload();
    };

    Walrus.bootstrap = function () {
        if (Walrus.config.ajaxNavigation) { Walrus.ajaxNavigationInit(); }
        if (Walrus.config.nolink) { Walrus.nolinkInit(); }
        if (Walrus.config.lazyLoad) { Walrus.checkLazy(); }
        $(window).on("popstate.WALRUS-popstate", function (event) {
            if (event.originalEvent.state !== null) {
                Walrus.ajaxNavigation(event, location.href, true);
            }
        });
    };

    Walrus.bootstrapUload = function () {
        $(window).off("popstate.WALRUS-popstate");
        $(document).off('click.WALRUS-ajaxnavigation');
        $('body').find('[data-nolink]').off('click.WALRUS-nolink');
    };

    Walrus.ajaxNavigationInit = function () {
        $(document).on('click.WALRUS-ajaxnavigation', Walrus.catchLinks);
    };

    Walrus.nolinkInit = function () {
        $('body').on('click.WALRUS-nolink', '[data-nolink]', function (event) {
            if (Walrus.isntExternal(atob($(this).data('nolink')))) {
                Walrus.ajaxNavigation(event, atob($(this).data('nolink')));
                event.stopPropagation();
                event.preventDefault();
            } else {
                location.href = atob($(this).data('nolink'));
            }
        });
    };

    /**
     * Launch the ajaxNavigation script with the specified url
     *
     * @param url the url to call
     * @param isBack if specified use the historic to go back
     * @param callback a callback executed at the end of the ajax request
     */
    Walrus.go = function (url, isBack, callback) {
        Walrus.ajaxNavigation(null, url, isBack, callback);
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
     * @param back
     * @param callback
     */
    Walrus.ajaxNavigation = function (event, url, back, callback) {

        $.ajax({
            url: url,
            dataType: 'html',
            async: false
        }).done(function (data) {
            var id,
                bread,
                $data,
                $dataContainer,
                $currentContainer;

            id = Walrus.config.pageContainer;
            $data = $('<div></div>').html(data) || null;

            $dataContainer = $data.find('#' + id);
            bread = $data.find('#breadcrumb').data();
            $data.find('#breadcrumb').remove();

            $currentContainer = $('#' + id);

            if ($dataContainer.length === 1 && $currentContainer.length === 1) {
                // browser history
                if (!back) { history.pushState({url: url}, '', url); }

                // update page content
                $currentContainer.html($dataContainer.html());
                document.title = $data.find('title:first').text();

                // breadcrumb
                if (Walrus.ajaxNavigationCallback) { Walrus.ajaxNavigationCallback(bread); }

                // callback
                if (callback) { callback(); }

                if (event) { event.preventDefault(); }
            } else {
                window.location = url;
            }
        });
    };

    /**
     * Save the breadCrumb callback.
     * It's easier to use it that way
     *
     * @type {boolean}
     */
    Walrus.ajaxNavigationCallback = false;

    /**
     * Setup a callback for the breadCrumb post ajax feature.
     *
     * @param callback
     */
    Walrus.breadCrumb = function (callback) {
        Walrus.ajaxNavigationCallback = callback;
    };

    /**
     * Search lazyload to execute
     */
    Walrus.checkLazy = function checkLazy() {
        var $nodes;

        $nodes = $(document).find('[data-lazyload]');
        $nodes.each(function () {
            $(this).load($(this).data('lazyload'));
        });
    };

    /**
     * Save long polling actions
     *
     * @type {{}}
     */
    Walrus.pollingAction = {};

    /**
     * Register a long polling action callback
     *
     * @param dataType
     * @param callback
     */
    Walrus.pollingRegister = function (dataType, callback) {
        Walrus.pollingAction[dataType] = callback;
    };

    /**
     * Handle automaticly the Walrus long polling
     *
     * @param url
     */
    Walrus.polling = function (url) {
        $.ajax({
            url: url,
            dataType: 'json'
        }).done(function (response) {
            var data,
                entity,
                content;

            if (!response.status || response.status !== 200
                    || Object.getOwnPropertyNames(Walrus.pollingAction).length === 0) {
                setTimeout(function () { Walrus.polling(url); }, 100);
                return;
            }

            data = response.data;
            for (entity in data) {
                if (data.hasOwnProperty(entity)) {
                    if (Walrus.pollingAction[entity]) {
                        content = Walrus.pollingAction[entity](data[entity]);
                        $(document).find('[data-poll="' + entity + '"').each(function () {
                            $(this).html(content + $(this).html());
                        });
                    }
                }
            }

            setTimeout(function () { Walrus.polling(url); }, 100);
        });
    };

    /**
     * Compile Walrus templating to html
     *
     * @param template
     * @param data
     * @returns {XML|string|void}
     */
    Walrus.compile = function (template, data) {
        var start   = "{{",
            end     = "}}",
            path    = "[a-z0-9_$][\\.a-z0-9_]*",
            pattern = new RegExp(start + "\\s*(" + path + ")\\s*" + end, "gi"),
            undef;

        return template.replace(pattern, function (tag, token) {
            var path = token.split("."),
                len = path.length,
                lookup = data,
                i = 0;

            for (i; i < len; i += 1) {
                lookup = lookup[path[i]];
                if (lookup === undef) { throw "Walrus: '" + path[i] + "' not found in " + tag; }
                if (i === len - 1) { return lookup; }
            }
        });
    };

}(Walrus));