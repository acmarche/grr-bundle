<template>
    <td
        class="list-group-item"
        :class="{
            'calendar-day--not-current': !day.isCurrentMonth,
            'calendar-day--today': isToday
        }"
    >
        <span>{{ label }}</span>
    </td>
</template>

<script>
import dayjs from 'dayjs';

export default {
    name: 'CalendarMonthDayItem',

    props: {
        day: {
            type: Object,
            required: true,
        },

        isCurrentMonth: {
            type: Boolean,
            default: false,
        },

        isToday: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        label() {
            return dayjs(this.day.date).format('D');
        },
    },
};
</script>
