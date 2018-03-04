
#Jak u¿yæ
<p>wymagany csrf-token w head</p>
``
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="css/helpers/pfile.css" rel="stylesheet" type="text/css">

<script src="js/helpers/pfile.js"></script>
``

<p>przyk³¹d u¿ycia</p>
``
<a href="/test" onclick="createBoxPfile('.newfile')">za³aduj plik</a>
<input type="text" value="" class="newfile">
``