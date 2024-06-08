import { Controller } from '@hotwired/stimulus';
import leaflet from "leaflet";
import 'leaflet.bigimage';
import Routing from "../app";
import '../equivalents'

export default class extends Controller {
  map = null;
  mapUrba = null;
  mapRPG = null;
  allMaps = [];
  latitude = null;
  longitude = null;
  id = document.getElementById('recherche').value;
  allParcelles = [];

  codeInsee = null;
  codeParcelle2 = null;
  codeParcelle4 = null;

  initialize() {

    const mapEl = document.getElementById('map')
    this.map = this.mapFactory(mapEl, "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}")
    const mapUrbaEl = document.getElementById('mapUrba')
    this.mapUrba = this.mapFactory(mapUrbaEl, "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}")
    const mapRPG = document.getElementById('mapRPG')
    this.mapRPG = this.mapFactory(mapRPG, "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png")
    const adresseParcelleEl = document.getElementById('adresse')
    new L.TileLayer("https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=LANDUSE.AGRICULTURE2021&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
      { opacity: 0.8 }).addTo(this.mapRPG);


    fetch(Routing.generate('app_airtable', { record: this.id })).then((response) => {
      response.json().then((data) => {
        this.latitude = data.fields.Latitude;
        this.longitude = data.fields.Longitude;
        this.findAdresse(this.latitude, this.longitude, adresseParcelleEl)
        //this.codeInsee = data.fields.Code_Insee;
        this.codeParcelle2 = data.fields["TYP: Parcelles"].substring(0, 2);
        this.codeParcelle4 = data.fields["TYP: Parcelles"].substring(2, 6);
        this.allParcelles = data.fields["TYP: Parcelles"].replaceAll(' ', '').split(',');
        this.centerMap(this.map)
        this.addMaker(this.map)

        this.centerMap(this.mapUrba)
        this.fetchParcelle(this.map);
        this.fetchParcelle(this.mapUrba);
        this.fetchZoneUrba(this.mapUrba);
        this.centerMap(this.mapRPG);
        this.fetchParcelle(this.mapRPG);

      })
    })



    this.map.on('fullscreenchange', function () {
      const mapDiv = document.getElementById('map')
      mapDiv.requestFullscreen()
    })

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
    this.allMaps.push(finishedMap)
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

  addMaker(map) {
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
        console.error(data);
        console.error("Erreur zone Urba")
        return
      }

    });
  }

  alphabetPosition(text) {
    return [...text].map(a => parseInt(a, 36) - 10).filter(a => a >= 0);
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
