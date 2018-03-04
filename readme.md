
# Patryk Pawlicki 04-03-2018

## Jak użyć
<p>wymagany csrf-token w head</p>

```

<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="css/helpers/pfile.css" rel="stylesheet" type="text/css">

<script src="js/helpers/pfile.js"></script>

```
<p>przykłąd użycia</p>

```

< a href="/test" onclick="createBoxPfile('.newfile')">załaduj plik</a>
< input type="text" value="" class="newfile">

```
