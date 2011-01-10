<?php
/**
 * Documents Files
 *
 * @author tsalik
 */

require_once('BaseModel.php');

class InnerDocumentFile extends BaseModel
{
    protected $_name = 'documents_files';
    protected $_sequence = 'documents_files_seq';

    const DOCUMENTS_DIR = 'documents';

    static public function getFilePath($owner, $media)
    {
        return realpath(BILLING_PATH) . '/' .
            self::DOCUMENTS_DIR . "/{$owner}/" .
            $media;
    }

    public function CreateDocumentFile($owner, $doc, $file)
    {
        $dir = realpath(BILLING_PATH) . '/' . self::DOCUMENTS_DIR . "/{$owner}/";
        if (!file_exists($dir))
        {
            mkdir($dir);
        }
        $fileName = date('[Y-m-d-H-i-s]') . $file['name'];
        //var_dump(array('source'=>$file['tmp_name'], 'dest'=>$dir . $fileName)); exit;
        if (!move_uploaded_file($file['tmp_name'], $dir . $fileName))
        {
            throw new Exception('Не удалось сохранить файл.');
        }

        return $this->insert(array(
            'file_name' => $file['name'],
            'file_hash' => hash_file('md5', $dir . $fileName),
            'document_id' => $doc,
            'media' => $fileName,
            'mime_type' => $file['type']
        ));
    }

    private function setAllFilesAsInactive($doc)
    {
        $this->update(array('is_active' => 'false'), "document_id={$doc}");
    }

    public function addDocumentFile($owner, $doc, $file)
    {
        $this->setAllFilesAsInactive($doc);
        $this->CreateDocumentFile($owner, $doc, $file);
    }

    public function getFilesByDocument($doc)
    {
        $sql = "
            SELECT
                file_name, file_hash, is_active, media, upload_date
            FROM
                documents_files
            WHERE
                document_id = {$doc}
            ORDER BY upload_date desc
        ";
                
        return $this->_db->fetchAll($sql);
    }

    public function setActiveByHash($doc, $hash)
    {
        $this->startTransaction();
        try
        {
            $this->update(array('is_active' => 'false'), "document_id={$doc}");
            $this->update(array('is_active' => 'true'), "document_id={$doc} AND file_hash='{$hash}'");
        }
        catch (Exception $ex)
        {
            $this->rollbackTransaction();
            throw $ex;
        }
        $this->commitTransaction();
        return $this->getFileInfoByHash($hash);
    }

    public function removeByHash($doc, $hash)
    {
        $this->startTransaction();
        try
        {
            $oldDoc = $this->getFileInfoByHash($hash);
            $this->delete("file_hash='{$hash}' and document_id={$doc}");

            if ($oldDoc['is_active'])
            {
                $sql = "
                    SELECT
                        id, file_name, is_active, media, mime_type, upload_date, file_hash
                    FROM
                        {$this->_name}
                    WHERE
                        document_id={$doc}
                    ORDER BY upload_date desc
                    LIMIT 1
                ";
                $newDoc = $this->_db->fetchRow($sql);

                if ($newDoc)
                {
                    $this->update(array('is_active' => 'true'), "id={$newDoc['id']}");
                }
            }
            else
            {
                $sql = "
                    SELECT
                        id, file_name, is_active, media, mime_type, upload_date, file_hash
                    FROM
                        {$this->_name}
                    WHERE
                        document_id={$doc} AND is_active = true
                ";
                $newDoc = $this->_db->fetchRow($sql);
            }
        }
        catch (Exception $ex)
        {
            $this->rollbackTransaction();
            throw $ex;
        }
        $this->commitTransaction();

        return $newDoc;
    }

    public function getFileInfoByHash($hash)
    {
        $sql = "
            SELECT
                df.file_name, df.is_active, df.media, df.mime_type, df.upload_date, df.file_hash, d.owner_id
            FROM
                documents_files df
                INNER JOIN documents d
                    ON df.document_id = d.id
            WHERE
                df.file_hash = '{$hash}'
        ";
                
        return $this->_db->fetchRow($sql);
    }
}