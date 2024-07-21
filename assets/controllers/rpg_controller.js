import { Controller } from '@hotwired/stimulus';
import Routing from "../app";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    initialize() {
        const select = document.getElementById('record_airtable_RPG');
        if (!select) return;
        this.addRPGOptions(select);
    }

    async addRPGOptions(select) {
        const response = await fetch(Routing.generate('app_rpg'));
        const data = await response.json();
        console.log(data)
        data.forEach((rpg) => {
            const option = document.createElement('option');
            option.value = rpg[0];
            option.text = rpg[0] + ':' + rpg[1];
            select.appendChild(option);
        });
    }
}
