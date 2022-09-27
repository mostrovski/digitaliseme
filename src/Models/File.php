<?php
namespace Digitaliseme\Models;

use Digitaliseme\Core\Database;

abstract class File {
    // Base class for RawFile, UploadedFile, and DocumentFile
    protected function relocateFile($path, $newPath) {
        return rename($path, $newPath);
    }

    protected function deleteFile($path) {
        return unlink($path);
    }

    protected function downloadFile($path, $name) {
        $extension = pathinfo($path)['extension'];
        $fileName = $name.'.'.$extension;
        $archive = $this->zipFile($path, $fileName);
        if (!file_exists($archive))
        return [
            'success' => false,
            'error'   => DOWNLOAD_FAILURE,
        ];
        $this->forceDownload($archive, $name);
        unlink($archive);
        return ['success' => true];
    }

    protected function zipFile($path, $name) {
        $zip = new \ZipArchive();
        $archive = 'app/downloads/archive.zip';
        $zip->open($archive, \ZipArchive::CREATE);
        $zip->addFile($path, $name);
        $zip->close();
        return $archive;
    }

    protected function forceDownload($archive, $name) {
        ob_end_clean();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename='.$name.'.zip');
        header('Content-Length: ' . filesize($archive));
        readfile($archive);
    }

    protected function createFileRecord($name, $path, $userId=NULL) {
        $db = new Database();

        $sqlForUploads = 'INSERT INTO uploads(filename, filepath, user_id) ';
        $sqlForUploads .= 'values(:filename, :filepath, :user_id)';
        $sqlForDocFiles = 'INSERT INTO doc_files(filename, filepath) ';
        $sqlForDocFiles .= 'values(:filename, :filepath)';

        $created = isset($userId) ?
            $db->insertIntoTable(
                $sqlForUploads,
                [':filename', ':filepath', ':user_id'],
                [$name, $path, $userId]
            ) :
            $db->insertIntoTable(
                $sqlForDocFiles,
                [':filename', ':filepath'],
                [$name, $path]
            );
        return $created ? $created : false;
    }

    protected function readFileRecord($id, $table) {
        $db = new Database();
        $sql = 'SELECT * FROM '.$table.' WHERE id = :id';
        return $db->fetchSingleRow($sql, ':id', $id);
    }

    protected function updateFileRecord($id, $field, $newValue) {
        $holder = ':'.$field;
        $db = new Database();
        $sql = 'UPDATE doc_files SET '.$field.' = '.$holder.' WHERE id = :id';
        $updated = $db->updateTable(
            $sql,
            [$holder, ':id'],
            [$newValue, $id]
        );
        return $updated ? true : false;
    }

    protected function deleteFileRecord($id, $table) {
        $db = new Database();
        $sql = 'DELETE FROM '.$table.' WHERE id = :id';
        $deleted = $db->deleteFromTable($sql, ':id', $id);
        return $deleted ? true : false;
    }
}
?>