<?php

namespace App\Http\Controllers\Admin\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Pfile extends Controller {

    private static $instance = null;
    private static $rootPath = null;
    public static $currentDir = '';
    public static $extensionFiles = array(
        'pdf' => 'file-pdf-o',
        'txt' => 'file-text-o',
        'default' => 'file-o',
        'currentDir' => 'folder-open-o',
        'dir' => 'folder-o',
        'png' => 'picture-o',
        'jpg' => 'picture-o',
        'gif' => 'picture-o',
        'odt' => 'file-word-o',
        'docx' => 'file-word-o',
        'doc' => 'file-word-o',
        'xls' => 'file-excel-o',
        'code' => 'file-code-o',
        'css' => 'css3',
        'html' => 'css3',
        'php' => 'file-code-o'
    );

    public function __construct() {
        self::$rootPath = public_path();
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Pfile();
        }
        return self::$instance;
    }

    /**
     * załadowanie wisoku testowego
     * @return type
     */
    public function view() {
        return view('admin.upload');
    }

    /**
     * przeładowanie plików w folderze
     * @param type $pathFile
     * @return type
     */
    public function files($pathFile) {
        $tmp = '';
        $prevDir = '';
        $files = array();
        $dir = isset($pathFile) && !empty($pathFile) ? $pathFile : '';

        $tmp = self::makeMap($dir);
        if (self::isDir(self::$rootPath . '/' . $dir)) {
            $files = self::getContent(self::$rootPath . '/' . $dir);
            $tmp = self::makeMap($dir);
            $prevDir = self::makeMapPrev($tmp);
        }

        $data = array(
            'files' => $files,
            'current' => $tmp . '/',
            'prev' => $prevDir,
            'getFile' => array(self::isFile('./' . $tmp), $tmp)
        );

        echo json_encode($data);
        return;
    }

    /**
     * tworzenie nowego katalogu
     * @param Request $request
     * @return type
     */
    public function newdir(Request $request) {
// TODO validator request
        $get = $request->all();
//sprawdza nazwe pliku
        $dir = isset($get['dirname']) && !empty($get['dirname']) ? $get['dirname'] : '';
//sprawdza lokalizacje gdzie tworzyć plik
        $dirLocation = isset($get['currentLocation']) && !empty($get['currentLocation']) ? $get['currentLocation'] : '/';
// cała scieżka
        $path = self::$rootPath . $dirLocation . $dir;
        if (!empty($dir)) {
//pobieramy zawartosc lokalizacji
            $files = self::getContent(self::$rootPath . $dirLocation);
//sprawdza czy niema juz takiego pliku dla pewności
            $existFile = array_filter($files, function($v, $k)use($dir) {
                return $v[0] == $dir;
            }, ARRAY_FILTER_USE_BOTH);
// i jeszcze raz sprawdza przed utworzeniem
            if (empty($existFile) && !self::isDir($path)) {
//robimy plik
                self::makeDir($path);
                echo json_encode(array('success' => $dir . ' plik utworzono'));
                return;
            } else {
                echo json_encode(array('error' => 'podana nazwa pliku już istanieje'));
                return;
            }
        }
    }
    /**
     * TODO upload duzych plików nie działa
     * upload plików poki co nie działa za duzych plików 30mb nie wiecej
     * @param Request $request
     */
    public function upload(Request $request) {
        $data = $request->all();
        $dir = isset($data['path']) && !empty($data['path']) ? $data['path'] : '/';
// print_r(self::$rootPath . $dir.$request->file->getClientOriginalName());
        if ($request->hasFile('file')) {
            $name = $request->file->getClientOriginalName();
            $path = self::$rootPath . $dir . $name;
            if (self::isFile($path) || self::isDir($path)) {
                $name = $request->file->hashName() . "_" . $name;
            }
            $request->file->move(self::$rootPath . $dir, $name);
        }
    }
    /**
     * TODO sprawdzanie uprawnień dla kasowaniego pliku
     * kasowanie plików
     * @param Request $request
     * @return type
     */
    public function deleteDir(Request $request) {
// TODO validator request

        $get = $request->all();
//sprawdza nazwe pliku
        $dir = isset($get['dirname']) && !empty($get['dirname']) ? $get['dirname'] : '';
        $dir = self::$rootPath . '/' . $dir;
        $dir = str_replace(array("..", "../", "//"), array('', '', "/"), $dir);
        if (!empty($dir) && self::$rootPath != $dir) {
            if (self::isDir($dir)) {
                if (empty(self::getContent($dir))) {
//                      usuwanie zawartości lepiej nie dodawać
//                      array_map('unlink', glob("some/dir/*.*"));
                    rmdir($dir);
                    echo json_encode(array('success' => 'katalog usunieto!'));
                    return;
                } else {
                    echo json_encode(array('error' => 'katalog nie jest pusty!'));
                    return;
                }
                echo 'kasuje ' . $dir;
            } elseif (self::isFile($dir)) {
                unlink($dir);
                echo json_encode(array('success' => 'plik usunieto!'));
                return;
            }
        }
        echo json_encode(array('error' => 'coś sie nie udało!'));
        return;
    }

    /**
     * tworzenie nowego pliku
     * @param type $path
     */
    private function makedir($path) {
        if (!mkdir($path, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    /**
     * TODO validator request
     * ładowanie widoku maganera
     * @param Request $request
     */
    public function index(Request $request) {

        $get = $request->all();
        $tmp = '';
        $prevDir = '';
        $files = array();
        $dir = isset($get['file']) && !empty($get['file']) ? $get['file'] : '';

        $tmp = self::makeMap($dir);
        if (self::isDir(self::$rootPath . '/' . $dir)) {
            $files = self::getContent(self::$rootPath . '/' . $dir);
            $tmp = self::makeMap($dir);
            $prevDir = self::makeMapPrev($tmp);
        }

        $data = array(
            'files' => $files,
            'current' => $tmp . '/',
            'prev' => $prevDir,
            'getFile' => array(self::isFile('./' . $tmp), $tmp)
        );

        echo view('admin.helpers.pfile', $data);
        return;
    }

    private function isFile($path) {
        return is_file($path);
    }

    private function isDir($path) {
        return is_dir($path);
    }
    /**
     * walidacja sciezki do pliku, usuwanie .. zeby nie dało sie przejść do home<-
     * @param type $path
     * @return string
     */
    private function makeMap($path) {
        $tmp = '';
        if (!empty($path)) {
            foreach (explode('/', $path) as $directory) {
                if ($directory !== '..' && !empty($directory)) {
                    $tmp .= '/' . $directory;
                }
            }
        }
        return $tmp;
    }
    /**
     * tworzenie sciezki do poprzedniego katalogu
     * blokada przy base path.
     * @param type $path
     * @return string
     */
    private function makeMapPrev($path) {

        $tmp = '';
        if (!empty($path)) {
            $array = preg_split("/\//", $path);
            array_pop($array);
            if (count($array) >= 1) {
                foreach ($array as $directory) {
                    if ($directory !== '..' && !empty($directory)) {
                        $tmp .= '/' . $directory;
                    }
                }
            } else {
                $tmp = '/' . $array;
            }
        }
        return $tmp;
    }
    /**
     * pobieranie zawartości katalogów wraz z informacjami o plikach 
     * icon, size
     * @param type $path
     * @return type
     */
    private function getContent($path) {
        $data = array();
        $files = scandir($path);
        unset($files[0]);
        unset($files[1]);
        foreach ($files as $file) {

            $info = self::fileInfo($file, $path);

            $data[] = array($file, $info);
        }
        return $data;
    }
    /**
     * wyciaganie informacji o pliku, katalogu (format, rozmiar)
     * @param type $_file
     * @param type $path
     * @return type
     */
    public function fileInfo($_file, $path) {

        $file = pathinfo($_file);
        $size = filesize($path . '/' . $_file);

        $data = array();
        if (self::isDir($path . '/' . $_file)) {
            $data['icon'] = self::$extensionFiles['dir'];
        } else {
            $data['icon'] = isset($file['extension']) && isset(self::$extensionFiles[$file['extension']]) ? self::$extensionFiles[$file['extension']] : self::$extensionFiles['default'];
        }
        $data['size'] = $this->getSize($size);
        return $data;
    }
    /**
     * konwertowanie rozmiarów do końcówek
     * pobrane z neta
     * @param type $size
     * @return type
     */
    private function getSize($size) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

}
