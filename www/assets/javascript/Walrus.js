
/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 07:51 21/01/14.
 */

var Walrus = {};

(function (Walrus) {

    'use strict';

    Walrus.catchLinks = function (event) {

        var elem,
            attributes,
            attribute,
            treeExplorer;

        elem = event.srcElement;
        treeExplorer = 1;

        for (treeExplorer; treeExplorer > 0; treeExplorer -= 1) {//fake faster while

            //handle body, html, document
            if (elem === null) { break; }

            //if we found a link
            if (elem.localName === 'a') {

                attributes = elem.attributes;

                for (attribute in attributes) {
                    if (attributes.hasOwnProperty(attribute)) {

                        attribute = attributes[attribute];
                        if (attribute.localName === 'href') {
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

        event.preventDefault();
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
            window.history.pushState({"html": resp.innerHTML, "pageTitle": document.title}, '', document.URL += url);

            allChilds = node.childNodes;
            i = allChilds.length - 1;

            for (i; i > 0; i -= 1) {
                if (allChilds[i].id && allChilds[i].id === "container") { container = allChilds[i]; }
            }

            document.getElementById('container').innerHTML = container.innerHTML;
            event.preventDefault();
        };
    };

    //Event Listener
    document.onclick = Walrus.catchLinks;

})(Walrus);