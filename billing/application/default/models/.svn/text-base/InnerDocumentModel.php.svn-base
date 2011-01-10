<?php
/**
 * Documents
 *
 * @author tsalik
 */

require_once 'BaseModel.php';
require_once 'InnerDocumentFileModel.php';

class InnerDocument extends BaseModel
{
    protected $_name = 'documents';
    protected $_sequence = 'documents_seq';

    public function getIdAndNameByOwner($owner)
    {
        $sql = "
            SELECT
                d.id, d.title
            FROM
                documents d
                INNER JOIN documents_files df
                    ON d.id = df.document_id AND df.is_active = true
            WHERE
                owner_id = {$owner}
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getInfoByOwner($owner, $all=true)
    {
        $verb = $all ? 'LEFT' : 'INNER';
        $sql = "
            SELECT
                d.id, d.title, d.description, d.hash, df.file_name, df.upload_date, df.mime_type, df.file_hash
            FROM
                documents d
                {$verb} JOIN documents_files df
                    ON df.is_active AND df.document_id = d.id
            WHERE
                owner_id = {$owner}
            ORDER BY
                upload_date
        ";
        return $this->_db->fetchAll($sql);
    }

    public function getInfoByHash($hash)
    {
        $sql = "
            SELECT
                d.id, d.owner_id, d.title, d.description, d.hash, df.file_name, df.upload_date, df.mime_type, df.file_hash
            FROM
                documents d
                LEFT JOIN documents_files df
                    ON df.is_active AND df.document_id = d.id
            WHERE
                d.hash = '{$hash}'
        ";
                
        return $this->_db->fetchRow($sql);
    }

    public function findDocumentByHashAndOwner($ownerId, $hash)
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                    id
                FROM
                    documents WHERE hash='{$hash}' AND owner_id={$ownerId}
            ) AS doc_exists
        ";
        return $this->_db->fetchOne($sql);
    }

    public function deleteByOwnerAndhash($owner, $hash)
    {
        $sql = "
            SELECT
                media
            FROM
                documents
            WHERE
                hash = '{$hash}'
                AND owner_id = {$owner}
        ";
        $media = $this->_db->fetchOne($sql);

        unlink(self::getFilePath($owne, $media));
        $this->delete(array('owner_id'=>$owner, 'hash'=>$hash));
    }

    public function createDocument($owner, $title, $description, $file, $typeId = -1)
    {
        $docId = $this->insert(array(
            'type_id' => $typeId,
            'title' => $title,
            'hash' => crypt('md5', rand() . $owner . rand() . $file['tmp_name'] . rand()),
            'description' => $description,
            'owner_id' => $owner
        ));

        $fileModel = new InnerDocumentFile();
        $fileModel->CreateDocumentFile($owner, $docId, $file);

        return $doc_id;
    }
}
