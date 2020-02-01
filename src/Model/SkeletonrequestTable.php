<?php
/**
 * SkeletonrequestTable.php - Skeletonrequest Table
 *
 * Table Model for Skeletonrequest
 *
 * @category Model
 * @package Skeletonrequest
 * @author Verein onePlace
 * @copyright (C) 2020 Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

namespace OnePlace\Skeletonrequest\Model;

use Application\Controller\CoreController;
use Application\Model\CoreEntityTable;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\DbSelect;

class SkeletonrequestTable extends CoreEntityTable {

    /**
     * SkeletonrequestTable constructor.
     *
     * @param TableGateway $tableGateway
     * @since 1.0.0
     */
    public function __construct(TableGateway $tableGateway) {
        parent::__construct($tableGateway);

        # Set Single Form Name
        $this->sSingleForm = 'skeletonrequest-single';
    }

    /**
     * Get Skeletonrequest Entity
     *
     * @param int $id
     * @return mixed
     * @since 1.0.0
     */
    public function getSingle($id) {
        return $this->getSingleEntity($id,'Skeletonrequest_ID');
    }

    /**
     * Save Skeletonrequest Entity
     *
     * @param Skeletonrequest $oSkeletonrequest
     * @return int Skeletonrequest ID
     * @since 1.0.0
     */
    public function saveSingle(Skeletonrequest $oSkeletonrequest) {
        $aData = [
            'label' => $oSkeletonrequest->label,
        ];

        $aData = $this->attachDynamicFields($aData,$oSkeletonrequest);

        $id = (int) $oSkeletonrequest->id;

        if ($id === 0) {
            # Add Metadata
            $aData['created_by'] = CoreController::$oSession->oUser->getID();
            $aData['created_date'] = date('Y-m-d H:i:s',time());
            $aData['modified_by'] = CoreController::$oSession->oUser->getID();
            $aData['modified_date'] = date('Y-m-d H:i:s',time());

            # Insert Skeletonrequest
            $this->oTableGateway->insert($aData);

            # Return ID
            return $this->oTableGateway->lastInsertValue;
        }

        # Check if Skeletonrequest Entity already exists
        try {
            $this->getSingle($id);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException(sprintf(
                'Cannot update skeletonrequest with identifier %d; does not exist',
                $id
            ));
        }

        # Update Metadata
        $aData['modified_by'] = CoreController::$oSession->oUser->getID();
        $aData['modified_date'] = date('Y-m-d H:i:s',time());

        # Update Skeletonrequest
        $this->oTableGateway->update($aData, ['Skeletonrequest_ID' => $id]);

        return $id;
    }

    /**
     * Generate new single Entity
     *
     * @return Skeletonrequest
     * @since 1.0.7
     */
    public function generateNew() {
        return new Skeletonrequest($this->oTableGateway->getAdapter());
    }
}