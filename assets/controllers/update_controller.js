import { Controller } from '@hotwired/stimulus';
import Routing from '../app';

export default class extends Controller {
    initialize() {
        const submit = document.getElementById('updateRecord');
        const submitNoRpg = document.getElementById('updateRecordNoRPG');
        if (!submit) return;
        if (!submitNoRpg) return;
        this.id = document.getElementById('recordId').innerText;
        this.airtableId = document.getElementById('airtableId').innerText;
        submit.addEventListener('click', () => {
            this.update()
        });
        submitNoRpg.addEventListener('click', () => {
            this.update(true)
        });
    }

    async update(noRPG=false) {
        const responseRecord = await fetch(Routing.generate('internal_record_get', { id: this.id }));
        const record = await responseRecord.json();
        console.log(record)
        if(noRPG){
            record["fields"]["RPG 2023 intranet"] = ["Autre"]
        }
        const infoAirtable = await fetch(Routing.generate('app_airtable_infos'));
        const info = await infoAirtable.json();
        console.log(record)
        const response = await fetch(`${info.api_url}${this.airtableId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${info.api_key}`,
            },
            body: JSON.stringify(delete record['id'] && record),
        });
        if (response.ok) {
            alert('Record mis à jour avec succès');
            window.location.href = '/';
        } else {
            alert('Une erreur est survenue lors de la mise à jour du record !' + response.statusText);
        }
    }
}
