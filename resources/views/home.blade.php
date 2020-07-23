<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Laravel</title>
      <!-- Fonts -->
      <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
         integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
         crossorigin=""/>
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
      <style type="text/css">
         #mapid { 
         height: 100vh;
         }
         #form {
         position: absolute;
         top: 73px;
         z-index: 1000;
         left: 10px;
         background: rgb(23 22 22 / 84%);
         color: white;
         padding: 15px;
         border-radius: 10px;
         }
         .text-muted {
         color: #bdbebf!important;
         }
      </style>
      <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
         integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
         crossorigin=""></script>
      <script
         src="https://code.jquery.com/jquery-3.5.1.js"
         integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
         crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
   </head>
   <body>
        <div id="mapid" style="width: '100%';" onmousemove="retornaCoordenadas()"></div>
        <script type="text/javascript">
            var latitude = -3.772269796983491,
                longitude = -38.63239288330079;
            var myIcon = L.icon({
                iconUrl: 'iconMapa.png',
                iconSize: [70, 70],
                iconAnchor: [22, 94],
                popupAnchor: [-3, -76],
            });
            var mymap = L.map('mapid').setView([latitude, longitude], 12);

            (function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        latitude = position.coords.latitude;
                        longitude = position.coords.longitude;
                        mymap.setView([latitude, longitude], 13)
                    });
                }
            })()

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                attribution: 'Map data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'your.mapbox.access.token'
            }).addTo(mymap);

            (function() {
                var proxyUrl = 'https://cors-anywhere.herokuapp.com/',
                    targetUrl = 'https://middleware.vanguardatech.com/api/search/retorno_posicoes/?format=json'
                const fetchPromise = fetch(proxyUrl + targetUrl);
                result = fetchPromise.then(response => {
                    return response.json();
                }).then(data => {
                    marcaMapa(data);
                }).catch(e => {
                    alert(
                        "O mapa não será marcado automaticamente\n" +
                        "Erro: API temporariamente indisponivel!"
                    )
                });
            })();

            function marcaMapa(result) {
                for (let coordenadas in result) {
                    L.marker([result[coordenadas].latitude, result[coordenadas].longitude], {
                        icon: myIcon
                    }).addTo(mymap)
                }
            }

            function pesquisarLocalizacao() {
                let infoLatitude = document.getElementById('latitude').value;
                let infoLongititude = document.getElementById('longitude').value;
                if (infoLatitude && infoLongititude) {
                    mymap.setView([infoLatitude, infoLongititude], 12)
                } else {
                    alert("Favor informar a latitude e longitude");
                }
            }

            function marcarLocalizacao() {
                let infoLatitude = document.getElementById('latitude').value;
                let infoLongititude = document.getElementById('longitude').value;

                if (infoLatitude && infoLongititude) {
                    L.marker([infoLatitude, infoLongititude], {
                        icon: myIcon
                    }).addTo(mymap)
                } else {
                    alert("Favor informar a latitude e longitude");
                }
            }

            function retornaCoordenadas() {
                latitude = (mymap.getBounds()._northEast.lat +
                    mymap.getBounds()._southWest.lat) / 2;
                longitude = (mymap.getBounds()._northEast.lng +
                    mymap.getBounds()._southWest.lng) / 2

                if (latitude && longitude) {
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                }
            }
        </script>
        <div id="form">
            <form onsubmit="return false;">
                <p>Filtro por coordenadas</p>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" class="form-control" id="latitude" aria-describedby="latitudeHelp" placeholder="  -3.785705" required>
                    <small id="latitudeHelp" class="form-text text-muted">Informe a latitude desejada</small>
                </div>
                <div class="form-group">
                    <label for="latitude">Longitude</label>
                    <input type="text" class="form-control" id="longitude" aria-describedby="longitudeHelp" placeholder="  -38.574132" required>
                    <small id="longitudeHelp" class="form-text text-muted">Informe a longitude desejada</small>
                </div>
                <button class="btn btn-primary" onclick="pesquisarLocalizacao()">Pesquisar</button>
                <button class="btn btn-primary" onclick="marcarLocalizacao()">Marcar</button>
            </form>
        </div>
   </body>
</html>