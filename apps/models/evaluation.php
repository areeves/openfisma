<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/
require_once 'Abstract.php';
class Evaluation extends Fisma_Model
{
    protected $_name = 'evaluations';
    protected $_primary = 'id';

    public function getEvEvalList()
    {
        return array(1=>array('name'=>'EV_SSO','function'=>'update_evidence_approval_first'),
                     2=>array('name'=>'EV_FSA','function'=>'update_evidence_approval_second'),
                     3=>array('name'=>'EV_IVV','function'=>'update_evidence_approval_third'));
    }
}

