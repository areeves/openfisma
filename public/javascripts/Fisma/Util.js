/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify it under the terms of the GNU General Public 
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * OpenFISMA is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more 
 * details.
 *
 * You should have received a copy of the GNU General Public License along with OpenFISMA.  If not, see 
 * {@link http://www.gnu.org/licenses/}.
 * 
 * @fileoverview Utility functions
 * 
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2010 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/content/license
 */
 
Fisma.Util = {
    /**
     * Escapes the specified string so that it can be included in a regex without special characters affecting
     * the regex's meaning
     * 
     * Special characters are: .*+?|()[]{}\
     * 
     * @param rawValue Unescaped input
     */
    escapeRegexValue : function (rawValue) {
        var specials = new RegExp("[.*+?|()\\[\\]{}\\\\]", "g");
        
        return rawValue.replace(specials, "\\$&");
    },

    /**
     * Convert a string name of an object into a reference to that object.
     * 
     * For example, "Fisma.Foo.myFoo" returns a reference to the actual Fisma.Foo.myFoo object, if it exists, 
     * or throws an error if does not exist.
     * 
     * This is useful for sending references to objects in JSON syntax, since JSON cannot encode a reference directly.
     * 
     * @param objectName The string name of the object
     */
    getObjectFromName : function (objectName) {

        var pieces = objectName.split('.');
        var currentObj = window;

        for (piece in pieces) {
            currentObj = currentObj[pieces[piece]];

            if (currentObj == undefined) {
                throw "Specified object does not exist: " + objectName;
            }
        }

        // At this point, the current value of parentObj should be the object itself
        return currentObj;
    },
    
    /**
     * Positions the specified panel relative to the specified element.
     * 
     * The goal is to put the panel near the specified element (typically, just underneath and left-aligned) but with
     * the added constraint of not positioning the panel anywhere that will be clipped by the current viewport.
     * 
     * @param panel
     * @param referenceElement
     */
    positionPanelRelativeToElement : function (panel, referenceElement) {

        // This is a constant which indicates how far down the panel should be below the reference element
        var VERTICAL_OFFSET = 5;

        panel.cfg.setProperty(
            "context", 
            [
                referenceElement, 
                YAHOO.widget.Overlay.TOP_LEFT, 
                YAHOO.widget.Overlay.BOTTOM_LEFT,
                null,
                [0, VERTICAL_OFFSET]
            ]);        
    },
    
    /**
     * Generate a timestamp in the format HH:MM:SS, using 24 hour time
     */
    getTimestamp : function () {
        var date = new Date();

        var hours = date.getHours() + "";

        if (hours.length == 1) {
         hours = "0" + hours;
        }

        var minutes = date.getMinutes() + "";

        if (minutes.length == 1) {
         minutes = "0" + minutes;
        }

        var seconds = date.getSeconds() + "";

        if (seconds.length == 1) {
         seconds = "0" + seconds;
        }

        return hours + ":" + minutes + ":" + seconds;
    },

    /**
     * Show confirm window with warning message. config object can have width, text, isLink, url and func
     * 
     * If there is a config.url string, clicking "Yes" button will navigate there. If there is a config.func, 
     * that function will be called, and the parameters passed to that function must be in an /array/ in config.args.
     * If the event comes from a link, set config.isLink to true so that it won't be directed to the link before 
     * "YES" button is clicked.
     *
     * @param event
     * @param config 
     */
    showConfirmDialog : function (event, config) {
        var confirmDialog = Fisma.Util.getDialog();
 
        var buttons = [ { text:"Yes", handler : function () {
                            if (config.url) {
                                document.location = config.url;
                             }else if(config.func) {
                                 var funcObj = Fisma.Util.getObjectFromName(config.func);
                                 if (YAHOO.lang.isFunction(funcObj)) {
                                     if (config.args) {
                                         funcObj.apply(this, config.args);
                                     } else {
                                         funcObj.call();
                                     }
                                 }
                             }
                             this.destroy();
                            }
                        },
                        { text:"No",  handler : function () {
                            this.destroy();
                            }    
                        } 
                     ]; 
 
        confirmDialog.setHeader("Are you sure?");
        confirmDialog.setBody(config.text); 
        confirmDialog.cfg.queueProperty("buttons", buttons); 
        if (config.width) {
            confirmDialog.cfg.setProperty("width", config.width); 
        }
        confirmDialog.render(document.body);
        confirmDialog.show();
        if (config.isLink) {
            YAHOO.util.Event.preventDefault(event);
        }

        confirmDialog.hideEvent.subscribe(function (e) {
            setTimeout(function () {confirmDialog.destroy();}, 0);
        });
    },
 
    /**
     * Show alert warning message. The config object can have width and zIndex property
     *
     * Generanlly, it can just pass alert message string if it does not need to override default config 
     *
     * @param message string
     * @param config object
     */
    showAlertDialog : function (alertMessage, config) {
        var alertDialog = Fisma.Util.getDialog();
 
        var handleOk =  function() {
            this.destroy();
        };
        var button = [ { text: "Ok", handler: handleOk } ];

        alertDialog.setHeader("WARNING");
        alertDialog.setBody(alertMessage); 
        alertDialog.cfg.queueProperty("buttons", button); 

        if (!YAHOO.lang.isUndefined(config) && config.width) {
            alertDialog.cfg.setProperty("width", config.width); 
        }
        if (!YAHOO.lang.isUndefined(config) && config.zIndex) {
            alertDialog.cfg.setProperty("zIndex", config.zIndex); 
        }

        alertDialog.render(document.body);
        alertDialog.show();

        alertDialog.hideEvent.subscribe(function (e) {
            setTimeout(function () {alertDialog.destroy();}, 0);
        });
    },

    /**
     * Generate a YUI SimpleDialog
     * 
     * @return a YUI SimpleDialog
     */
    getDialog : function(){
        var dialog =  
            new YAHOO.widget.SimpleDialog("warningDialog",  
                { width: "400px", 
                  fixedcenter: true, 
                  visible: false, 
                  close: true,
                  modal: true,
                  icon: YAHOO.widget.SimpleDialog.ICON_WARN, 
                  constraintoviewport: true, 
                  draggable: false
                } ); 

        return dialog;
    },

    /**
     * I've refactored this slightly by moving most of the logic into MessageBox.js and MessageBoxStack.js, and moving 
     * the styles into MessageBox.css. I've kept this global method in place to avoid breaking the API right before a 
     * release (which would require diff'ing a lot of lines of code.)
     * 
     * @param msg {String} the message to display
     * @param model {String} either "info" or "warning" -- this affects the color scheme used to display the message
     * @param clear {Boolean} If true, new message will replace existing message. If false, new message will be
     *              appended.
     */
     message: function (msg, model, clear) {
        clear = clear || false;

        msg = $P.stripslashes(msg);

        var messageBoxStack = Fisma.Registry.get("messageBoxStack");
        var messageBox = messageBoxStack.peek();

        if (messageBox) {
            if (clear) {
                messageBox.setMessage(msg);
            } else {
                messageBox.addMessage(msg);
            }
            
            if (model == 'warning') {
                messageBox.setErrorLevel(Fisma.MessageBox.ERROR_LEVEL.WARN);
            } else {
                messageBox.setErrorLevel(Fisma.MessageBox.ERROR_LEVEL.INFO);
            }

            messageBox.show();
        }
    },

    /**
     * To format time on the hidden element by id
     * 
     * @param id
     */
    updateTimeField: function (id) {
        var hiddenEl = document.getElementById(id);
        var hourEl = document.getElementById(id + 'Hour');
        var minuteEl = document.getElementById(id + 'Minute');
        var ampmEl = document.getElementById(id + 'Ampm');
        
        var hour = hourEl.value;
        var minute = minuteEl.value;
        var ampm = ampmEl.value;
        
        if ('PM' == ampm) {
            hour = parseInt(hour) + 12;
        }
        
        hour = $P.str_pad(hour, 2, '0', 'STR_PAD_LEFT');
        minute = $P.str_pad(minute, 2, '0', 'STR_PAD_LEFT');    
        
        var time = hour + ':' + minute + ':00';
        hiddenEl.value = time;
    }
};
