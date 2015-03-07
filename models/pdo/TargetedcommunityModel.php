<?php
/*=========================================================================
 MIDAS Server
 Copyright (c) Kitware SAS. 26 rue Louis Guérin. 69100 Villeurbanne, FRANCE
 All rights reserved.
 More information http://www.kitware.com

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0.txt

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
=========================================================================*/

require_once BASE_PATH.'/modules/autoregister/models/base/TargetedcommunityModelBase.php';
require_once BASE_PATH.'/modules/autoregister/models/dao/TargetedcommunityDao.php';

/** PDO model template for the autoregister module */
class Autoregister_TargetedcommunityModel extends Autoregister_TargetedcommunityModelBase {

    /** gets all communities in the autoregister targeted list */
    public function getAllTargeted() {
        $sql = $this->database->select();
        $rowset = $this->database->fetchAll($sql);
        $all = array();
        foreach ($rowset as $row) {
          $targetedcommunity = $this->initDao('Targetedcommunity', $row, 'autoregister');
          $all[$targetedcommunity->getCommunityId()] = $targetedcommunity->getCommunity();
        }

        return $all;
    }

    /** gets all communities that aren't in the autoregister targeted list */
    public function getAllIgnored() {
        $select = "select * from community where community_id not in (select community_id from autoregister_targetedcommunity";
        $communityModel = MidasLoader::loadModel('Community');
        $communities = $communityModel->getAll();
        $targeted = $this->getAllTargeted();
        $ignored = array();
        foreach ($communities as $community) {
            if (!array_key_exists($community->getCommunityId(), $targeted)) {
                $ignored[$community->getCommunityId()] = $community;
            }
        }

        return $ignored;
    }

}
