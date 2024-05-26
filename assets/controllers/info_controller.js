import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    document.getElementById('info').innerText = 'Infos chargées par le JS ici!';
  }
}
