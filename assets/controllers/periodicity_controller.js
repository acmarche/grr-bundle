import {Controller} from "stimulus";

/**
 * Display/Hide options for week periodicity
 */
export default class extends Controller {

    static targets = ['listPeriodicity']

    static values = {}

    connect() {
        this.loadOptionsWeeks();
    }

    loadOptionsWeeks() {
        this.listPeriodicityTarget.addEventListener('click', function (e) {
            let radio = document.querySelector("input[name='entry_with_periodicity[periodicity][type]']:checked");
            if (radio) {
                //all weeks
                if (radio.value === '2') {
                    let periodicityZone = document.querySelector('#weeks_options');
                    periodicityZone.classList.toggle('d-none');
                }
            }
        });
    }
}
