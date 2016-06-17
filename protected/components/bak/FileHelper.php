<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function readFileToBytes($file) {
    $fp = fopen($file, 'r');
    $content = fread($fp, filesize($file));
    fclose($fp);
    return $content;
}

function createDirectory($dir) {
    if (is_dir($dir) === false) {
        if (mkdir($dir) === false) {
            throw new CException("Error saving data - failed to create directory");
        }
    }
}

// Deletes the directory with all sub-directories and files.
function deleteDirectory($dirPath) {
    if (is_dir($dirPath)) {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != DIRECTORY_SEPARATOR) {
            $dirPath .=DIRECTORY_SEPARATOR;
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDirectory($file);
            } else {
                deleteFile($file);
            }
        }
        rmdir($dirPath);
    }
}

function deleteFile($filename) {
    if (file_exists($filename)) {
        chmod($filename, 0777);
        return unlink($filename);
    }
    return true;
}

function getFileExtension($file) {
    $name = $file->name;
    $extension = substr($name, strrpos($name, "."));
    return $extension;
}