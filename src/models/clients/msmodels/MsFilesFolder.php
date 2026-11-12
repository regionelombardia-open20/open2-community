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

namespace open20\amos\community\models\clients\msmodels;


use yii\base\Model;

/**
 * Class TeamsChannel
 *
 * Modello che rappresenta la struttura dei dati di un canale di Microsoft Teams.
 *
 * @var string $odataContext URL del contesto OData
 * @var string $id ID del canale o del file
 * @var string $createdDateTime Data e ora di creazione in formato ISO 8601
 * @var string $lastModifiedDateTime Data e ora dell'ultima modifica in formato ISO 8601
 * @var string $name Nome del canale o del file
 * @var string $webUrl URL del file o del canale
 * @var int $size Dimensione del file o della cartella in byte
 * @var array $parentReference Riferimento al genitore con le proprietà driveId e driveType
 * @var string $parentReference['driveId'] ID del drive
 * @var string $parentReference['driveType'] Tipo di drive (es. documentLibrary)
 * @var array $fileSystemInfo Informazioni sul file system con le date di creazione e modifica
 * @var string $fileSystemInfo['createdDateTime'] Data e ora di creazione del file
 * @var string $fileSystemInfo['lastModifiedDateTime'] Data e ora dell'ultima modifica del file
 * @var array $folder Informazioni sulla cartella con il numero di file o cartelle figlie
 * @var int $folder['childCount'] Numero di file o cartelle all'interno della cartella
 */
class MsFilesFolder extends AbstractMsTeams
{
    public $id;
    public $createdDateTime;
    public $lastModifiedDateTime;
    public $name;
    public $webUrl;
    public $size;
    public $parentReference;
    public $fileSystemInfo;
    public $folder;

    public function getDriveId(){
        if(!empty($this->parentReference['driveId'])){
            return $this->parentReference['driveId'];
        }
        return null;
    }
}