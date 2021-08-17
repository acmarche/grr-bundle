import {Controller} from "stimulus";
/**
 * Display/Hide options for week periodicity
 */
export default class extends Controller {

    static targets = []

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
        let typeList = $('#entry_with_periodicity_periodicity_type');
        let periodicityZone = $('#weeks_options');
        typeList.on('click', function (e) {
            loadOptionsWeeks()
        });

    }

    loadOptionsWeeks() {
        let radioValue = $("input[name='entry_with_periodicity[periodicity][type]']:checked").val();
        console.log(radioValue);
        if (radioValue === '2') {
            periodicityZone.removeClass('d-none');
        } else {
            if (!periodicityZone.hasClass('d-none')) {
                periodicityZone.addClass('d-none');
            }
        }
    }

}
