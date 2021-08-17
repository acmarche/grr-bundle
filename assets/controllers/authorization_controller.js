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
        console.log('ici');
        const radioValue = event.currentTarget;
        radioValue.disabled = !radioValue.disabled;

        console.log('id radio select: ' + radioValue);
        if (radioValue === '1') {

        } else {

        }
    }

    t() {
        let typeList = $('.authorization_role');
        let authorization_rooms = $('.room-select');
        typeList.on('click', function (e) {
            loadOptionsWeeks()
        });

        loadOptionsWeeks();
    }

    loadOptionsWeeks() {
        var radioValue = $(".form-check-input:checked").val();

        if (radioValue === '1') {
            authorization_rooms.prop('disabled', true);
        } else {
            authorization_rooms.prop('disabled', false);
        }
    }
}
