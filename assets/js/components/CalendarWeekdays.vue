<template>
    <ul class="list-group list-group-horizontal mt-2">
        <li
            v-for="weekday in weekdays"
            :key="weekday"
            class="list-group-item"
        >
            {{ weekday }}
        </li>
    </ul>
</template>

<script>
const WEEKDAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

export default {
    name: 'CalendarWeekdays',

    computed: {
        weekdays() {
            return WEEKDAYS;
        },
    },
};
</script>
