/**
 * Enabled/disabled input
 * //todo not duplicate
 */
$(document).ready(function () {
    let fullDayCheckbox = $('#entry_duration_full_day');
    let durationTime = $('#entry_duration_time');
    let durationUnit = $('#entry_duration_unit');

    fullDayCheckbox.on('click', function (e) {
        setProp();
    });

    setProp();

    function setProp() {
        if (fullDayCheckbox.prop("checked") === true) {
            durationTime.val(0);
            durationTime.prop('disabled', true);
            durationUnit.prop('disabled', true);
        } else {
            durationTime.prop('disabled', false);
            durationUnit.prop('disabled', false);
        }
    }

    let fullDayCheckbox2 = $('#entry_with_periodicity_duration_full_day');
    let durationTime2 = $('#entry_with_periodicity_duration_time');
    let durationUnit2 = $('#entry_with_periodicity_duration_unit');

    fullDayCheckbox.on('click', function (e) {
        setProp2();
    });

    setProp2();

    function setProp2() {
        if (fullDayCheckbox2.prop("checked") === true) {
            durationTime2.prop('disabled', true);
            durationUnit2.prop('disabled', true);
            durationTime2.val(0);
        } else {
            durationTime2.prop('disabled', false);
            durationUnit2.prop('disabled', false);
        }
    }
});
