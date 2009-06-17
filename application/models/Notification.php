<?php

/**
 * Notification
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class Notification extends BaseNotification
{
    /**
     * Notification event constants
     */
    const FINDING_CREATED = 1;
    const FINDING_IMPORT = 2;
    const FINDING_INJECT = 3;
    
    const ASSET_MODIFIED = 4;
    const ASSET_CREATED = 5;
    const ASSET_DELETED = 6;
    
    const UPDATE_COURSE_OF_ACTION = 7;
    const UPDATE_FINDING_ASSIGNMENT = 8;
    const UPDATE_CONTROL_ASSIGNMENT = 9;
    const UPDATE_COUNTERMEASURES = 10;
    const UPDATE_THREAT = 11;
    const UPDATE_FINDING_RECOMMENDATION = 12;
    const UPDATE_FINDING_RESOURCES = 13;
    const UPDATE_EST_COMPLETION_DATE = 14;

    const MITIGATION_APPROVED_SSO = 15;
    const MITIGATION_APPROVED_IVV = 52;
    const MITIGATION_STRATEGY_SUBMIT = 53;
    const MITIGATION_STRATEGY_REVISE = 54;
    const POAM_CLOSED = 16;
    
    const EVIDENCE_UPLOAD = 17;
    const EVIDENCE_DENIED = 50;
    const EVIDENCE_APPROVED_1ST = 18;
    const EVIDENCE_APPROVED_2ND = 19;
    
    const ACCOUNT_MODIFIED = 21;
    const ACCOUNT_DELETED = 22;
    const ACCOUNT_CREATED = 23;
    const ACCOUNT_LOCKED = 51;
    
    const ORGANIZATION_DELETED = 24;
    const ORGANIZATION_MODIFIED = 25;
    const ORGANIZATION_CREATED = 26;
    
    const SYSTEM_DELETED = 27;
    const SYSTEM_MODIFIED = 28;
    const SYSTEM_CREATED = 29;
    
    const PRODUCT_CREATED = 30;
    const PRODUCT_MODIFIED = 31;
    const PRODUCT_DELETED = 32;
    
    const ROLE_CREATED = 33;
    const ROLE_DELETED = 34;
    const ROLE_MODIFIED = 35;
    
    const SOURCE_CREATED = 36;
    const SOURCE_MODIFIED = 37;
    const SOURCE_DELETED = 38;
    
    const NETWORK_MODIFIED = 39;
    const NETWORK_CREATED = 40;
    const NETWORK_DELETED = 41;
    
    const CONFIGURATION_MODIFIED = 42;
    
    const ACCOUNT_LOGIN_SUCCESS = 43;
    const ACCOUNT_LOGIN_FAILURE = 44;
    const ACCOUNT_LOGOUT = 45;
    
    const ECD_EXPIRES_TODAY = 46;
    const ECD_EXPIRES_7_DAYS = 47;
    const ECD_EXPIRES_14_DAYS = 48;
    const ECD_EXPIRES_21_DAYS = 49;

    /**
     * Add notifications for the specified event.
     *
     * @param int $eventId The event id
     * @param object $record  the model which is changed
     * @param object $user  the user object
     * @param int $organizationId The organization id.
     */
    public function add($eventId, $record, $user, $organizationId = null)
    {
        $event = Doctrine::getTable('Event')->find($eventId);
        if (empty($event)) {
            //@todo english
            throw new Fisma_Exception_General("The event of this operation dose not exist");
        }
        $eventText = $event->name . " by $user->nameLast . $user->nameFirst";
        $eventText .= "(Id. $record->id)";

        if ($organizationId == null) {
            $userEvents = Doctrine::getTable('UserEvent')->findByEventId($eventId);
        } else {
            $userEvents = Doctrine_Query::create()
                            ->select('ue.eventId, ue.userId')
                            ->from('UserEvent ue, UserOrganization uo')
                            ->where('ue.eventId = ?', $eventId)
                            ->addWhere('uo.organizationId = ?', $organizationId)
                            ->execute();
        }

        foreach ($userEvents as $userEvent) {
            $this->eventId   = $userEvent->eventId;
            $this->userId    = $userEvent->userId;
            $this->eventText = $eventText;
            $this->save();
        }
    }
}
