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

/** Component for api methods */
class Autoregister_ApiComponent extends AppComponent {


  /**
   * Helper function for verifying keys in an input array
   */
  private function _checkKeys($keys, $values) {
    foreach ($keys as $key) {
      if (!array_key_exists($key, $values)) {
        throw new Exception('Parameter '.$key.' must be set.', 400);
      }
    }
  }

  /**
   * Helper function to get the user from token or session authentication
   */
  private function _getUser($args) {
    $authComponent = MidasLoader::loadComponent('Authentication');
    return $authComponent->getUser($args, $this->userSession->Dao);
  }

  /**
   * Enable curation for a folder, calling user requires ADMIN
   * access on folder.
   * @param folder_id id of the folder to enable curation.
   * @return curatedfolder.
   */
  public function registerCommunity($args) {
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
    } else if($register === 'target') {
        $targetedcommunityModel->targetCommunity($communityDao);
    } else {
        throw new Exception('Register must be one of [ignore|target].', 401);
    }

    return array('register' => $register, 'community_id' => $communityDao->getKey());
  }

} // end class
