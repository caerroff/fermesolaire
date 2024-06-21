import { Controller } from '@hotwired/stimulus';
import Routing from "../app";
import { area } from '../modules/utils';
import { getCodeInsee } from '../modules/utils';

export default class extends Controller {
  id = document.getElementById('recherche').value;


  connect() {
    // Get the info element from DOM
    const info = document.getElementById('info');

    if (!info) return;
    fetch(Routing.generate('app_airtable', { record: this.id })).then((response) => {
      response.json().then((data) => {
        info.innerHTML = `
          <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Parcelles</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>${data.fields.Latitude}</td>
              <td>${data.fields.Longitude}</td>
              <td>${data.fields["TYP: Parcelles"]}</td>
            </tr>
          </table>
          
          <h4 class="pt-1">Superficie Totale: <span id="superficie" class="mb-0">chargement...</span></h4>
        `;
      })
    })

    fetch(Routing.generate('app_airtable', { record: this.id })).then((response) => {
      response.json().then((data) => {
        const parcelles = data.fields["TYP: Parcelles"].replaceAll(' ', '').split(',');
        let total = 0;
        parcelles.forEach(async parcelle => {
          const codeInsee = await getCodeInsee(data.fields.Latitude, data.fields.Longitude);
          const codeParcelle2 = parcelle.substring(0, 2);
          const codeParcelle4 = parcelle.substring(2, 6);
          const parcelleData = await this.getPointForParcelle(codeInsee, codeParcelle2, codeParcelle4);
          const coords = parcelleData.features[0].geometry.coordinates[0];
          total += area(coords[0]);
          const superficie = document.getElementById('superficie');
          superficie.innerHTML = total.toFixed(2) + ' m²';
        });
      })
    })
  }

  async getPointForParcelle(codeInsee, codeParcelle2, codeParcelle4) {
    const response = await fetch('https://apicarto.ign.fr/api/cadastre/parcelle?code_insee=' + codeInsee + '&section=' + codeParcelle2 + '&numero=' + codeParcelle4)
    const json = await response.json()
    const data = await json
    return data
  }
}
