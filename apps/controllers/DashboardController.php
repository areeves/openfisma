<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Controller/Action.php';
require_once MODELS . DS . 'user.php';
require_once CONTROLLERS . DS . 'SecurityController.php';
require_once LIBS . DS .  'dashboard_chart/Dashboard_Chart.php';
require_once LIBS . DS .  'dashboard_chart/create_xml.php';
require_once LIBS . DS .  'PoamSummary.class.php';

/**
 * DashboardController responsible for all dashboard creation
 */
class DashboardController extends SecurityController
{

    /**
     * @Desc : IndexAction for the use of create the datasource for the front flash that war create as a xml file.
       Using $view to show the data layer that will replace the smarty template function.
     */
    public function indexAction()
    {

          $total_open   = 0;
          $total_en     = 0;
          $total_eo     = 0;
          $total_ep     = 0;
          $total_es     = 0;
          $total_closed = 0;
          $total_none   = 0;
          $total_cap    = 0;
          $total_fp     = 0;
          $total_ar     = 0;
          $total_items  = 0;

          $auth = Zend_Auth::getInstance();
          $userID = $this->me->id;
          $user = new User;
          $arrays = $user->getMySystems($userID);
          while ($array = array_pop($arrays))
          {
              $system_id   = $array;
              $total_none += $this->totalCount('NONE', NULL,$system_id);
              $total_cap  += $this->totalCount('CAP',  NULL,$system_id);
              $total_fp   += $this->totalCount('FP',   NULL,$system_id);
              $total_ar   += $this->totalCount('AR',   NULL,$system_id);
              $total_open += $this->totalCount(NULL, 'OPEN',$system_id);
              $total_en   += $this->totalCount(NULL, 'EN',$system_id);
              $total_eo   += $this->totalCount(NULL, 'EO',$system_id);
              $total_ep   += $this->totalCount(NULL, 'EP',$system_id);
              $total_es   += $this->totalCount(NULL, 'ES',$system_id);
              $total_closed += $this->totalCount(NULL, 'CLOSED',$system_id);
          }

          $total_items = $total_none + $total_cap + $total_fp + $total_ar;
          $summary =  Array('total_items'  => $total_items,
               'total_none'   => $total_none,
               'total_cap'    => $total_cap,
               'total_fp'     => $total_fp,
               'total_ar'     => $total_ar,
               'total_open'   => $total_open,
               'total_en'     => $total_en,
               'total_eo'     => $total_eo,
               'total_ep'     => $total_ep,
               'total_es'     => $total_es,
               'total_closed' => $total_closed
               );
          create_xml_1($summary['total_open'],
                       $summary['total_en'],
                       $summary['total_eo'],
                       $summary['total_ep'],
                       $summary['total_es'],
                       $summary['total_closed']);

          create_xml_2($summary['total_open'],
                       $summary['total_en'],
                       $summary['total_eo'],
                       $summary['total_ep'],
                       $summary['total_es'],
                       $summary['total_closed']);

          create_xml_3($summary['total_none'],
                       $summary['total_cap'],
                       $summary['total_fp'],
                       $summary['total_ar']);


        $date = date('Y-M-D h:i:s:A');
        $dash_board = new Dashboard_Chart();
        $chart_one = $dash_board->InsertChart('/temp/dashboard1.xml', "380" , "220");
        $chart_two = $dash_board->InsertChart('/temp/dashboard2.xml' , "200" , "220");
        $chart_three = $dash_board->InsertChart('/temp/dashboard3.xml' , "380" , "220");
        $view = $this->view;
        $view->assign('open',$summary['total_open']);
        $view->assign('need_ev_ot',$summary['total_en']);
        $view->assign('need_ev_od',$summary['total_eo']);
        $view->assign('OneChart',$chart_one);
        $view->assign('TwoChart',$chart_two);
        $view->assign('ThreeChart',$chart_three);
        $view->assign('Current_time',$date);
        $this->render();
    }

    /**
     *  Select poams table's count  by the supply parameers.
     *  @param $tyle is an variable such as  NONE, CAP, FP,AR or NULL.
                    var $status is an variable  such as  OPEN, EN,CLOSED etc..
     *  @return int.
     */
    private function totalCount($type = NULL, $status = NULL , $system_id)
    {
        require_once MODELS . DS . 'poam.php';
        $db = Zend_Registry::get('db');
        $poamsModel = new poam($db);
        $poam =  $poamsModel->select();
        $poam->from($poamsModel,'count(*)');
        $poam->where('system_id = ? ',$system_id);
        if ($type or $status) {
            if ($type) {
                $poam->where('type = ? ', $type);
            }
            if ($status) {
                switch ($status) {
                    case "EN" :
                    $poam->where('status = ? and action_est_date > NOW()', $status);
                    break;
                    case "EO" :
                    $poam->where('status = ? and action_est_date <= NOW() ','EN');
                    $poam->orwhere('action_est_date = ? ','NULL');
                    break;
                    default   :
                    $poam->where('status = ? ', $status);
                }
            }
        }
        $result = $db->fetchAll($poam);
        if ($result) {
            $row = $result[0];
            return $row['count(*)'];
        } else {
            return 0;
        }
    }

}

?>
