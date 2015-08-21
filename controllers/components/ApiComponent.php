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

/** Component for api methods. */
class Autoregister_ApiComponent extends AppComponent
{
    /**
     * Helper function for verifying keys in an input array.
     */
    private function _checkKeys($keys, $values)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $values)) {
                throw new Exception('Parameter '.$key.' must be set.', 400);
            }
        }
    }

    /**
     * Helper function to get the user from token or session authentication.
     */
    private function _getUser($args)
    {
        $authComponent = MidasLoader::loadComponent('Authentication');

        return $authComponent->getUser($args, $this->userSession->Dao);
    }

    /**
     * Set the autoregister status on a community, calling user requires ADMIN
     * access on folder.
     *
     * @param communityId : id of the community to change autoregister status
     * @param register : autoregister status to set on the community ['ignore','target']
     *
     * @return array('register' => register value, 'community_id' => communityId).
     */
    public function registerCommunity($args)
    {
        $userDao = $this->_getUser($args);
        if (!$userDao) {
            throw new Exception('You must login to autoregister a community.', 401);
        }
        if (!$userDao->getAdmin()) {
            throw new Exception('You must be a site admin to autoregister a community.', 401);
        }

        $this->_checkKeys(array('communityId', 'register'), $args);
        $communityModel = MidasLoader::loadModel('Community');
        $communityDao = $communityModel->load($args['communityId']);
        if (!$communityDao) {
            throw new Exception('No community found with that id.', 404);
        }

        $register = $args['register'];
        $targetedcommunityModel = MidasLoader::loadModel('Targetedcommunity', 'autoregister');
        if ($register === 'ignore') {
            $targetedcommunityModel->ignoreCommunity($communityDao);
        } elseif ($register === 'target') {
            $targetedcommunityModel->targetCommunity($communityDao);
        } else {
            throw new Exception('Register must be one of [ignore|target].', 401);
        }

        return array('register' => $register, 'community_id' => $communityDao->getKey());
    }

    /**
     * Set the default autoregister setting, which determines whether newly created
     * communities will be added to the autoregister list; Admin access required.
     *
     * @param default : autoregister default for new communities [true|false]
     *
     * @return array('default' => default)
     */
    public function defaultAutoregisterSetting($args)
    {
        $userDao = $this->_getUser($args);
        if (!$userDao) {
            throw new Exception('You must login to change the default autoregister setting.', 401);
        }
        if (!$userDao->getAdmin()) {
            throw new Exception('You must be a site admin to change the default autoregister setting.', 401);
        }

        $this->_checkKeys(array('default'), $args);
        $default = $args['default'];
        $settingModel = MidasLoader::loadModel('Setting');
        if ($default === 'true' || $default === 'false') {
            $settingModel->setConfig('defaultAutoregister', $default, 'autoregister');
        } else {
            throw new Exception('Default must be one of [true|false].', 401);
        }

        return array('default' => $default);
    }
}
