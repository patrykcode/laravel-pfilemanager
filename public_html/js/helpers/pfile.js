var targetNameInput = '';
var currentLocation = '';
function createBoxPfile(inputName) {
    event.preventDefault();
    targetNameInput = inputName;
    $.ajax({
        method: "GET",
        url: '/pfile',
        data: {file: ''},
        beforeSend: function () {
            $(".loading").html('<div class="lds-css ng-scope"><div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>');
        },
        success: function (data) {
            document.body.innerHTML += data;
        }
    }).done(function () {
        setTimeout(function () {
            $(".loading").html('');
        }, 350);
    });
}

/**
 * Przeładowanie zawartości plików i ustawienie scieżek
 * @param {type} path
 * @returns {undefined}
 */
function reloadFile(path) {
    $.ajax({
        method: "GET",
        url: '/pfilemanager',
        data: {file: path},
        beforeSend: function () {
            $(".loading").html('<div class="lds-css ng-scope"><div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>');
        },
        success: function (data) {

            var json = JSON.parse(data);
            if (json) {
                if (json.getFile[0] == false) {
                    refreshDirAdd();
                    //czyscimy aktualne pliki przed dodaniem nowych
                    var ul = document.getElementById("file-select");
                    ul.innerHTML = "";
                    //powtór do poprzedniej lokalizacji
                    var prev = json.prev == '' ? '/' : json.prev;
                    ul.innerHTML += '<li onclick="reloadFile(\'/' + prev + '\')"><i class="fa fa-folder-open-o"></i> <a> .. </a></li>';
                    //aktualny katalog
                    document.querySelector('.current-path').innerHTML = json.current;
                    currentLocation = json.current;
                    document.querySelector('.btn-create').setAttribute('onclick', "createDir(\'" + json.current.toString() + "\');")
                    //dodawanie nowej zawartości ściezki
                    for (var i = 0; i < json.files.length; i++) {
                        var li = '<li class="file-child-node" onclick="reloadFile(\'' + json.current.toString() + '/' + json.files[i][0].toString() + '\')"><i class="fa fa-' + json.files[i][1]['icon'] + '"></i>  <a>' + json.files[i][0] + '</a> <small>' + json.files[i][1]['size'] + '</small><a class="file-del" onclick="deleteFile(\'' + json.current.toString() + '/' + json.files[i][0].toString() + '\')"><i class="fa fa-trash-o"></i></a></li>';
                        ul.innerHTML += li;
                    }
                } else {
                    // wybór pliki jeśli nie  jest katalogiem
                    document.querySelector('.file-name').value = json.getFile[1];
                    document.querySelector(targetNameInput).value = json.getFile[1];
                }
            }
        },
    }).done(function () {
        setTimeout(function () {
            $(".loading").html('');
        }, 350);
    });
}
function createDir(path) {
    name = document.querySelector('#dirname').value;
    if (name.length > 1) {
        $.ajax({
            method: "GET",
            url: '/pfile-newdir',
            data: {dirname: name, currentLocation: document.querySelector('.current-path').innerHTML},
            beforeSend: function () {
                $(".loading").html('<div class="lds-css ng-scope"><div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>');
            },
            success: function (data) {
                $json = JSON.parse(data);
                if ($json.success)
                    reloadFile(path);
                else {
                    alert($json.error);
                    reloadFile(path);
                }

            }
        }).done(function () {
            setTimeout(function () {
                $(".loading").html('');
            }, 350);
        });
    } else {
        alert('Nazwa katalogu jest za krótka');
    }
}
function uploadFile() {
    var formData = new FormData();
    formData.append('file', document.querySelector('#upload').files[0]);
    formData.append('path', currentLocation);
    document.querySelector('#upload').value = '';
    $.ajax({
        url: 'pfile-upload',
        type: 'POST',
        data: formData,
        async: true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        processData: false, // tell jQuery not to process the data
        contentType: false, // tell jQuery not to set contentType
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function (e) {
                    try {
                        var position = e.loaded || e.position;
                        var total = e.total;
                        var percent = position / total * 100;
                        document.querySelector('.lds-eclipse small').innerHTML = parseInt(percent) + ' %';
                    } catch (err) {
                        alert(err);
                    }
                }, false);

            }
            return myXhr;
        },
        beforeSend: function () {
            $(".loading").html('<div class="lds-css ng-scope"><div style="width:100%;height:100%" class="lds-eclipse"><small></small><div></div></div>');
        },
        success: function (data) {
            reloadFile(currentLocation);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR, textStatus, errorThrown);
        },
        timeout: 6000000
    }).done(function () {
        setTimeout(function () {
            $(".loading").html('');
        }, 350);
    });
}
/**
 * odswiezanie widoku "nowy katalog"
 * @returns {undefined}
 */
function refreshDirAdd() {
    $('.file-create').hide();
    $('.file-create input[type="text"]').val('');
}

/**
 * kasowanie plików
 * @param {type} path
 * @returns {undefined}
 */
function deleteFile(path) {
    event.preventDefault();
    event.stopPropagation();
    if (confirm('Czy napewno usunąć plik:' + path)) {
        $.ajax({
            method: "GET",
            url: '/pfile-deletedir',
            data: {dirname: path},
            beforeSend: function () {
                $(".loading").html('<div class="lds-css ng-scope"><div style="width:100%;height:100%" class="lds-eclipse"><div></div></div>');
            },
            success: function (data) {
                $json = JSON.parse(data);
                if ($json.success)
                    reloadFile(currentLocation);
                else {
                    alert($json.error);
                    reloadFile(currentLocation);
                }
            }
        }).done(function () {
            setTimeout(function () {
                $(".loading").html('');
            }, 350);
        });
    }
}

/**
 * zamykanie okna managera
 * @returns {undefined}
 */
function closeBox() {
    document.querySelector('.file-box-background').outerHTML = '';
}

window.addEventListener('click', function (e) {
    if (e.target.className == 'file-box-background') {
        closeBox();
    }
})