<div class="file-box-background">

    <div class="file-box">
        <div class="file-box-close" onclick="closeBox();">&times;</div>
        <div class="loading"></div>
        <div class="file-box-head">
            <h2>Filemanager - wybierz plik</h2>
            <hr>
            <div class="file-info">
                <input type="text" value="{{ $getFile[0] ? $getFile[1] : ''}}" class="file-name" readonly>
                <button class="p-upload">dodaj</button><br/>
                <small class="current-path">{{$current}}</small>
            </div>
        </div>
        <hr>
        <div class="file-action">
            <a onclick="$('.file-create').slideToggle();" class="btn-file btn-newdir"><i class="fa fa-plus"></i>&nbsp;nowy katalog</a>
            <div class="file-create">
                <small>Podaj nazwe</small><br/>
                <input type="text" id="dirname" class="file-input">
                <a class="btn-file btn-newdir btn-create" onclick="createDir('{{$current}}')"><i class="fa fa-plus"></i></a>
            </div>

            <label for="upload">
                <a class="btn-file btn-upload">
                    <i class="fa fa-upload"></i>&nbsp;dodaj plik
                </a>
                <!---->
                <input type="file" id="upload" name="upload" onchange="uploadFile();" style="display:none;">
            </label>
        </div>
        <div class="file-box-body">
            <ul id="file-select">  
                <li><i class="fa fa-folder-open-o"></i> <a onclick="reloadFile('{{$prev}}')">..</a></li>
                @foreach($files as $file)
                <li class="file-child-node" onclick="reloadFile('{{$current}}{{$file[0]}}')">
                    <i class="fa fa-{{$file[1]['icon']}}"></i>
                    <a>{{$file[0]}} 
                        <small>{{$file[1]['size']}}</small>
                    </a>
                    <a class="file-del" onclick="deleteFile('{{$current}}{{$file[0]}}')">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </li>
                @endforeach
            </ul> 
        </div>
        <hr>
        <div class="file-box-fotter">
            <small>Wskazówka: Wybierz dowolny plik i zaakceptuj</small>
        </div>
    </div>
</div>
