<?PHP

require_once CONTROLLERS . DS . 'SecurityController.php';

class FindingController extends SecurityController
{
    public function indexAction()
    {
        $this->render();
    }
    /** List the summary of openfisma
     */
    public function summaryAction()
    {
        $status = '';

        $this->render();
    }
    public function searchAction()
    {
        $this->render();
    }
}
?>
