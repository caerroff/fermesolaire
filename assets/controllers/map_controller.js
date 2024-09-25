import { Controller } from "@hotwired/stimulus";
import leaflet from "leaflet";
import "leaflet.bigimage";
import Routing from "../app";
import "../equivalents";
import { foldersToKML, toKML } from "@placemarkio/tokml";
import measure from "../measure";
import { Client } from "@googlemaps/google-maps-services-js";
import { directions } from "@googlemaps/google-maps-services-js/dist/directions";
import axios from "axios";

const MAPBOX_TOKEN = "pk.eyJ1IjoiY2FlcnJvZmYiLCJhIjoiY20xZjRncHAyMTV3aTJqc2FzOHl1bTJsbyJ9.Fsh9vlPIq0LA4K4NQSMwjQ";

export default class extends Controller {
  map = null;
  mapUrba = null;
  mapRPG = null;
  allMaps = {};
  maps = [];
  latitude = null;
  longitude = null;
  id = document.getElementById("form_recherche").value;
  allParcelles = [];

  codeInsee = null;
  codeParcelle2 = null;
  codeParcelle4 = null;

  initialize() {
    this.maps = document.querySelectorAll(".map");
    for (let i = 0; i < this.maps.length; i++) {
      const map = this.maps[i];
      const mapEl = document.getElementById(map.id);
      let layer;
      let opacity = 1;
      let maxZoom = 18;
      switch (map.id) {
        case "mapRPG":
          layer =
            "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png";
          maxZoom = 16;
          break;
        case "mapIGN":
          layer = "";
          break;
        default:
          layer =
            "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}";
          break;
      }
      this.allMaps[map.id] = this.mapFactory(mapEl, layer, opacity, maxZoom);
    }
    const adresseParcelleEl = document.getElementById("adresse");
    new L.TileLayer(
      "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=LANDUSE.AGRICULTURE2021&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
      { opacity: 0.8 }
    ).addTo(this.allMaps["mapRPG"]);

    new L.tileLayer(
      "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
      { opacity: 0.85 }
    ).addTo(this.allMaps["mapIGN"]);
    new L.tileLayer(
      "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=CADASTRALPARCELS.PARCELLAIRE_EXPRESS&style=PCI%20vecteur&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image%2Fpng&TileMatrix={z}&TileCol={x}&TileRow={y}"
    ).addTo(this.allMaps["mapIGN"]);
    new L.tileLayer(
      "https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png",
      {
        opacity: 0.8,
      }
    ).addTo(this.allMaps["mapIGN"]);

    new L.tileLayer.wms(
      "	https://data.geopf.fr/wms-v/ows?SERVICE=WMS&VERSION=1.3.0&REQUEST=GetMap&TRANSPARENT=true&transparent=true&version=1.1.1&format=image/png&LAYERS=du,psmv&WIDTH=256&HEIGHT=256&CRS=EPSG:3857&STYLES=",
      {
        transparent: true,
        format: "image/png",
        layers: ['du', 'psmv'],
        bounds: L.latLngBounds([[-54.5247541978, 2.05338918702],[9.56001631027, 51.1485061713]])
      }
    ).addTo(this.allMaps["mapUrba"])
    fetch(Routing.generate("app_airtable", { record: this.id })).then(
      (response) => {
        response.json().then((data) => {
          this.latitude = data.fields.Latitude;
          this.longitude = data.fields.Longitude;
          document.getElementById("latlon").innerText =
            this.latitude + "," + this.longitude;

          document
            .getElementById("latlon")
            .addEventListener("click", function (event) {
              event.preventDefault();
              navigator.clipboard.writeText(
                document.getElementById("latlon").innerText
              );
            });

          this.findAdresse(this.latitude, this.longitude, adresseParcelleEl);
          this.codeParcelle2 = data.fields["TYP: Parcelles"].substring(0, 2);
          this.codeParcelle4 = data.fields["TYP: Parcelles"].substring(2, 6);
          this.allParcelles = data.fields["TYP: Parcelles"]
            .replaceAll(" ", "")
            .split(",");
          this.addMarker(this.allMaps["mapReseau"]);
          this.fetchZoneUrba(this.allMaps["mapUrba"]);
          for (let i = 0; i < this.maps.length; i++) {
            const map = this.maps[i];
            if (map.id == "map") {
              this.fetchParcelle(this.allMaps[map.id], true);
            }
            this.centerMap(this.allMaps[map.id]);
            this.fetchParcelle(this.allMaps[map.id]);
          }
          this.loadKmz(this.allMaps["mapReseau"]);
          this.addPopupRpg(this.allMaps["mapRPG"]);
        });
      }
    );
    this.setupFilters(this.allMaps["mapZNIEFF"]);
    document.getElementById("kml").addEventListener("click", () => {
      this.fetchKmlParcelle(this.allMaps["map"]);
    });

    L.Measure = {
      linearMeasurement: "Mesure de Distance",
      areaMeasurement: "Mesure de Surface",
      start: "Premier Point",
      meter: "m",
      kilometer: "km",
      squareMeter: "m²",
      squareKilometers: "km²",
    };

    L.control.measure({}).addTo(this.allMaps["mapZNIEFF"]);

    // this.allMaps.forEach((map) => {
    //   map.on('fullscreenchange', function () {
    //     const mapDiv = document.getElementById(map.id)
    //     mapDiv.requestFullscreen()
    //   })
    // })
    for (let i = 0; i < this.maps.length; i++) {
      const map = this.maps[i];
      const mapEl = document.getElementById(map.id);
      mapEl.addEventListener("fullscreenchange", function () {
        mapEl.requestFullscreen();
      });
    }

    document
      .getElementById("directionBtn")
      .addEventListener("click", (e) => {
        e.preventDefault();
        const arrival = document.getElementById("relaisNom").innerText;
        this.requestDirection(arrival);
      });
  }

