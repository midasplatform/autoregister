<?php
/*=========================================================================
 Midas Server
 Copyright Kitware SAS, 26 rue Louis Guérin, 69100 Villeurbanne, France.
 All rights reserved.
 For more information visit http://www.kitware.com/.

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

/** Upgrade the autoregister module to version 1.0.1. */
class Autoregister_Upgrade_1_0_1 extends MIDASUpgrade
{
    /** @var string */
    public $moduleName = 'autoregister';

    /** Post database upgrade. */
    public function postUpgrade()
    {
        // load all the autoregistered communities
        $targetedcommunityModel = MidasLoader::loadModel('Targetedcommunity', 'autoregister');
        $communities = $targetedcommunityModel->getAllTargeted();
        $groupModel = MidasLoader::loadModel('Group');
        $userModel = MidasLoader::loadModel('User');
        foreach ($communities as $community) {
            $memberGroup = $community->getMemberGroup();
            // register all users to all autoregistered communities
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
        }
    }
}
