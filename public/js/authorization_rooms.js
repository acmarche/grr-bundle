/**
 * Disabled option rooms if area administrator or manger resource
 */
$(document).ready(function () {
    let typeList = $('.authorization_role');
    let authorization_rooms = $('.room-select');
    typeList.on('click', function (e) {
        loadOptionsWeeks()
    });

    loadOptionsWeeks();

    function loadOptionsWeeks() {
        var radioValue = $(".form-check-input:checked").val();

        if (radioValue === '1') {
            authorization_rooms.prop('disabled', true);
        } else {
            authorization_rooms.prop('disabled', false);
        }
    }
});