  /**
   * Inits a map with a tileLayer and returns it
   * @param map HTML element of the Map
   * @param tileLayer URL of the tileLayer
   * @param opacity The opacity that will be applied to the tileLayer
   * @returns {*} The finished map
   */
  mapFactory(map, tileLayer, opacity = 1, maxZoom = 19) {
    const finishedMap = new L.Map(map, {
      center: [46, 3],
      zoom: 12,
      maxZoom: maxZoom,
      //fullscreenControl: true,
      // OR
      fullscreenControl: {
        pseudoFullscreen: true, // if true, fullscreen to page width and height
      },
    });
    new L.TileLayer(tileLayer, {
      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      opacity: opacity,
    }).addTo(finishedMap);
    L.control
      .BigImage({ position: "topright", printControlLabel: "⤵️" })
      .addTo(finishedMap);
    return finishedMap;
  }

  /**
   * Find the address of the given coordinates and put it in the element
   * @param latitude
   * @param longitude
   * @param element
   */
  findAdresse(latitude, longitude, element) {
    fetch(
      "https://api-adresse.data.gouv.fr/reverse/?lon=" +
        longitude +
        "&lat=" +
        latitude
    ).then((response) => {
      response.json().then((data) => {
        element.className = "font-bold";
        element.innerText = data.features[0].properties.label;
      });
    });
  }

  async loadKmz(map) {
    // open file located in ../../public/assets/kmz.json
    const response = await fetch("/assets/kmz.json");
    const data = await response.json();

    // create a new layer with the data
    for (let i = 0; i < data.features.length; i++) {
      if (
        Math.abs(
          parseFloat(this.latitude) - data.features[i].geometry.coordinates[1]
        ) > 0.3 ||
        Math.abs(
          parseFloat(this.longitude) - data.features[i].geometry.coordinates[0]
        ) > 0.3
      ) {
        continue;
      }
      const selectRelais = document.getElementById("record_airtable_Relais");
      if (selectRelais) {
        selectRelais.innerHTML +=
          '<option value="' +
          data.features[i].properties.name +
          '">' +
          `[${data.features[i].id}] ` +
          data.features[i].properties.name +
          "</option>";
      }
      const icon = L.icon({
        iconUrl: 'assets/marker.png',
        iconSize: [28,40],
        iconAnchor: [14, 32]
      })
      const marker = new L.Marker([
        data.features[i].geometry.coordinates[1],
        data.features[i].geometry.coordinates[0],
      ], {
        icon: icon
      })
        .addTo(map)
        // Add a popup to the marker
        .bindPopup(data.features[i].properties.description);
      marker.on("click", function (event) {
        const relaisNom = document.getElementById("relaisNom");
        if (!relaisNom) {
          return;
        }
        relaisNom.innerText = event.latlng.lat + "," + event.latlng.lng;
      });
    }
  }

