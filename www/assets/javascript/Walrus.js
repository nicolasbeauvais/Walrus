
/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
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
            if (elem === null) { break;}

            //if we found a link
            if (elem.localName === 'a') {

                attributes = elem.attributes;

                for (attribute in attributes) {
                    if (attributes.hasOwnProperty(attribute)) {

                        attribute = attributes[attribute];
                        if (attribute.localName === 'href') {
                            console.log(attribute.value);
                        }
                    }
                }

             } else {
                elem = elem.parentNode;
                treeExplorer += 1;
            }
        }


        event.preventDefault();
    };

    //Event Listener
    document.onclick = Walrus.catchLinks;

})(Walrus);