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

/** Base model class template for the autoregister module */
abstract class Autoregister_TargetedcommunityModelBase extends Autoregister_AppModel {

  /** Constructor */
  public function __construct() {
    parent::__construct();
    $this->_name = 'autoregister_targetedcommunity';
    $this->_key = 'targetedcommunity_id';

    $this->_mainData = array(
      'targetedcommunity_id' => array('type' => MIDAS_DATA),
      'community_id' => array('type' => MIDAS_DATA),
      'creation_date' => array('type' => MIDAS_DATA),
      'community' =>  array('type' => MIDAS_MANY_TO_ONE,
                       'model' => 'Community',
                       'parent_column' => 'community_id',
                       'child_column' => 'community_id'));
    $this->initialize();
  }

  /** ensures the passed in community is tracked in autoregister target set */
  public function targetCommunity($community) {
    $targetedcommunities = $this->findBy('community_id', $community->getCommunityId());
    if (!$targetedcommunities) {
        $targetedcommunity = MidasLoader::newDao('TargetedcommunityDao', 'autoregister');
        $targetedcommunity->setCommunityId($community->getCommunityId());
        $this->save($targetedcommunity);
        // now add all users as members
        $groupModel = MidasLoader::loadModel('Group');
        $userModel = MidasLoader::loadModel('User');
        $memberGroup = $community->getMemberGroup();
        $limit = 50;
        $offset = 0;
        while(true) {
            $users = $userModel->getAll(false, $limit, 'lastname', $offset);
            foreach ($users as $user) {
                $groupModel->addUser($memberGroup, $user);
            }
            if (count($users) < $limit) {
                break;
            } else {
                $offset = $offset + $limit;
            }
        }
     } else {
        $targetedcommunity = $targetedcommunities[0];
    }

    return $targetedcommunity;
  }

  /** ensures the passed in community is ignored in autoregister target set */
  public function ignoreCommunity($community) {
    $targetedcommunities = $this->findBy('community_id', $community->getCommunityId());
    if ($targetedcommunities && count($targetedcommunities) > 0) {
        $targetedcommunity = $targetedcommunities[0];
        $this->delete($targetedcommunity);
    }

    return $targetedcommunity;
  }

  /** gets all communities in the autoregister targeted list */
  abstract public function getAllTargeted();
  /** gets all communities that aren't in the autoregister targeted list */
  abstract public function getAllIgnored();

}
