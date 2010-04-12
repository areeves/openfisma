/**
 * Based on the iToggle example from Engage Interactive Labs.
 * http://labs.engageinteractive.co.uk/itoggle/
 * 
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
 * @fileoverview Implements an ON/OFF switch button
 * 
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2010 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/content/license
 * @version   $Id$
 */

/**
 * Constructor
 * 
 * @param element|string element The element to convert into a switch button
 * @param boolean initialState True if switch is ON, false if OFF
 * @param function callback Called when switch's state changes. The callback takes the switch button object as a
 * its only parameter.
 * @param object payload Payload is a generic object which is stored with the button that the implementer can use to 
 * pass extra information to the 
 */
Fisma.SwitchButton = function (element, initialState, callback, payload) {

    var that = this;
    
    // element can be an actual element or an ID
    if (element instanceof HTMLElement) {
        this.element = element;
    } else {
        this.element = document.getElementById(element);
        
        if (!this.element) {
            throw 'Invalid element name "' + name + '"';
        }
    }
    
    // Set up DOM elements needed for switch button
    this.createDomElements();
    
    // Set parameters
    this.state = initialState;
    this.payload = payload;
    
    if (!this.state) {
        // Button is drawn in "ON" position by default. If initial state is "OFF" then we need to redraw it
        this.element.style.backgroundPositionX = '-54px';
    }
 
    // Set click handler
    this.element.onclick = function () {
        that.toggleSwitch.call(that);
    }
    
    /* 
     * Callback will be a string like 'Fisma.Module.handleSwitchButtonStateChange', which needs to be converted into a 
     * reference to the actual function, such as window['Fisma']['Module']['handleSwitchButtonStateChange']
     */
    if ('' != callback) {
        var callbackPieces = callback.split('.');
        var callbackParent = window;
        
        for (piece in callbackPieces) {
            callbackParent = callbackParent[callbackPieces[piece]];
            
            if (!callbackParent) {
                throw "Specified callback does not exist: " + callback;
            }
        }
        
        // At this point, the current value of callbackParent should be the callback function itself
        if ('function' ==  typeof callbackParent) {
            this.callback = callbackParent;
        } else {
            throw "Specified callback is not a function: " + callback;
        }
    }
}

Fisma.SwitchButton.prototype = {
    
    /**
     * Create the necessary elements in the DOM to support the button functionality
     */
    createDomElements : function () {
        YAHOO.util.Dom.addClass(this.element, 'switchButton');

        // The border span puts a thin graphical border around the button, giving it some visual depth
        var borderSpan = document.createElement('span');
        YAHOO.util.Dom.addClass(borderSpan, 'border');
        this.element.appendChild(borderSpan);

        // Place a spinner graphic next to the button (hidden by default, see setBusy())
        var spinnerSpan = document.createElement('span');
        YAHOO.util.Dom.addClass(spinnerSpan, 'spinner');

        var spinnerImg = document.createElement('img');
        spinnerImg.src = '/images/spinners/small.gif';
        spinnerSpan.appendChild(spinnerImg);

        this.element.appendChild(spinnerSpan);
        this.spinner = spinnerSpan;
    },
    
    /**
     * Toggle this switch between its off and on states
     */
    toggleSwitch : function () {        
        var animationAttributes;
        
        if (this.state) {
            
            // Animate from "ON" to "OFF"
            animationAttributes = {
                backgroundPositionX : {
                    from : 0,
                    to : -54,
                    unit : 'px'
                }                
            }

            this.state = false;
        } else {
            
            // Animate from "OFF" to "ON"
            animationAttributes = {
                backgroundPositionX : {
                    from : -54,
                    to : 0,
                    unit : 'px'
                }                
            }

            this.state = true;
        }        
        
        var toggleAnimation = new YAHOO.util.Anim(this.element, animationAttributes, .25, YAHOO.util.Easing.easeOut);

        toggleAnimation.animate();
        
        if (this.callback) {
            this.callback(this);
        }
    },
    
    /**
     * Set the busy state of the button
     * 
     * In addition to ON/OFF states, the button also has the states "busy" and "not busy" (which are orthogonal to 
     * ON/OFF). These states can be used by the implementer to show the user that some action is occuring in the 
     * background, such as an XHR to persist the button's state.
     * 
     * @param bool busy True if the XHR should display a "busy" indicator (i.e. indeterminate progress spinner)
     */
    setBusy : function (busy) {

        if (busy) {
            // Show progress spinner
            this.spinner.style.visibility = 'visible';
        } else {
            // Hide progress spinner
            this.spinner.style.visibility = 'hidden';
        }
    }
};