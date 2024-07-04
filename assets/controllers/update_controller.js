import { Controller } from '@hotwired/stimulus';
import Routing from '../app';

export default class extends Controller {
    initialize() {
        const submit = document.getElementById('updateRecord');
        if (!submit) return;
        this.id = document.getElementById('recordId').innerText;
        this.airtableId = document.getElementById('airtableId').innerText;
        submit.addEventListener('click', () => {
            this.update()
        });
    }

    async update() {
        const responseRecord = await fetch(Routing.generate('internal_record_get', { id: this.id }));
        const record = await responseRecord.json();
        const infoAirtable = await fetch(Routing.generate('app_airtable_infos'));
        const info = await infoAirtable.json();
        console.log(record)
        const response = await fetch(`${info.api_url}${this.airtableId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${info.api_key}`,
            },
            body: JSON.stringify(record),
        });
        // if (response.ok) {
        //     window.location.href = '/';
        // }
    }
}
