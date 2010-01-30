<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
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
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Jim Chen <xhorse@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package    Cron_Job
 */

/**
 * This static class is responsible for scanning for notifications which need to
 * be delivered, delivering the notifications, and then removing the sent
 * notifications from the queue.
 *
 * @package    Cron_Job
 * @subpackage Controller_Subpackage
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 *
 * @todo Needs cleanup
 * @todo need to adjust for timezone difference between DB and application when
 * displaying timestamps
 */

$notify = new Notify();
$notify->processNotificationQueue();

class Notify
{
    public function __construct()
    {
        require_once(realpath(dirname(__FILE__) . '/../../library/Fisma.php'));

        Fisma::initialize(Fisma::RUN_MODE_COMMAND_LINE);
        Fisma::connectDb();
    }
    
    /**
     * Iterate through the users and check who has
     * notifications pending.
     *
     * @todo log the email send results
     */
    function processNotificationQueue() {
        // Get all notifications grouped by user_id
        $query = Doctrine_Query::create()
                    ->select('n.*, u.email, u.notifyFrequency')
                    ->from('Notification n')
                    ->innerJoin('n.User u')
                    ->innerJoin('n.Event e')
                    ->where('u.emailValidate = 1')
                    ->addWhere(time() . ' > ? ',
                        strtotime("'u.mostRecentNotifyTs'") + "'u.notifyFrequency'" * 3600 )
                    ->orderBy('n.userId');
        $notifications = $query->execute();

        // Loop through the groups of notifications, concatenate all messages
        // per user into a single array, then call the e-mail function for
        // each user. If the e-mail is successful, then remove the notifications
        // from the table and update the most_recent_notify_ts timestamp.
        $currentNotifications = array();
        for ($i = 0; $i < count($notifications); $i++) {
            $currentNotifications[] = $notifications[$i];

            // If this is the last entry OR if the next entry has a different
            // user ID, then this current message is completed and should be
            // e-mailed to the user.
            if ($i == (count($notifications) - 1)
                || ($notifications[$i]->userId !=
                    $notifications[$i+1]->userId)) {

                Notify::sendNotificationEmail($currentNotifications);
                Notify::purgeNotifications($currentNotifications);
                Notify::updateUserNotificationTimestamp($notifications[$i]->userId);

                // Move onto the next user
                $currentNotifications = array();
            }

        }
    }

    /**
     * Compose and send the notification email for
     * this user.
     *
     * Notice that there is a bit of a hack -- the addressing information is
     * stored in the 0 row of $notifications.
     *
     * @param array $notifications A group of rows from the notification table
     */
    static function sendNotificationEmail($notifications) {
        $mail = new Fisma_Mail();
        // Send the e-mail
        $mail->sendNotification($notifications);
    }

    /**
     * Remove notifications from the queue table.
     *
     * @param array $notifications A group of rows from the notifications table
     */
    static function purgeNotifications($notifications) {
        $notificationIds = array();
        foreach ($notifications as $notification) {
            $notificationIds[] = $notification['id'];
        }

        Doctrine_Query::create()
            ->delete()
            ->from('Notification')
            ->whereIn('id', $notificationIds)
            ->execute();
    }

    /**
     * Updates the timestamp for the
     * specified user so that he will not receieve too many e-mail in too short
     * of a time period.
     *
     * @param integer $userId The Id of the user to update
     */
    static function updateUserNotificationTimestamp($userId) {
        $user = new User();
        $user = $user->getTable()->find($userId);
        $user->mostRecentNotifyTs = Fisma::now();
        $user->save();
    }
}

