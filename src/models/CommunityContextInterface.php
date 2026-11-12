<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\community
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\community\models;

use yii\db\ActiveQuery;

/**
 * Interface CommunityContextInterface
 * @package open20\amos\community\models
 */
interface CommunityContextInterface
{
    /**
     * Array containing the possible roles of a community Member
     * @return array
     */
    public function getContextRoles();

    /**
     * The name of the basic member role
     * @return string
     */
    public function getBaseRole();

    /**
     * The name of the greatest role a member can have
     * @return string
     */
    public function getManagerRole();

    /**
     * Array containing user permission for a given role
     * @param string $role
     * @return array
     */
    public function getRolePermissions($role);

    /**
     * The community created by the context model (community related to project-management, events or a community itself)
     * @return Community
     */
    public function getCommunityModel();

    /**
     * Array containing the next level for a given initial role
     * @param string $role
     * @return string
     */
    public function getNextRole($role);

    /**
     * For m2m widget actions: return the plugin module name to construct redirect URL
     * @return string
     */
    public function getPluginModule();

    /**
     * For m2m widget actions: return the plugin controller name to construct redirect URL
     * @return string
     */
    public function getPluginController();

    /**
     * For m2m widget actions: return the controller action name to construct redirect URL
     * @return string
     */
    public function getRedirectAction();

    /**
     * Active query to search the users to associate in the additional association page
     *
     * @param integer $communityId Id of the community created by the context model
     * @return ActiveQuery
     */
    public function getAdditionalAssociationTargetQuery($communityId);

}