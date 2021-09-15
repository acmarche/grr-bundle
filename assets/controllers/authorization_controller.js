import {Controller} from "stimulus";

/**
 * Disabled option rooms if area administrator or manger resource
 */
export default class extends Controller {

    static targets = ['rooms']

    static values = {}

    connect() {

    }

    selectRole(event) {
        const radioValue = event.currentTarget;
        radioValue.disabled = !radioValue.disabled;

        console.log('id radio select: ' + radioValue);
        console.log(this.roomsTarget);
        this.roomsTarget.disable = true;
        if (radioValue === '1') {

        } else {

        }
    }

    t() {
        let typeList = document.querySelector('.authorization_role');
        let authorization_rooms = document.querySelector('.room-select');
        typeList.on('click', function (e) {
            loadOptionsWeeks()
        });

        loadOptionsWeeks();
    }

    loadOptionsWeeks() {
        var radioValue = document.querySelector(".form-check-input:checked").value;
        console.log(radioValue);
        if (radioValue === '1') {
            this.roomsTarget.disable = true;
        } else {
            this.roomsTarget.disabled = false;
        }
    }
}
