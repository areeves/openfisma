/**
 * Copyright (c) 2011 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see {@link http://www.gnu.org/licenses/}.
 *
 * @author    Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license   http://www.openfisma.org/content/license
 */

(function () {
    var Task = {
        /**
         * Polling interval
         */
        UPDATE_INTERVAL: 5000,

        /**
         * Counter to track the number of consecutive polls
         */
        _pollingCounter: 0,

        /**
         * Polling URI.  This value changes depending on whether a task is running.
         */
        _pollingURI: '/task/status/format/json',

        /**
         * Timer object for handling polls
         */
        _pollingTimer: null,

        /**
         * YUI ProgressBar object
         */
        _progressBar: null,

        /**
         * Current task ID the progressBar is using
         */
        _progressBarId: null,

        /**
         * Kicks off polling to find current running and pending processes
         */
        start: function() {
            Task.getStatus();
        },

        /**
         * Display the taskbar and add the ProgressBar.
         */
        createProgressBar: function() {
            var taskBar = document.getElementById('taskbar');
            taskBar.style.visibility = 'visible';
            Task._progressBar = new YAHOO.widget.ProgressBar({
                anim: true,
                direction: "ltr",
                height: "16px",
                width: "100px",
            }).render("taskbar-progress");
        },

        /**
         * @param data JSON data passed from the polling
         */
        updateProgressBar: function(data) {
            Task._pollingCounter++;
            var workerName = data.running.worker;
            if (workerName == 'antivirus') {
                workerName = 'Virus scan';
            }

            document.getElementById('taskbar-worker').innerHTML = '<span id="taskbartooltip" class="tooltip">' +
                workerName + '</span> in progress:';
            Task.createTooltip("taskbartooltip", "OpenFISMA is running a background task.");

            if (data.running.progress < Task._progressBar.get('value'))
            {
                Task._progressBar.set('anim', false);
                Task._progressBar.set('value', 0);
                Task._progressBar.set('anim', true);
                Task.removeMessageBar();
            }

            Task._progressBar.set('value', parseInt(data.running.progress));
            document.getElementById('taskbar-jobs').innerHTML = '<b>' + data.count.pending + '</b> jobs remaining';

            if (Task._pollingCounter >= 4) {
                Task.removeMessageBar();
            }

            if (data.running.progress == 100) {
                var message = workerName + ' completed';
                window.message(message, "notice", true);
                Task._pollingCounter = 0;
                Task._progressBarId = null;
                Task._pollingURI = '/task/status/format/json';
            } else {
                Task._pollingURI = '/task/status/format/json' + '/id/' + data.running.id;
                Task._progressBarId = parseInt(data.running.id);
            }
        },

        /**
         * Retrieves the number of running and pending processes
         * Creates and updates the ProgressBar if necessary
         */
        getStatus: function() {
            YAHOO.util.Connect.asyncRequest('GET', Task._pollingURI, {
                success: function(o) {
                    var data = YAHOO.lang.JSON.parse(o.responseText);
                    var pendingCount = parseInt(data.tasks.count.pending);
                    var runningCount = parseInt(data.tasks.count.running);

                    if ((runningCount >= 1 || Task._progressBarId != null) && Task._progressBar != null) {
                        Task.updateProgressBar(data.tasks);
                    } else if (runningCount >= 1 && Task._progressBar == null) {
                        Task.createProgressBar();
                        Task.updateProgressBar(data.tasks);
                    }

                    if (pendingCount >= 1 || runningCount >= 1 || Task._progressBarId != null) {
                        Task._pollingTimer = YAHOO.lang.later(Task.UPDATE_INTERVAL, null, Task.getStatus);
                    } else {
                        YAHOO.lang.later(3000, null, function() {
                            Task.removeTaskBar();
                            Task._pollingTimer.cancel();
                        });
                    }
                    if (Task._statusCounter >= 3 && Task._progressBarId == null) {
                        Task.removeMessageBar();
                        Task.removeTaskBar();
                        Task._pollingTimer.cancel();
                    } else {
                        Task._statusCounter++;
                    }
                },

                failure: function(o) {
                }
            })
        },

        /**
         * Remove the taskbar from the display
         */
        removeTaskBar: function() {
            document.getElementById('taskbar').style.display = 'none';
        },

        /**
         * Remove the msgbar from the display
         */
        removeMessageBar: function() {
            document.getElementById('msgbar').style.display = 'none';
        },

        /**
         * @param name Tooltip name
         * @param content Content for the tooltip
         */
        createTooltip: function(name, content) {
            var toolTip = new YAHOO.widget.Tooltip("tooltipObj", {
                context: name,
                showdelay: 150,
                hidedelay: 150,
                autodismissdelay: 25000,
                text: content,
                effect: {
                    effect: YAHOO.widget.ContainerEffect.FADE,
                    duration: 0.25  },
                width: "20%"
            });
        }
    };
    Fisma.Task = Task;
})();
