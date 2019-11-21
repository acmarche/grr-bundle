/**
 * Enabled/disabled input
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
            durationTime.prop('disabled', true);
            durationUnit.prop('disabled', true);
        } else {
            durationTime.prop('disabled', false);
            durationUnit.prop('disabled', false);
        }
    }
});