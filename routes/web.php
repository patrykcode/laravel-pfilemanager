<?php

use App\Http\Controllers\Admin\File\Pfile;

Route::get('/pfile', 'Admin\File\Pfile@index');
Route::get('/pfile-newdir', 'Admin\File\Pfile@newDir');
Route::get('/pfile-deletedir', 'Admin\File\Pfile@deleteDir');
Route::post('/pfile-upload', 'Admin\File\Pfile@upload');
Route::get('/pfilemanager', function () {
    //TODO Request $request->has('file'); try{}catch(Exception #e){//error brak pliku lub jakis fuckup}
    // dodaac Pfile do configów globalnych
    echo (Pfile::getInstance()->files($_GET['file']));
});