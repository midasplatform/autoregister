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
require_once BASE_PATH . '/modules/api/library/APIEnabledNotification.php';
/**
 * Notification manager for the autoregister module.
 *
 * @package Modules\Autoregister\Notification
 */
class Autoregister_Notification extends ApiEnabled_Notification
{
    /** @var string */
    public $moduleName = 'autoregister';
    public $_moduleComponents = array('Api');

    /**  Initialize the notification process. */
    public function init() {
        $fc = Zend_Controller_Front::getInstance();
        $this->moduleWebroot = $fc->getBaseUrl().'/modules/'.$this->moduleName;
        $this->coreWebroot = $fc->getBaseUrl().'/core';
        $this->enableWebAPI($this->moduleName);
        $this->addCallBack('CALLBACK_CORE_NEW_USER_ADDED', 'handleUserAdded');
        $this->addCallBack('CALLBACK_CORE_NEW_COMMUNITY_ADDED', 'handleCommunityAdded');
    }
    //TODO
    //db change
    //qtip
    //adapt existing changes/callbacks to list
    ///** when created, take all comm and add them to db */
    /* when a comm is deleted, if in our db, remove it */
    /* this db is comm and then a boolean that is registered */
    /**
     *
     * @param array $params parameters
     */
    public function handleUserAdded($params) {
        $user = $params['userDao'];
        $communityModel = MidasLoader::loadModel('Community');
        $groupModel = MidasLoader::loadModel('Group');
        $communities = $communityModel->getAll();
        foreach ($communities as $community) {
            $memberGroup = $community->getMemberGroup();
            $groupModel->addUser($memberGroup, $user);
        }
    }

    /**
     *
     * @param array $params parameters
     */
    public function handleCommunityAdded($params) {
        $community = $params['community'];
        $groupModel = MidasLoader::loadModel('Group');
        $userModel = MidasLoader::loadModel('User');
        $users = $userModel->getAll();
        $memberGroup = $community->getMemberGroup();
        foreach ($users as $user) {
            $groupModel->addUser($memberGroup, $user);
        }
    }
}
