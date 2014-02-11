
/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 07:51 21/01/14.
 */

var Walrus = {};

(function (Walrus) {

    // walrus is a ninja
    'use strict';

    Walrus.catchLinks = function (event) {

        var elem,
            attributes,
            attribute,
            treeExplorer,
            isLink;

        elem = event.srcElement;
        treeExplorer = 1;
        isLink = false;

        for (treeExplorer; treeExplorer > 0; treeExplorer -= 1) {

            //handle body, html, document
            if (elem === null) { break; }

            //if we found a link
            if (elem.localName === 'a') {

                attributes = elem.attributes;

                for (attribute in attributes) {
                    if (attributes.hasOwnProperty(attribute)) {

                        attribute = attributes[attribute];
                        if (attribute.localName === 'href') {
                            isLink = true;
                            if (Walrus.isntExternal(attribute.value)) {
                                Walrus.ajaxNavigation(event, attribute.value);
                                break;
                            } else { return; }

                        }
                        return;
                    }
                }

            } else {
                elem = elem.parentNode;
                treeExplorer += 1;
            }
        }

        if (isLink) { event.preventDefault(); }
    };

    Walrus.isntExternal = function (url) {
        var local = location.href.replace(/^((https?:)?\/\/)?(www\.)?(\.)?/gi, '').split('/')[0];
        url = url.replace(/^((https?:)?\/\/)?(www\.)?(\.)?/gi, '').split('/')[0];
        return local === (url === '' ? local : url);
    };

    Walrus.ajaxNavigation = function (event, url) {
        var request = new XMLHttpRequest();
        request.open('GET', url, true);
        request.send();

        request.onload = function () {
            var resp,
                node,
                container,
                allChilds,
                i;

            document.getElementById('container').innerHTML = '';

            resp = this.response;
            node = document.createElement('div');
            node.innerHTML = resp;

            document.title = node.getElementsByTagName('title')[0].innerText;
            window.history.pushState({"html": resp.innerHTML, "pageTitle": document.title}, '', url);

            allChilds = node.childNodes;
            i = allChilds.length - 1;

            for (i; i > 0; i -= 1) {
                if (allChilds[i].id && allChilds[i].id === "container") { container = allChilds[i]; }
            }

            document.getElementById('container').innerHTML = container.innerHTML;
            Walrus.eventTrigger(document, 'pageLoaded');

            Walrus.bootstrap();
            event.preventDefault();
        };
    };

    Walrus.checkLazy = function () {
        var nodes,
            nodesLength,
            request;

        nodes = Walrus.getByData('lazyload');
        nodesLength = nodes.length;

        if (nodesLength < 1) { return; }
        for (nodesLength; nodesLength > 0; nodesLength -= 1) {
            request = new XMLHttpRequest();
            request.open('GET', nodes[nodesLength - 1].data, true);
            request.send();

            request.onload = Walrus.appendLazy(nodes[nodesLength - 1].elem);
        }
    };

    Walrus.appendLazy = function (node) {

        var callback = function () {
            var data = this.response;
            node.innerHTML = data;
            node.removeAttribute('data-lazyload');
        };

        return callback;
    };

    Walrus.getByData = function (data, value) {
        var matches,
            allDom,
            i,
            node,
            nodeData;

        matches = [];
        allDom = document.getElementsByTagName("*");
        i = allDom.length - 1;

        for (i; i > 0; i -= 1) {
            node = allDom[i];
            if (node.getAttribute('data-' + data)) {
                nodeData = {};
                nodeData.elem = node;
                nodeData.data = node.getAttribute('data-' + data);

                if (!value || nodeData.data === value) {
                    matches.push(nodeData);
                }
            }
        }
        return matches;
    };

    Walrus.eventTrigger = function (element, eventName) {
        var event;

        if (document.createEvent) {
            event = document.createEvent("HTMLEvents");
            event.initEvent(eventName, true, true);
        } else {
            event = document.createEventObject();
            event.eventType = eventName;
        }

        event.eventName = eventName;

        if (document.createEvent) {
            element.dispatchEvent(event);
        } else {
            element.fireEvent("on" + event.eventType, event);
        }
    };

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

        Walrus.config = config;

        if (Walrus.config.ajaxNavigation) { document.onclick = Walrus.catchLinks; }

        window.onload = Walrus.bootstrap;
    };

    Walrus.bootstrap = function () {
        if (Walrus.config.lazyLoad) { Walrus.checkLazy(); }
    };

    Walrus.pollingAction = {};

    Walrus.pollingRegister = function (dataType, callback) {
        Walrus.pollingAction[dataType] = callback;
    };

    Walrus.polling = function (url) {
        var request = new XMLHttpRequest();
        request.open('GET', url, true);
        request.send();

        request.onload = function () {
            var resp,
                entity,
                data,
                tpl,
                nodes,
                nodesLength;

            resp = JSON.parse(this.response);

            if (resp.status !== 200 || !resp.data || Object.getOwnPropertyNames(Walrus.pollingAction).length === 0) {
                setTimeout(function () { Walrus.polling(url); }, 100);
                return;
            }

            data = resp.data;

            for (entity in data) {
                if (data.hasOwnProperty(entity)) {
                    if (Walrus.pollingAction[entity]) {
                        tpl = Walrus.pollingAction[entity](data[entity]);
                        nodes = Walrus.getByData('poll', entity);
                        nodesLength = nodes.length;

                        if (nodesLength < 1) { return; }
                        for (nodesLength; nodesLength > 0; nodesLength -= 1) {
                            nodes[nodesLength - 1].elem.innerHTML = tpl + nodes[nodesLength - 1].elem.innerHTML;
                        }
                    }
                }
            }

            setTimeout(function () { Walrus.polling(url); }, 100);
        };
    };

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
