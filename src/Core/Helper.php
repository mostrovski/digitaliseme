<?php

namespace Digitaliseme\Core;

use JetBrains\PhpStorm\NoReturn;

class Helper {
    /*************************************************************************
     *
     *                                 GENERAL
     *
     ************************************************************************/
    public static function isUserLoggedIn() {
        return isset($_SESSION["loggedin"]);
    }

    public static function redirect(string $url): void {
        if (! str_starts_with($url, config('app.url'))) {
            $url = config('app.url').ltrim($url, '/');
        }
        header('Location: '.$url);
        exit;
    }

    public static function getGreeting() {
        $greeting = self::isUserLoggedIn() ?
        'you are in, '.$_SESSION["loggedinName"].' &check;' :
        config('app.info.greeting');
        return $greeting;
    }
    /*************************************************************************
     *
     *                                 FILES
     *
     ************************************************************************/
    public static function extractFileName($file) {
        $name = mb_substr($file, 0, mb_strrpos($file, '.'));
        return $name;
    }

    public static function extractFileExtension($file) {
        $ext = mb_substr($file, mb_strrpos($file, '.') + 1);
        return $ext;
    }

    public static function defineDirectoryFor($selectedType) {
        $dir = 'app/archive/'.$selectedType.'/';
        return $dir;
    }

    public static function redefinePath($path, $directory) {
        $fileName = mb_substr($path, (mb_strrpos($path, '/') + 1));
        return $directory.$fileName;
    }
    /*************************************************************************
     *
     *                                 UPLOADS
     *
     ************************************************************************/
    public static function fetchUploads() {
        $db = new Database();
        $sql = 'SELECT * FROM uploads WHERE user_id = :user_id';
        return $db->fetchMultipleRows(
            $sql,
            ':user_id',
            $_SESSION["loggedinID"]
        );
    }

    public static function drawUploadsTable($files) {
        $table = '';
        $num = 1;
        foreach ($files as $file) {
            $buttons = self::createButtonsForUploads(
                $file->publicPath(),
                $file->id
            );
            $table .= '<tr>';
            $table .= '<td>'.$num.'</td>';
            $table .= '<td>'.$file->filename.'</td>';
            $table .= '<td>'.$file->created_at.'</td>';
            $table .= '<td>'.$buttons['preview'].'</td>';
            $table .= '<td>'.$buttons['proceed'].'</td>';
            $table .= '<td>'.$buttons['delete'].'</td>';
            $table .= '</tr>';
            $num++;
        }
        return $table;
    }

    protected static function createButtonsForUploads($path, $id) {
        return [
            'preview' => self::createPreviewButton($path),
            'proceed' => self::createProcessButton($id),
            'delete'  => self::createDeleteButton($id),
        ];
    }

    protected static function createPreviewButton($path) {
        $link = '<a class="gray_button" target="_blank" href="';
        $link .= $path.'">preview</a>';
        return $link;
    }

    protected static function createProcessButton($id) {
        $link = '<a class="green_button" href="';
        $link .= config('app.url').'documents/create/'.$id.'">process</a>';
        return $link;
    }

    protected static function createDeleteButton($id) {
        $link = '<a class="red_button" href="';
        $link .= config('app.url').'uploads/delete/'.$id.'" ';
        $link .='onclick="return confirm(\'Delete this file?\')">delete</a>';
        return $link;
    }
    /*************************************************************************
     *
     *                                 DOCUMENTS
     *
     ************************************************************************/
    public static function fetchDocuments() {
        $db = new Database();
        $sql = 'SELECT documents.id, doctitle, type, saved, file_id ';
        $sql .= 'FROM documents JOIN doc_types ';
        $sql .= 'ON doctype_id = doc_types.id JOIN doc_files ';
        $sql .= 'ON file_id = doc_files.id ';
        $sql .= 'ORDER BY saved DESC';
        return $db->fetchWithoutParams($sql);
    }

    public static function fetchDocumentTypes() {
        $db = new Database();
        $sql = 'SELECT * FROM doc_types';
        return $db->fetchWithoutParams($sql);
    }

    public static function drawDocumentsTable($documents) {
        $table = '';
        $num = 1;
        foreach ($documents as $doc) {
            $buttons = self::createButtonsForDocuments($doc->id);
            $table .= '<tr>';
            $table .= '<td>'.$num.'</td>';
            $table .= '<td>'.$doc->doctitle.'</td>';
            $table .= '<td>'.$doc->type.'</td>';
            $table .= '<td>'.$doc->saved.'</td>';
            $table .= '<td>'.$buttons['details'].'</td>';
            $table .= '<td>'.$buttons['download'].'</td>';
            $table .= '</tr>';
            $num++;
        }
        return $table;
    }

    protected static function createButtonsForDocuments($docId) {
        return [
            'details'  => self::createDetailsButton($docId),
            'download' => self::createDownloadButton($docId),
        ];
    }

    protected static function createDetailsButton($id) {
        $link = '<a class="green_button" href="';
        $link .= config('app.url').'documents/show/'.$id.'">details</a>';
        return $link;
    }

    protected static function createDownloadButton($id) {
        $link = '<a class="gray_button" href="';
        $link .= config('app.url').'documents/download/'.$id.'">download</a>';
        return $link;
    }
    /*************************************************************************
     *
     *                                 KEYWORDS
     *
     ************************************************************************/
    public static function getKeywordsArrayFrom($string) {
        $keywords = explode(',', $string);
        $keywords = array_map('trim', $keywords);
        $keywords = array_filter($keywords, 'strlen');
        return $keywords;
    }

    public static function getKeywordsStringFrom($array) {
        $keywords = implode(', ', $array);
        return $keywords;
    }
}
