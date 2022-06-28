import {Controller} from "@hotwired/stimulus";

export default class extends Controller {

    static targets = ['listentries']

    static values = {
        url: String,
        area: Number,
        room: Number,
        day: String,
    }

    async search(date, room) {
        const response = await fetch(`${this.urlValue}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({'date': date, 'area': this.areaValue, 'room': room})
        });
        this.listentriesTarget.innerHTML = await response.text();
    }

    getEntries(event) {
        console.log('iii');
        const date = event.currentTarget.dataset.day;
        const roomId = event.currentTarget.dataset.room;
        console.log(roomId);
        console.log(this.areaValue);

        this.search(date, roomId);
    }

}
