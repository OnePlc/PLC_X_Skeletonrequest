<?php
/**
 * Skeletonrequest.php - Skeletonrequest Entity
 *
 * Entity Model for Skeletonrequest
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
use Application\Model\CoreEntityModel;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Select;

class Skeletonrequest extends CoreEntityModel {
    public $label;

    /**
     * Skeletonrequest constructor.
     *
     * @param AdapterInterface $oDbAdapter
     * @since 1.0.0
     */
    public function __construct($oDbAdapter) {
        parent::__construct($oDbAdapter);

        # Set Single Form Name
        $this->sSingleForm = 'skeletonrequest-single';

        # Attach Dynamic Fields to Entity Model
        $this->attachDynamicFields();
    }

    /**
     * Set Entity Data based on Data given
     *
     * @param array $aData
     * @since 1.0.0
     */
    public function exchangeArray(array $aData) {
        $this->id = !empty($aData['Skeletonrequest_ID']) ? $aData['Skeletonrequest_ID'] : 0;
        $this->label = !empty($aData['label']) ? $aData['label'] : '';

        $this->updateDynamicFields($aData);
    }

    /**
     * Get Matching Articles to Request
     *
     * @return array Matchings Results as Skeleton Entities
     * @since 1.0.0
     * @addedtoskeleton
     * @requires 1.0.5
     * @campatibleto master-dev
     */
    public function getMatchingResults() {
        # Init Skeleton Table
        if(!array_key_exists('skeleton',CoreController::$aCoreTables)) {
            CoreController::$aCoreTables['skeleton'] = new TableGateway('skeleton',CoreController::$oDbAdapter);
        }
        # Init Tags Table
        if(!array_key_exists('core-tag',CoreController::$aCoreTables)) {
            CoreController::$aCoreTables['core-tag'] = new TableGateway('core_tag',CoreController::$oDbAdapter);
        }
        # Init Entity Tags Table
        if(!array_key_exists('core-entity-tag',CoreController::$aCoreTables)) {
            CoreController::$aCoreTables['core-entity-tag'] = new TableGateway('core_entity_tag',CoreController::$oDbAdapter);
        }
        # Init Entity Tags Table
        if(!array_key_exists('core-entity-tag-entity',CoreController::$aCoreTables)) {
            CoreController::$aCoreTables['core-entity-tag-entity'] = new TableGateway('core_entity_tag_entity',CoreController::$oDbAdapter);
        }

        try {
            $oSkeletonResultTbl = CoreController::$oServiceManager->get(\OnePlace\Skeleton\Model\SkeletonTable::class);
        } catch(\RuntimeException $e) {
            throw new \RuntimeException(sprintf(
                'Could not load entity table needed for matching'
            ));
        }

        # Init Empty List
        $aMatchedArticles = [];

        # Get Matches Skeleton by Category
        $aMatchedArticles = $this->matchByAttribute('category');
        $bCategoryFilterActive = (count($aMatchedArticles) > 0) ? true : false;
        $bModelFilterActive = false;
        if(!$bCategoryFilterActive) {
            $aMatchedArticles = $this->matchByAttribute('model');
            $bModelFilterActive = (count($aMatchedArticles) > 0) ? true : false;
        } else {
            # todo: Make AND filter
        }

        if(!$bCategoryFilterActive && !$bModelFilterActive) {
            $aMatchedArticles = $this->matchByAttribute('manufacturer');
        } else {
           # todo: Make AND filter
        }

        /**
         * Enforce State for Matching Results
         */
        if(count($aMatchedArticles) > 0) {
            # Check if state tag is present
            $sTagKey = 'state';
            $oTag = CoreController::$aCoreTables['core-tag']->select(['tag_key'=>$sTagKey]);
            if(count($oTag) > 0) {
                # check if enforce state option for request is active
                $sState = CoreController::$aGlobalSettings['skeletonrequest-enforce-state'];
                if($sState != '') {
                    # enforce state for results
                    $aEnforcedMatches = [];
                    $oTag = $oTag->current();
                    $oEntityTag = CoreController::$aCoreTables['core-entity-tag']->select(['tag_value' => $sState, 'tag_idfs' => $oTag->Tag_ID]);

                    # check if state exists for entity
                    if (count($oEntityTag) > 0) {
                        $oEntityTag = $oEntityTag->current();
                        # compare state for all matches, only add matching
                        foreach (array_keys($aMatchedArticles) as $sMatchKey) {
                            $oMatch = $aMatchedArticles[$sMatchKey];
                            if ($oMatch->getSelectFieldID('state_idfs') == $oEntityTag->Entitytag_ID) {
                                $aEnforcedMatches[] = $oMatch;
                            }
                        }
                    }
                    # return curated results
                    $aMatchedArticles = $aEnforcedMatches;
                }
            }
        }

        return $aMatchedArticles;
    }

    private function matchByAttribute($sTagKey) {
        try {
            $oSkeletonResultTbl = CoreController::$oServiceManager->get(\OnePlace\Skeleton\Model\SkeletonTable::class);
        } catch(\RuntimeException $e) {
            throw new \RuntimeException(sprintf(
                'Could not load entity table needed for matching'
            ));
        }
        $aMatchedArticles = [];
        # Match Skeleton by Category - only if category tag is found
        $oTag = CoreController::$aCoreTables['core-tag']->select(['tag_key'=>$sTagKey]);
        if(count($oTag)) {
            $oTag = $oTag->current();
            # 1. Get all Categories linked to this request
            $oCategorySel = new Select(CoreController::$aCoreTables['core-entity-tag-entity']->getTable());
            $oCategorySel->join(['cet'=>'core_entity_tag'],'cet.Entitytag_ID = core_entity_tag_entity.entity_tag_idfs');
            $oCategorySel->where(['entity_idfs'=>$this->getID(),'cet.tag_idfs = '.$oTag->Tag_ID,'entity_type'=>'skeletonrequest']);
            $oMyCats = CoreController::$aCoreTables['core-entity-tag']->selectWith($oCategorySel);
            if(count($oMyCats) > 0) {
                # Loop over all matched categories
                foreach($oMyCats as $oMyCat) {
                    # Find skeleton with the same category
                    $oMatchedArtsByCat = CoreController::$aCoreTables['core-entity-tag-entity']->select(['entity_tag_idfs'=>$oMyCat->Entitytag_ID,'entity_type'=>'skeleton']);
                    if(count($oMatchedArtsByCat) > 0) {
                        foreach($oMatchedArtsByCat as $oMatchRow) {
                            $aMatchedArticles[] = $oSkeletonResultTbl->getSingle($oMatchRow->entity_idfs);
                        }
                    }
                }
            }
        }

        return $aMatchedArticles;
    }
    /**
     * @addedtoskeletonend
     */
}