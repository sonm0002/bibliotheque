<?php

require_once 'autoload.inc.php';
require_once 'mypdo.inc.php';

$html = <<<HTML
  <h1><i>BIBLIOTHEQUE</i></h1>
    <div class="container">
    <form name='f'>
      <div class="form-group">
        <label class="form-label">
        <h3>Recherche</h3>
          <input class="form-input" name="nom" type="text" placeholder="Artist name" list="artistlist">
          <datalist id="artistlist">

          </datalist>
        </label>
      </div>
    </form>

    <p>
            Résultats : <span id ='nom'></span> =>
        <span id ='resultat'></span><br>        
    </p>
  </div>
HTML;


$html .= <<<HTML
<div class="container">
<h3>Liste déroulante</h3>
  <form class="form-horizontal">
    <div class="form-group">
      <div class="col-4 col-sm-12">
        <select name="genre"  class="f-select" size="8">

HTML;

$stmt = MyPDO::getInstance()->prepare(
  <<<SQL
    SELECT id, name
    FROM genre
    ORDER BY 2
SQL
);

$stmt->execute();

while (($record = $stmt->fetch()) != false) {
  $label = htmlentities($record['name'], ENT_COMPAT | ENT_HTML5);
  $html .= <<<HTML
          <option value="{$record['id']}">{$label}</option>

HTML;
}

$html .= <<<HTML
        </select>
      </div>
     <br> 
      <div class="col-4 col-sm-12">
        <select name="artist" class="f-select" size="8">
          <option>Artistes...</option>
        </select>
      </div>
      <br> 
      <div class="col-4 col-sm-12">
        <select name="album" class="f-select" size="8">
          <option>Albums...</option>
        </select>
      </div>
    </div>
  </form>

  <div id="details">
  </div>
</div>

HTML;


$js = <<<JS
  function emptySelect(sel) {
    while (sel.childElementCount>1) sel.removeChild(sel.lastChild);
  }

  function addOption(sel, txt, val) {
    let option = document.createElement("option");
    option.textContent = txt;
    option.value = val;

    sel.appendChild(option);
  }

  function emptyNode(node) {
    while (node.hasChildNodes()) node.removeChild(node.firstChild);
  }

  function load(url, str, sel) {
    new AjaxRequest({
      url: url,
      method: 'get',
      handleAs: 'json',
      parameters: { q: str },
      onSuccess: function (res) {
        emptySelect(sel);
        for (option of res) addOption(sel, option.txt, option.id); 
      },
      onError: function (status, message) {
        console.log('AjaxRequest error ' + status + ': ' + message);
      }
    });
  }

  window.addEventListener('load', () => {
    let genreList = document.querySelector('select[name=genre]');
    let artistList = document.querySelector('select[name=artist]');
    let albumList = document.querySelector('select[name=album]'); 
    let albumDetails = document.querySelector('div#details')

    genreList.addEventListener('change', () => {
      load('artists.php', genreList.value, artistList);
    });

    artistList.addEventListener('change', () => {
      load('albums.php', artistList.value, albumList);
    });

    albumList.addEventListener('change', () => {
      new AjaxRequest({
        url: 'songs.php',
        method: 'get',
        handleAs: 'json',
        parameters: { q: albumList.value },
        onSuccess: function (res) {
          let artist = artistList.options[artistList.selectedIndex].textContent;
          let album  = albumList.options[albumList.selectedIndex].textContent; 
          let html = '<h2>' + artist + ' - ' + album + '</h2>';

          html += '<table class="table table-striped">';
          
          for (const track of res) {
            html += '  <tr>';
            html += '    <td>' + track.num + '</td>';
            html += '    <td>' + track.name + '</td>';
            html += '    <td>' + track.duration + '</td>';
            html += '  </tr>';
          }

          html += '</table>'

          albumDetails.innerHTML = html;
        },
        onError: function (status, message) {
          console.log('AjaxRequest error ' + status + ': ' + message);
        }
      });
    });
  });

JS;


$js .= <<<JS
    var form = document.querySelector("form");
    form.addEventListener("submit", (event) => {
      event.preventDefault();
    });

    var input = document.querySelector("input");
    input.addEventListener("keyup", (event) => {
      new AjaxRequest({
        url: "liste_artistes.php",
        method: 'get',
        handleAs: 'text',
        parameters: { q: input.value },
        onSuccess: function (res) {
          var artistList = res.split(",");
          console.log(artistList);
          var html = "";
          for (artist of artistList) {
            html += "<option value=\"" + artist + "\">";
          }

          var list = document.querySelector("#artistlist");
          list.innerHTML = html;
        },
        onError: function (status, message) {
          console.log('AjaxRequest error ' + status + ': ' + message);
        }
      });
    });

    window.onload = function () {
    document.forms['f'].onsubmit = function () {return false ; }

    document.forms['f'].elements['nom'].onkeyup = function() {

             console.log(document.forms['f'].elements['nom'].value)

        //si une requête existe, cancel la requête
        if (document.requete) {
            document.requete.cancel();
        }
        //let pour déclaration de var en js
        let lettre = document.forms['f'].elements['nom'].value;

        //on la stocke pr l'utiliser
        document.requete = new AjaxRequest(
            {
                url        : "liste_artistes.php",
                method     : 'get',
                handleAs   : 'text',
                parameters : { q: document.forms['f'].elements['nom'].value, wait: '' },
                onSuccess  : function(res) {
                        let pattern = document.forms['f'].elements['nom'].value;
                        res = res.replace("/"+pattern+"/gi", "<span>"+pattern+"</span>");
                        document.getElementById('resultat').innerHTML = res;
                        document.getElementById('nom').innerHTML = lettre;
                    },
                onError    : function(status, message) {
                        window.alert('Error ' + status + ': ' + message) ;
                    }
            }) ;
    }
}
JS;


$page = new WebPage("Bibliothèque musicale");
$page ->appendCss(<<<CSS
  h3 {
    color             : rgb(47,79,79);
    font-variant      : small-caps ;
    margin-bottom     : 5px ;
    margin-left       : -15px ;
    margin-right      : -15px ;
    padding           : 8px ;
    position          : relative ;
    border-radius     : 5px;
    background-color  : black ;
    border            : solid 3px black ;
    border-style      : 20px;
    background-color  : white;
    background-repeat : repeat-y ;
    font-weight       : bold ;
    font-size         : 1.6em ;
}

body {
  background-color: lightblue;
}

h1 {
    margin-right      : -20%;
    margin-left       : -20%;
    margin-top        : -1%;
    margin-bottom     : -3%;
    padding           : 8px ;
    background-color  : rgb(120, 120, 120);
    background-repeat : repeat-y ;
    font-size         : 1.7em ;
    font-weight       : bold ;
    font-family       : serif;
    text-align        : center;
    color             : black;
    position          : relative ;
    
}

.f-select {
     width                 : 500px; 
    }

CSS
);
$page->appendCss("style.css");
$page->appendJsUrl("ajaxrequest-fetch.js");
$page->appendJs($js);
$page->appendContent($html);

echo $page->toHTML();