  centerMap(map) {
    map.panTo(new L.LatLng(this.latitude, this.longitude));
  }

  addMarker(map) {
    if (!map) return;
    L.marker([this.latitude, this.longitude]).addTo(map);
  }

  getColorCodeCultu(codeCultu) {
    let colour = "#";
    for (let i = 0; i < codeCultu.length; i++) {
      colour = colour.concat(
        (alphabetPosition(codeCultu[i]) * 10 + 5).toString(16)
      );
    }
    return colour;
  }

  async getRPG() {
    try{
      const responseGeom = await fetch(
        "https://apicarto.ign.fr/api/gpu/municipality?insee=" +
          (await this.getCodeInsee())
      );
      const jsonGeom = await responseGeom.json();
      const dataGeom = await jsonGeom;
      const geom = await dataGeom.features[0].geometry;
      const response = await fetch(
        "https://apicarto.ign.fr/api/rpg/v2?annee=2021&geom=" +
          (await JSON.stringify(geom))
      );
      const json = await response.json();
      const data = await json;
      return await data;
    }catch(e){
      console.error(e)
    }
    
  }

  async addPopupRpg(map) {
    const data = await this.getRPG();
    L.geoJSON(data, {
      onEachFeature: function (feature, layer) {
        //Adding the card for onClick
        const color = (feature) => {
          let colour = "#";
          for (let i = 0; i < codeCultu.length; i++) {
            colour = colour.concat(
              (alphabetPosition(codeCultu[i]) * 10 + 5).toString(16)
            );
          }
          return colour;
        };
        layer.options.color = color;
        layer.options.opacity = 0;
        layer.options.fillOpacity = 0.0;
        layer.addEventListener("click", () => {
          const marker = L.marker(layer.getBounds().getCenter(), {
            opacity: 0,
          }).addTo(map);
          let content =
            "<strong>" + feature.properties.code_cultu + "<br /></strong>";
          content += JSON.stringify(feature.properties).replaceAll(
            ",",
            "<br />"
          );
          marker.bindPopup("<p>" + content + "</p>").openPopup();
          marker.addEventListener("click", () => {
            marker.openPopup();
          });
        });
      },
      opacity: 0.2,
    }).addTo(map);
  }

  async getCodeInsee() {
    const response = await fetch(
      "https://geo.api.gouv.fr/communes/?lat=" +
        this.latitude +
        "&lon=" +
        this.longitude
    );
    const data = await response.json();
    this.codeInsee = data[0].code;
    return this.codeInsee;
  }

  fetchParcelle(map, pin = false) {
    fetch(
      "https://geo.api.gouv.fr/communes/?lat=" +
        this.latitude +
        "&lon=" +
        this.longitude
    ).then((response) => {
      response.json().then((data) => {
        this.codeInsee = data[0].code;
        this.allParcelles.forEach((parcelle) => {
          fetch(
            "https://apicarto.ign.fr/api/cadastre/parcelle?code_insee=" +
              this.codeInsee +
              "&section=" +
              parcelle.substring(0, 2) +
              "&numero=" +
              parcelle.substring(2, 6),
            { cache: "force-cache" }
          ).then((response) => {
            response.json().then((data) => {
              if (pin == false) {
                L.geoJSON(data).addTo(map);
              } else {
                let polygonsWithCenters = L.layerGroup();
                let geoJsonLayer = L.geoJSON(data, {
                  onEachFeature: function (feature, layer) {
                    let marker = L.marker(layer.getBounds().getCenter());
                    let polygonAndItsCenter = L.layerGroup([layer, marker]);
                    polygonAndItsCenter.addTo(polygonsWithCenters);
                  },
                });
                polygonsWithCenters.addTo(map);
              }
            });
          });
        });
      });
    });
  }

