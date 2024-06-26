import { Controller } from '@hotwired/stimulus';
import leaflet from "leaflet";
import 'leaflet.bigimage';
import Routing from "../app";
import '../equivalents'

export default class extends Controller {
  map = null;
  mapUrba = null;
  mapRPG = null;
  allMaps = {};
  maps = [];
  latitude = null;
  longitude = null;
  id = document.getElementById('form_recherche').value;
  allParcelles = [];

  codeInsee = null;
  codeParcelle2 = null;
  codeParcelle4 = null;

  initialize() {
    this.maps = document.querySelectorAll('.map')

    for (let i = 0; i < this.maps.length; i++) {
      const map = this.maps[i]
      const mapEl = document.getElementById(map.id)
      let layer;
      switch (map.id) {
        case 'mapRPG':
          layer = "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
          break;
        case 'mapIGN':
          layer = ''
          break;
        default:
          layer = "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"
          break;
      }
      this.allMaps[map.id] = this.mapFactory(mapEl, layer)
    }
    const adresseParcelleEl = document.getElementById('adresse')
    new L.TileLayer("https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=LANDUSE.AGRICULTURE2021&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
      { opacity: 0.8 }).addTo(this.allMaps['mapRPG']);

    new L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", { opacity: 0.85 }).addTo(this.allMaps['mapIGN'])
    new L.tileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=CADASTRALPARCELS.PARCELLAIRE_EXPRESS&style=PCI%20vecteur&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image%2Fpng&TileMatrix={z}&TileCol={x}&TileRow={y}').addTo(this.allMaps['mapIGN'])
    fetch(Routing.generate('app_airtable', { record: this.id })).then((response) => {
      response.json().then((data) => {
        this.latitude = data.fields.Latitude;
        this.longitude = data.fields.Longitude;
        this.findAdresse(this.latitude, this.longitude, adresseParcelleEl)
        //this.codeInsee = data.fields.Code_Insee;
        this.codeParcelle2 = data.fields["TYP: Parcelles"].substring(0, 2);
        this.codeParcelle4 = data.fields["TYP: Parcelles"].substring(2, 6);
        this.allParcelles = data.fields["TYP: Parcelles"].replaceAll(' ', '').split(',');
        this.addMarker(this.allMaps['map'])
        this.fetchZoneUrba(this.allMaps['mapUrba']);
        for (let i = 0; i < this.maps.length; i++) {
          const map = this.maps[i]
          this.centerMap(this.allMaps[map.id])
          this.fetchParcelle(this.allMaps[map.id])
        }
      })
    })

    this.setupFilters(this.allMaps['mapZNIEFF'])

    // this.allMaps.forEach((map) => {
    //   map.on('fullscreenchange', function () {
    //     const mapDiv = document.getElementById(map.id)
    //     mapDiv.requestFullscreen()
    //   })
    // })
    for (let i = 0; i < this.maps.length; i++) {
      const map = this.maps[i]
      const mapEl = document.getElementById(map.id)
      mapEl.addEventListener('fullscreenchange', function () {
        mapEl.requestFullscreen()
      })
    }
  }

