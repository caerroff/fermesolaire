import { Controller } from '@hotwired/stimulus';
import Routing from "../app";
import { area } from '../modules/utils';
import { getCodeInsee } from '../modules/utils';
import $ from 'jquery';

export default class extends Controller {
  id = document.getElementById('form_recherche').value;
  insee = null;

  connect() {
    const submit = document.getElementById('record_airtable_submit')

    // TODO: find why the form is not submitting by itself
    submit.addEventListener('click', (event) => {
      const form = document.querySelector('form[name="record_airtable"]');
      form.submit();
    })

    // Get the info element from DOM
    const info = document.getElementById('info');
    const solargis = document.getElementById('solargis');

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
              <td id='latInfo'>${data.fields.Latitude}</td>
              <td id='lonInfo'>${data.fields.Longitude}</td>
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
          this.insee = codeInsee;
          this.getLoi();
          const codeParcelle2 = parcelle.substring(0, 2);
          const codeParcelle4 = parcelle.substring(2, 6);
          const parcelleData = await this.getPointForParcelle(codeInsee, codeParcelle2, codeParcelle4);
          if(parcelleData.features.length == 0){
            return
          }
          const coords = parcelleData.features[0].geometry.coordinates[0];
          total += area(coords[0]);
          const superficie = document.getElementById('superficie');
          superficie.innerHTML = total.toFixed(2) + ' m²';
        });
        this.getRisques(this.insee);
      })
    })
  }

  async getLoi() {
    const response = await fetch(Routing.generate('loi_api_littoral', { code_insee: this.insee }));
    const json = await response.json();
    const element2 = document.createElement('div');
    console.log(json)
    element2.innerHTML = json.classement ?? json;
    if (json != 'Non présent') {
      element2.setAttribute('class', 'alert alert-danger');
    } else {
      element2.setAttribute('class', 'alert alert-success');
    }
    document.getElementById('loiLittoral').innerHTML = '';
    document.getElementById('loiLittoral').appendChild(element2);

    const response2 = await fetch(Routing.generate('loi_api_montagne', { code_insee: this.insee }));
    const json2 = await response2.json();
    const element = document.createElement('div');
    element.innerHTML = json2.reglementation ?? json2;
    if (json2 != 'Non présent') {
      element.setAttribute('class', 'alert alert-danger');
    } else {
      element.setAttribute('class', 'alert alert-success');
    }
    document.getElementById('loiMontagne').innerHTML = '';
    document.getElementById('loiMontagne').appendChild(element);
  }

  async getPointForParcelle(codeInsee, codeParcelle2, codeParcelle4) {
    const response = await fetch('https://apicarto.ign.fr/api/cadastre/parcelle?code_insee=' + codeInsee + '&section=' + codeParcelle2 + '&numero=' + codeParcelle4)
    const json = await response.json()
    const data = await json
    return data
  }

  async getRisques(codeInsee){
    return
    // const response = await fetch("https://georisques.gouv.fr/api/v1/gaspar/risques?code_insee="+codeInsee)
    // const json = await response.json()
    // const risquesText = document.getElementById("risques_text")
    // if(json.results == 0){
    //   risquesText.innerText = "Pas de risques trouvés"
    // }else{
    //   risquesText.innerText = "Risques trouvés pour le code INSEE"
    //   risquesText.classList.remove("bg-success")
    //   risquesText.classList.add("bg-danger")
    //   for(let i = 0; i < json.data.length; i++){
    //     const el = new HTMLAnchorElement()
    //     el.classList.add(['alert', 'alert-warning'])
    //     el.attributes.append("href", json.data[i])
    //     el.innerText = json.data[i]
    //     risquesText.child
    //   }
    // }
  }
}
