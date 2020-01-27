<?php
/**
 * SkeletonrequestController.php - Main Controller
 *
 * Main Controller Skeletonrequest Module
 *
 * @category Controller
 * @package Skeletonrequest
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

declare(strict_types=1);

namespace OnePlace\Skeletonrequest\Controller;

use Application\Controller\CoreController;
use Application\Model\CoreEntityModel;
use OnePlace\Skeletonrequest\Model\Skeletonrequest;
use OnePlace\Skeletonrequest\Model\SkeletonrequestTable;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\AdapterInterface;

class SkeletonrequestController extends CoreController {
    /**
     * Skeletonrequest Table Object
     *
     * @since 1.0.0
     */
    private $oTableGateway;

    /**
     * SkeletonrequestController constructor.
     *
     * @param AdapterInterface $oDbAdapter
     * @param SkeletonrequestTable $oTableGateway
     * @since 1.0.0
     */
    public function __construct(AdapterInterface $oDbAdapter,SkeletonrequestTable $oTableGateway,$oServiceManager) {
        $this->oTableGateway = $oTableGateway;
        $this->sSingleForm = 'skeletonrequest-single';
        parent::__construct($oDbAdapter,$oTableGateway,$oServiceManager);

        if($oTableGateway) {
            # Attach TableGateway to Entity Models
            if(!isset(CoreEntityModel::$aEntityTables[$this->sSingleForm])) {
                CoreEntityModel::$aEntityTables[$this->sSingleForm] = $oTableGateway;
            }
        }
    }

    /**
     * Skeletonrequest Index
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function indexAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('skeletonrequest');

        # Add Buttons for breadcrumb
        $this->setViewButtons('skeletonrequest-index');

        # Set Table Rows for Index
        $this->setIndexColumns('skeletonrequest-index');

        # Get Paginator
        $oPaginator = $this->oTableGateway->fetchAll(true);
        $iPage = (int) $this->params()->fromQuery('page', 1);
        $iPage = ($iPage < 1) ? 1 : $iPage;
        $oPaginator->setCurrentPageNumber($iPage);
        $oPaginator->setItemCountPerPage(3);

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('skeletonrequest-index',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        return new ViewModel([
            'sTableName'=>'skeletonrequest-index',
            'aItems'=>$oPaginator,
        ]);
    }

    /**
     * Skeletonrequest Add Form
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function addAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('skeletonrequest');

        # Get Request to decide wether to save or display form
        $oRequest = $this->getRequest();

        # Display Add Form
        if(!$oRequest->isPost()) {
            # Add Buttons for breadcrumb
            $this->setViewButtons('skeletonrequest-single');

            # Load Tabs for View Form
            $this->setViewTabs($this->sSingleForm);

            # Load Fields for View Form
            $this->setFormFields($this->sSingleForm);

            # Log Performance in DB
            $aMeasureEnd = getrusage();
            $this->logPerfomance('skeletonrequest-add',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

            return new ViewModel([
                'sFormName' => $this->sSingleForm,
            ]);
        }

        # Get and validate Form Data
        $aFormData = $this->parseFormData($_REQUEST);

        # Save Add Form
        $oSkeletonrequest = new Skeletonrequest($this->oDbAdapter);
        $oSkeletonrequest->exchangeArray($aFormData);
        $iSkeletonrequestID = $this->oTableGateway->saveSingle($oSkeletonrequest);
        $oSkeletonrequest = $this->oTableGateway->getSingle($iSkeletonrequestID);

        # Save Multiselect
        $this->updateMultiSelectFields($_REQUEST,$oSkeletonrequest,'skeletonrequest-single');

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('skeletonrequest-save',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        # Display Success Message and View New Skeletonrequest
        $this->flashMessenger()->addSuccessMessage('Skeletonrequest successfully created');
        return $this->redirect()->toRoute('skeletonrequest',['action'=>'view','id'=>$iSkeletonrequestID]);
    }

    /**
     * Skeletonrequest Edit Form
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function editAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('skeletonrequest');

        # Get Request to decide wether to save or display form
        $oRequest = $this->getRequest();

        # Display Edit Form
        if(!$oRequest->isPost()) {

            # Get Skeletonrequest ID from URL
            $iSkeletonrequestID = $this->params()->fromRoute('id', 0);

            # Try to get Skeletonrequest
            try {
                $oSkeletonrequest = $this->oTableGateway->getSingle($iSkeletonrequestID);
            } catch (\RuntimeException $e) {
                echo 'Skeletonrequest Not found';
                return false;
            }

            # Attach Skeletonrequest Entity to Layout
            $this->setViewEntity($oSkeletonrequest);

            # Add Buttons for breadcrumb
            $this->setViewButtons('skeletonrequest-single');

            # Load Tabs for View Form
            $this->setViewTabs($this->sSingleForm);

            # Load Fields for View Form
            $this->setFormFields($this->sSingleForm);

            # Log Performance in DB
            $aMeasureEnd = getrusage();
            $this->logPerfomance('skeletonrequest-edit',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

            return new ViewModel([
                'sFormName' => $this->sSingleForm,
                'oSkeletonrequest' => $oSkeletonrequest,
            ]);
        }

        $iSkeletonrequestID = $oRequest->getPost('Item_ID');
        $oSkeletonrequest = $this->oTableGateway->getSingle($iSkeletonrequestID);

        # Update Skeletonrequest with Form Data
        $oSkeletonrequest = $this->attachFormData($_REQUEST,$oSkeletonrequest);

        # Save Skeletonrequest
        $iSkeletonrequestID = $this->oTableGateway->saveSingle($oSkeletonrequest);

        $this->layout('layout/json');

        # Save Multiselect
        $this->updateMultiSelectFields($_REQUEST,$oSkeletonrequest,'skeletonrequest-single');

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('skeletonrequest-save',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        # Display Success Message and View New User
        $this->flashMessenger()->addSuccessMessage('Skeletonrequest successfully saved');
        return $this->redirect()->toRoute('skeletonrequest',['action'=>'view','id'=>$iSkeletonrequestID]);
    }

    /**
     * Skeletonrequest View Form
     *
     * @since 1.0.0
     * @return ViewModel - View Object with Data from Controller
     */
    public function viewAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('skeletonrequest');

        # Get Skeletonrequest ID from URL
        $iSkeletonrequestID = $this->params()->fromRoute('id', 0);

        # Try to get Skeletonrequest
        try {
            $oSkeletonrequest = $this->oTableGateway->getSingle($iSkeletonrequestID);
        } catch (\RuntimeException $e) {
            echo 'Skeletonrequest Not found';
            return false;
        }

        # Attach Skeletonrequest Entity to Layout
        $this->setViewEntity($oSkeletonrequest);

        # Add Buttons for breadcrumb
        $this->setViewButtons('skeletonrequest-view');

        # Load Tabs for View Form
        $this->setViewTabs($this->sSingleForm);

        # Load Fields for View Form
        $this->setFormFields($this->sSingleForm);

        # Log Performance in DB
        $aMeasureEnd = getrusage();
        $this->logPerfomance('skeletonrequest-view',$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"utime"),$this->rutime($aMeasureEnd,CoreController::$aPerfomanceLogStart,"stime"));

        return new ViewModel([
            'sFormName'=>$this->sSingleForm,
            'oSkeletonrequest'=>$oSkeletonrequest,
        ]);
    }
}
