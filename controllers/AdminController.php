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

/** Admin controller for the autoregister module. */
class Autoregister_AdminController extends Autoregister_AppController
{
    /** Index action. */
    public function indexAction()
    {
        $this->requireAdminPrivileges();
        $settingModel = MidasLoader::loadModel('Setting');
        $this->view->defaultAutoregister = $settingModel->getValueByName('defaultAutoregister', 'autoregister');
        $communityModel = MidasLoader::loadModel('Community');
        $targetedcommunityModel = MidasLoader::loadModel('Targetedcommunity', 'autoregister');
        $this->view->targeted = $targetedcommunityModel->getAllTargeted();
        $this->view->ignored = $targetedcommunityModel->getAllIgnored();
        $this->view->pageTitle = 'Autoregister Module Configuration';
        session_start();
    }
}
