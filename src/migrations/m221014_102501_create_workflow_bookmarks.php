<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-community
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

use yii\helpers\ArrayHelper;
use open20\amos\core\migration\AmosMigrationWorkflow;
use \open20\amos\community\models\Bookmarks;

class m221014_102501_create_workflow_bookmarks extends AmosMigrationWorkflow
{
    /**
     * @inheritdoc
     */
    protected function setWorkflow()
    {
        return ArrayHelper::merge(
            parent::setWorkflow(),
            $this->workflowConf(),
            $this->workflowStatusConf(),
            $this->workflowTransitionsConf(),
            $this->workflowMetadataConf()
        );
    }

    /**
     * In this method there are the new workflow configuration.
     * @return array
     */
    private function workflowConf()
    {
        return [
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW,
                'id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'initial_status_id' => 'DRAFT'
            ]
        ];
    }

    /**
     * In this method there are the new workflow statuses configurations.
     * @return array
     */
    private function workflowStatusConf()
    {
        return [
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_STATUS,
                'id' => 'DRAFT',
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'label' => 'Bozza',
                'sort_order' => '0'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_STATUS,
                'id' => 'PUBLISHED',
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'label' => 'Pubblicato',
                'sort_order' => '1'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_STATUS,
                'id' => 'TOVALIDATE',
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'label' => 'Da validare',
                'sort_order' => '1'
            ]
        ];
    }

    /**
     * In this method there are the new workflow status transitions configurations.
     * @return array
     */
    private function workflowTransitionsConf()
    {
        return [
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'start_status_id' => 'DRAFT',
                'end_status_id' => 'TOVALIDATE'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'start_status_id' => 'TOVALIDATE',
                'end_status_id' => 'PUBLISHED'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'start_status_id' => 'PUBLISHED',
                'end_status_id' => 'DRAFT'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'start_status_id' => 'DRAFT',
                'end_status_id' => 'PUBLISHED'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_TRANSITION,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'start_status_id' => 'TOVALIDATE',
                'end_status_id' => 'DRAFT'
            ]
        ];
    }

    /**
     * In this method there are the new workflow metadata configurations.
     * @return array
     */
    private function workflowMetadataConf()
    {
        return [
            // DRAFT
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'DRAFT',
                'key' => 'buttonLabel',
                'value' => 'Rimetti in bozza'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'DRAFT',
                'key' => 'PUBLISHED_label',
                'value' => 'Togli dalla Pubblicazione',
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'DRAFT',
                'key' => 'description',
                'value' => 'Bozza'
            ],
            // PUBLISHED
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'PUBLISHED',
                'key' => 'buttonLabel',
                'value' => 'Pubblica'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'PUBLISHED',
                'key' => 'description',
                'value' => 'Pubblica',
            ],
            // TOVALIDATE
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'TOVALIDATE',
                'key' => 'buttonLabel',
                'value' => 'Richiedi validazione'
            ],
            [
                'type' => AmosMigrationWorkflow::TYPE_WORKFLOW_METADATA,
                'workflow_id' => Bookmarks::BOOKMARKS_WORKFLOW,
                'status_id' => 'TOVALIDATE',
                'key' => 'description',
                'value' => 'Richiedi validazione',
            ],
        ];
    }
}