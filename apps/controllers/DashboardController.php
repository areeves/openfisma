<?PHP

require_once CONTROLLERS . DS . 'SecurityController.php';

class DashboardController extends SecurityController
{
    public function indexAction()
    {
        $this->render();
    }
}
?>
