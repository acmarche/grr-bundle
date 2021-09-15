import {Controller} from "stimulus";

export default class extends Controller {

    static targets = ['selectedArea', 'selectedRoom', 'rooms']

    static values = {
        url: String,
        urlCurrent: String,
        isRestricted: Boolean
    }

    async search(query, isRequired) {
        const params = new URLSearchParams({
            id: query,
            isRequired: isRequired,
            isRestricted: this.isRestrictedValue
        });
        console.log('query: ' + query);
        const response = await fetch(`${this.urlValue}?${params.toString()}`, {
            method: 'POST'
        });
        this.roomsTarget.innerHTML = await response.text();
    }

    selectArea(event) {
        const areaIdSelected = event.currentTarget.value;
     //   console.log('id area select: ' + areaIdSelected);
        this.changerUrlArea(areaIdSelected);
    }

    updateRooms(event) {
        const areaIdSelected = event.currentTarget.value;
        const isRequired = event.currentTarget.getAttribute('required');
        console.log('id area select: ' + areaIdSelected);
        this.search(areaIdSelected, isRequired);
    }

    selectRoom(event) {
        const roomIdSelected = event.currentTarget.value;
   //     console.log('id room select: ' + roomIdSelected);
        this.changerUrlRoom(roomIdSelected);
    }

    changerUrlArea(areaId) {
        let url = this.urlCurrentValue;
        const regex = /\/area\/\d+/;
        url = url.replace(regex, '/area/' + areaId);
        if (url) {
            window.location = url;
        }
    }

    changerUrlRoom(roomId) {
        let url = this.urlCurrentValue;
        const regex = /\/room\/\d*/;
        url = url.replace(regex, '/room/' + roomId);
        if (url) {
            window.location = url;
        }
    }
}
