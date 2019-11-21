/**
 * Display/Hide options for week periodicity
 */
$(document).ready(function () {
    let typeList = $('#entry_with_periodicity_periodicity_type');
    let periodicityZone = $('#weeks_options');
    typeList.on('click', function (e) {
        loadOptionsWeeks()
    });

    loadOptionsWeeks();

    function loadOptionsWeeks() {
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
});