  async fetchKmlParcelle(map) {
    const response1 = await fetch(
      "https://geo.api.gouv.fr/communes/?lat=" +
        this.latitude +
        "&lon=" +
        this.longitude
    );
    const data1 = await response1.json();
    this.codeInsee = data1[0].code;
    let geoJsons = [];
    for (let i = 0; i < this.allParcelles.length; i++) {
      const response2 = await fetch(
        "https://apicarto.ign.fr/api/cadastre/parcelle?code_insee=" +
          (await this.codeInsee) +
          "&section=" +
          (await this.allParcelles[i].substring(0, 2)) +
          "&numero=" +
          (await this.allParcelles[i].substring(2, 6)),
        { cache: "force-cache" }
      );
      const data2 = await response2.json();
      if(data2.features.totalFeatures == 0){
        continue;
      }
      geoJsons.push(data2.features);
    }
    let count = 0;
    while (geoJsons.length < this.allParcelles.length && count < 100000) {
      count += 1;
    }
    const featuresArray = [];
    for (let i = 0; i < await geoJsons.length; i++) {
      if(!geoJsons[i][0]){
        continue
      }
      featuresArray.push(geoJsons[i][0]);
    }
    const kml = toKML({ type: "FeatureCollection", features: featuresArray });
    let hiddenElement = document.createElement("a");
    hiddenElement.href =
      "data:text/plain;charset=utf-8," + encodeURIComponent(kml);
    hiddenElement.target = "_blank";
    hiddenElement.download = "parcelles.kml";
    hiddenElement.click();
  }

  async getPointForZoneUrba(codeInsee) {
    const response = await fetch(
      "https://apicarto.ign.fr/api/gpu/zone-urba?partition=DU_" + codeInsee
    );
    const json = await response.json();
    const data = await json;
    return data;
  }

  async fetchZoneUrba(map) {
    this.getPointForZoneUrba(await this.getCodeInsee()).then((data) => {
      try {
        const errorEl = document.getElementById("error-zone-urba");
        if (data.features.length == 0) {
          errorEl.classList.remove("d-none");
        } else {
          if (!errorEl.classList.contains("d-none")) {
            errorEl.classList.add("d-none");
          }
        }
        L.geoJSON(data, {
          onEachFeature: function (feature, layer) {
            //Adding the card for onClick
            //layer.options.fillColor = '#883333'
            //layer.options.color = '#CC3333'
            layer.addEventListener("click", () => {
              const marker = L.marker(layer.getBounds().getCenter(), {
                opacity: 0,
              }).addTo(map);
              marker
                .bindPopup(
                  "<p>" +
                    feature.properties.libelle +
                    " - " +
                    feature.properties.libelong +
                    "</p>"
                )
                .openPopup();
            });
            new L.Marker(layer.getBounds().getCenter(), {
              icon: new L.DivIcon({
                className: "label",
                html:
                  '<span class="text-nowrap rounded-xl font-weight-light bg-white" style="--bs-bg-opacity: .5;">' +
                  feature.properties.libelle +
                  "</span>",
              }),
            }).addTo(map);
          },
          opacity: 1,
          fillOpacity: 0.3,
          color: "#FF5555",
        }).addTo(map);
      } catch (e) {
        console.error(e, data);
        console.error("Erreur zone Urba");
        return;
      }
    });
  }

  alphabetPosition(text) {
    return [...text].map((a) => parseInt(a, 36) - 10).filter((a) => a >= 0);
  }

