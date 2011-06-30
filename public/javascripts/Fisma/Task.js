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
        UPDATE_INTERVAL: 5000,

        _statusCounter: 0,

        _statusURI: '/gearman/status-data/format/json',

        _statusTimer: null,

        _progressBar: null,

        _progressBarId: null,

        start: function() {
            Task.getStatus();
        },

        createProgressBar: function() {
            var taskBar = document.getElementById('taskbar');
            taskBar.style.visibility = 'visible';
            Task._progressBar = new YAHOO.widget.ProgressBar({
                anim: true,
                direction: "ltr",
                height: "16px",
                width: "100px"
            }).render("taskbar-progress");
        },

        updateProgressBar: function(data) {
            Task._progressBar.set('anim', 'true');
            Task._statusCounter++;
            var taskBarWorker = document.getElementById('taskbar-worker');
            var workerName = data.running.worker;
            if (workerName == 'test') {
                workerName = 'Virus scan';
            }

            taskBarWorker.innerHTML = '<span id="taskbartooltip" class="tooltip">' + workerName + '</span> in progress:';
            Task.createTooltip("taskbartooltip", "OpenFISMA is running a background task.");

            Task._progressBar.set('value', parseInt(data.running.progress));
            Task._progressBar.on('progress',function (value) {
                YAHOO.util.Dom.get('valueContainer').innerHTML = data.running.progress;
            });

            var taskBarStatus = document.getElementById('taskbar-status');
            taskBarStatus.innerHTML = '<b>' + data.count.pending + '</b> jobs remaining';

            var taskBarFinished = document.getElementById('taskbar-finished');

            if (Task._statusCounter >= 4) {
                taskBarFinished.innerHTML = '';
            }

            if (data.running.progress == 100) {
                taskBarFinished.innerHTML = workerName + '[' + Task._progressBarId + '] completed';
                Task._statusCounter = 0;
                Task._progressBarId = null;
                Task._progressBar.set('anim', 'false');
                Task._progressBar.set('value', '0');
                Task._statusURI = '/gearman/status-data/format/json';
            } else {
                Task._statusURI = '/gearman/status-data/format/json' + '/id/' + data.running.id;
                Task._progressBarId = parseInt(data.running.id);
            }
        },

        getStatus: function() {
            YAHOO.util.Connect.asyncRequest('GET', Task._statusURI, {
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
                        Task._statusTimer = YAHOO.lang.later(Task.UPDATE_INTERVAL, null, Task.getStatus);
                    } else {
                        Task._statusTimer.cancel();
                    }

                    if (Task._statusCounter >= 3 && Task._progressBarId == null) {
                        document.getElementById('taskbar').style.display = 'none';
                        Task._statusTimer.cancel();
                    } else {
                        Task._statusCounter++;
                    }
                },
                failure: function(o) {
                }
            })
        },

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