  /**
   * Inits a map with a tileLayer and returns it
   * @param map HTML element of the Map
   * @param tileLayer URL of the tileLayer
   * @returns {*} The finished map
   */
  mapFactory(map, tileLayer) {
    const finishedMap = new L.Map(map, {
      center: [46, 3],
      zoom: 12,
      //fullscreenControl: true,
      // OR
      fullscreenControl: {
        pseudoFullscreen: true // if true, fullscreen to page width and height
      }
    });

    new L.TileLayer(tileLayer, { attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(finishedMap);
    L.control.BigImage({ position: 'topright', printControlLabel: '⤵️' }).addTo(finishedMap);
    return finishedMap
  }

  /**
   * Find the address of the given coordinates and put it in the element
   * @param latitude
   * @param longitude
   * @param element
   */
  findAdresse(latitude, longitude, element) {
    fetch('https://api-adresse.data.gouv.fr/reverse/?lon=' + longitude + '&lat=' + latitude).then((response) => {
      response.json().then((data) => {
        element.className = "font-bold"
        element.innerText = data.features[0].properties.label
      })
    })
  }

  centerMap(map) {
    map.panTo(new L.LatLng(this.latitude, this.longitude))
  }

  addMarker(map) {
    if (!map) return
    L.marker([this.latitude, this.longitude]).addTo(map)
  }

  async getCodeInsee() {
    const response = await fetch('https://geo.api.gouv.fr/communes/?lat=' + this.latitude + '&lon=' + this.longitude)
    const data = await response.json()
    this.codeInsee = data[0].code
    return this.codeInsee
  }

  fetchParcelle(map) {
    fetch('https://geo.api.gouv.fr/communes/?lat=' + this.latitude + '&lon=' +
      this.longitude).then((response) => {
        response.json().then((data) => {
          this.codeInsee = data[0].code
          this.allParcelles.forEach((parcelle) => {
            fetch('https://apicarto.ign.fr/api/cadastre/parcelle?code_insee=' + this.codeInsee + '&section=' + parcelle.substring(0, 2) + '&numero=' + parcelle.substring(2, 6), { cache: "force-cache" }).then((response) => {
              response.json().then((data) => {
                L.geoJSON(data, {
                  onEachFeature: function (feature, layer) {
                    // If size too small, do not add label
                    if (layer.getBounds().getNorthEast().lat - layer.getBounds().getSouthWest().lat < 0.001) {
                      return
                    }
                  }
                }).addTo(map)
              })
            })
          })
        }
        )
      })
  }

  async fetchZoneUrba(map) {

    this.getRPG(await this.getCodeInsee()).then((data) => {
      try {
        L.geoJSON(data, {
          onEachFeature: function (feature, layer) {
            //Adding the card for onClick
            //layer.options.fillColor = '#883333'
            //layer.options.color = '#CC3333'
            layer.addEventListener('click', () => {
              const marker = L.marker(layer.getBounds().getCenter(), { opacity: 0 }).addTo(map)
              let content = ''
              content += (feature.properties.code_cultu.toString().replaceAll(',', '<br />'))
              marker.bindPopup('<p>' + content + '</p>').openPopup()
            })

          },
          opacity: 1,
          fillOpacity: 0.3,
          color: "#FF5555"
        }).addTo(map);
      } catch (e) {
        console.error(e, data);
        console.error("Erreur zone Urba")
        return
      }

    });
  }

  alphabetPosition(text) {
    return [...text].map(a => parseInt(a, 36) - 10).filter(a => a >= 0);
  }

  setupFilters(map) {
    const filters = document.querySelectorAll('.filter')
    filters.forEach((filter) => {
      filter.addEventListener('change', (event) => {
        if (event.target.checked) {
          const fond = event.target.id
          switch (fond) {
            case 'oiseaux':
              new L.TileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZPS&style=PROTECTEDAREAS.ZPS&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.8 }).addTo(map)
              break;
            case 'habitats':
              new L.TileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.SIC&style=PROTECTEDAREAS.SIC&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.8 }).addTo(map)
              break;
            case 'pnr':
              new L.TileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PNR&style=PROTECTEDAREAS.PNR&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.8 }).addTo(map)
              break;
            case 'biotope':
              new L.TileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.APB&style=PROTECTEDAREAS.APB&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.8 }).addTo(map)
              break;
            case 'znieff1':
              new L.TileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.8 }).addTo(map)
              new L.TileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1&style=PROTECTEDAREAS.ZNIEFF1&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.8 }).addTo(map)
              break;
            case 'znieff2':
              L.tileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { 'opacity': 1.0 }).addTo(map)
              L.tileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2&style=PROTECTEDAREAS.ZNIEFF2&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { 'opacity': 0.7 }).addTo(map)
              break;
            case 'parcs':
              L.tileLayer('https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PN&style=PROTECTEDAREAS.PN&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}', { opacity: 0.7 }).addTo(map)
              break;
          }
        } else {
          // do a similar switch case to remove the same layer that was added with the id
          // multiple layers can have been added and it is not necessarily the layer 1
          const fond = event.target.id
          switch (fond) {
            case 'oiseaux':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZPS&style=PROTECTEDAREAS.ZPS&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              break;
            case 'habitats':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.SIC&style=PROTECTEDAREAS.SIC&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              break;
            case 'pnr':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PNR&style=PROTECTEDAREAS.PNR&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              break;
            case 'biotope':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.APB&style=PROTECTEDAREAS.APB&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              }
              )
              break;
            case 'znieff1':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1&style=PROTECTEDAREAS.ZNIEFF1&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              break;
            case 'znieff2':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2&style=PROTECTEDAREAS.ZNIEFF2&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              break;
            case 'parcs':
              map.eachLayer((layer) => {
                if (layer._url === 'https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PN&style=PROTECTEDAREAS.PN&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}') {
                  map.removeLayer(layer)
                }
              })
              break;

          }
        }
      })
    })
  }

  getColorCodeCultu(codeCultu) {
    let colour = "#"
    for (let i = 0; i < codeCultu.length; i++) {
      colour = colour.concat((this.alphabetPosition(codeCultu[i]) * 10 + 5).toString(16))
    }
    return colour
  }

  async getRPG(codeInsee) {
    const responseGeom = await fetch('https://apicarto.ign.fr/api/gpu/municipality?insee=' + codeInsee)
    const jsonGeom = await responseGeom.json()
    const dataGeom = await jsonGeom
    const geom = await dataGeom.features[0].geometry
    try {
      const response = await fetch('https://apicarto.ign.fr/api/rpg/v2?annee=2021&geom=' + JSON.stringify(geom))
      const json = await response.json()
      const data = await json
      return await data
    } catch (e) {
      console.error("Impossible de charger les données RPG pour le code INSEE " + codeInsee)
      return e
    }

  }
}
