<?php

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