  /**
   * Make a request to Google's Direction API to know shortest direction (walking) to point
   * @param {string} arrival
   */
  async requestDirection(
    arrival,
    departureLatitude = this.latitude,
    departureLongitude = this.longitude
  ) {
    if (!arrival) {
      return;
    }
    
    const arrivalLat = arrival.split(",")[0];
    const arrivalLon = arrival.split(",")[1];
    const response = await fetch(
      `https://api.mapbox.com/directions/v5/mapbox/walking/${departureLongitude},${departureLatitude};${arrivalLon},${arrivalLat}?access_token=${MAPBOX_TOKEN}`
      // `https://api.mapbox.com/directions/v5/mapbox/walking/${departureLongitude},${departureLatitude};${arrivalLon},${arrivalLat}?access_token=pk.eyJ1IjoiY2FlcnJvZmYiLCJhIjoiY20xZjRncHAyMTV3aTJqc2FzOHl1bTJsbyJ9.Fsh9vlPIq0LA4K4NQSMwjQ`
      // "https://api.mapbox.com/directions/v5/mapbox/cycling/-122.42,37.78;-77.03,38.91?access_token=pk.eyJ1IjoiY2FlcnJvZmYiLCJhIjoiY20xZjRncHAyMTV3aTJqc2FzOHl1bTJsbyJ9.Fsh9vlPIq0LA4K4NQSMwjQ"
    );
    const json = await response.json()
    const paragraph = document.getElementById('responseDirection')
    paragraph.innerText = `La distance entre les deux points (en passant par les routes/chemins) est de ${json.routes[0].distance / 1000}km et le temps de trajet est de ${this.convertSeconds(json.routes[0].duration)}`    
  }

  convertSeconds (seconds) {
    console.log(seconds)
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
  
    return `${hours} heure(s) : ${minutes} minute(s)`
  }

  setupFilters(map) {
    const filters = document.querySelectorAll(".filter");
    filters.forEach((filter) => {
      filter.addEventListener("change", (event) => {
        if (event.target.checked) {
          const fond = event.target.id;
          switch (fond) {
            case "oiseaux":
              new L.TileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZPS&style=PROTECTEDAREAS.ZPS&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.8 }
              ).addTo(map);
              break;
            case "habitats":
              new L.TileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.SIC&style=PROTECTEDAREAS.SIC&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.8 }
              ).addTo(map);
              break;
            case "pnr":
              new L.TileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PNR&style=PROTECTEDAREAS.PNR&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.8 }
              ).addTo(map);
              break;
            case "biotope":
              new L.TileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.APB&style=PROTECTEDAREAS.APB&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.8 }
              ).addTo(map);
              break;
            case "znieff1":
              new L.TileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.8 }
              ).addTo(map);
              new L.TileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1&style=PROTECTEDAREAS.ZNIEFF1&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.8 }
              ).addTo(map);
              break;
            case "znieff2":
              L.tileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 1.0 }
              ).addTo(map);
              L.tileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2&style=PROTECTEDAREAS.ZNIEFF2&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.7 }
              ).addTo(map);
              break;
            case "parcs":
              L.tileLayer(
                "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PN&style=PROTECTEDAREAS.PN&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}",
                { opacity: 0.7 }
              ).addTo(map);
              break;
          }
        } else {
          // do a similar switch case to remove the same layer that was added with the id
          // multiple layers can have been added and it is not necessarily the layer 1
          const fond = event.target.id;
          switch (fond) {
            case "oiseaux":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZPS&style=PROTECTEDAREAS.ZPS&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
            case "habitats":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.SIC&style=PROTECTEDAREAS.SIC&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
            case "pnr":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PNR&style=PROTECTEDAREAS.PNR&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
            case "biotope":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.APB&style=PROTECTEDAREAS.APB&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
            case "znieff1":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF1&style=PROTECTEDAREAS.ZNIEFF1&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
            case "znieff2":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2.SEA&style=normal&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.ZNIEFF2&style=PROTECTEDAREAS.ZNIEFF2&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
            case "parcs":
              map.eachLayer((layer) => {
                if (
                  layer._url ===
                  "https://wxs.ign.fr/an7nvfzojv5wa96dsga5nk8w/geoportail/wmts?layer=PROTECTEDAREAS.PN&style=PROTECTEDAREAS.PN&tilematrixset=PM&Service=WMTS&Request=GetTile&Version=1.0.0&Format=image/png&TileMatrix={z}&TileCol={x}&TileRow={y}"
                ) {
                  map.removeLayer(layer);
                }
              });
              break;
          }
        }
      });
    });
  }
}
