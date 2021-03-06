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

require_once BASE_PATH.'/modules/api/library/APIEnabledNotification.php';

/** Notification manager for the autoregister module. */
class Autoregister_Notification extends ApiEnabled_Notification
{
    /** @var string */
    public $moduleName = 'autoregister';
    public $_moduleComponents = array('Api');

    /** Initialize the notification process. */
    public function init()
    {
        $fc = Zend_Controller_Front::getInstance();
        $this->moduleWebroot = $fc->getBaseUrl().'/modules/'.$this->moduleName;
        $this->coreWebroot = $fc->getBaseUrl().'/core';
        $this->enableWebAPI($this->moduleName);
        $this->addCallBack('CALLBACK_CORE_NEW_USER_ADDED', 'handleUserAdded');
        $this->addCallBack('CALLBACK_CORE_NEW_COMMUNITY_ADDED', 'handleCommunityAdded');
        $this->addCallBack('CALLBACK_CORE_COMMUNITY_DELETED', 'handleCommunityDeleted');
    }

    /**
     * @param array $params parameters
     */
    public function handleUserAdded($params)
    {
        $user = $params['userDao'];
        $targetedcommunityModel = MidasLoader::loadModel('Targetedcommunity', 'autoregister');
        $communities = $targetedcommunityModel->getAllTargeted();
        $groupModel = MidasLoader::loadModel('Group');
        foreach ($communities as $community) {
            $memberGroup = $community->getMemberGroup();
            $groupModel->addUser($memberGroup, $user);
        }
    }

    /**
     * @param array $params parameters
     */
    public function handleCommunityAdded($params)
    {
        $settingModel = MidasLoader::loadModel('Setting');
        $default = $settingModel->getValueByName('defaultAutoregister', 'autoregister');
        if ($default === 'true') {
            $community = $params['community'];
            $targetedcommunityModel = MidasLoader::loadModel('Targetedcommunity', 'autoregister');
            $targetedcommunityModel->targetCommunity($community);
        }
    }

    /**
     * @param array $params parameters
     */
    public function handleCommunityDeleted($params)
    {
        $community = $params['community'];
        $targetedcommunityModel = MidasLoader::loadModel('Targetedcommunity', 'autoregister');
        $targetedcommunityModel->ignoreCommunity($community);
    }
}
