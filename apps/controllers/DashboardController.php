<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Controller/Action.php';
require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'poam.php';
require_once LIBS . DS .  'PoamSummary.class.php';

/**
 * DashboardController responsible for all dashboard creation
 */
class DashboardController extends SecurityController
{
    protected $_poam = null;

    function init()
    {
        parent::init();
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('totalstatus', 'xml')
                      ->addActionContext('totaltype','xml')
                      ->initContext();
    }

    function preDispatch()
    {
        parent::preDispatch();
        if( !isset($this->_poam) ){
            $this->_poam = new Poam();
        }
    }


    public function indexAction()
    {
        $ret = $this->_poam->search($this->me->systems,
                        array('count'=>'status', 'status'),
                        array('status'=>array('OPEN','EN')));
        $eo_count = $this->_poam->search($this->me->systems, 
                        array('count'=>'count(*)'),
                        array('status'=>'EN',
                              'est_date_end'=> parent::$now ));
        $alert = array();
        foreach($ret as $r) {
            $alert[$r['status']] = $r['count'];
        }
        $alert['EO'] = $eo_count;
        $this->view->alert = $alert;
        $this->render();
    }

    public function totalstatusAction()
    {
        $poam = $this->_poam;
        $req = $this->getRequest();
        $type = $req->getParam('type','pie');

        if( !in_array($type, array('3d column','pie')) ) {
            $type = 'pie';
        }
        $ret = $poam->search($this->me->systems,
                        array('count'=>'status', 'status'));
        $eo_count = $poam->search($this->me->systems, 
                        array('count'=>'count(*)'),
                        array('status'=>'EN',
                              'est_date_end'=> parent::$now ));
        $this->view->summary = array( 'NEW'=>0,'OPEN'=>0,'EN'=>0,'EP'=>0,
                                      'ES'=>0,'CLOSED'=>0) ;
        foreach($ret as $s){
            $this->view->summary["{$s['status']}"] = $s['count'];
        }
        $this->view->summary["EO"] = $eo_count;
        $this->view->chart_type = $type;
        $this->render($type);
    }

    public function totaltypeAction()
    {
        $ret = $this->_poam->search($this->me->systems, array('count'=>'type', 'type') );
        $this->view->summary = array();
        foreach($ret as $s){
            $this->view->summary["{$s['type']}"] = $s['count'];
        }
        $this->render();
    }

}

?>
