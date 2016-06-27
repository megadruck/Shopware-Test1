/*!
* Fine Uploader
*
* Copyright 2013-2014, Widen Enterprises, Inc. info@fineuploader.com
*
* Version: 5.0.3
*
* Homepage: http://fineuploader.com
*
* Repository: git://github.com/Widen/fine-uploader.git
*
* Licensed under GNU GPL v3, see LICENSE
*
* Third-party credits:
*   MegaPixImageModule (MIT)
*       https://github.com/stomita/ios-imagefile-megapixel
*       Copyright (c) 2012 Shinichi Tomita <shinichi.tomita@gmail.com>
*
*   CryptoJS
*       code.google.com/p/crypto-js/wiki/License
*       (c) 2009-2013 by Jeff Mott. All rights reserved.
*/ 


(function() {
    "use strict";
    var match = /(\{.*\})/.exec(document.body.innerHTML);
    if (match) {
        parent.postMessage(match[1], "*");
    }
}());
