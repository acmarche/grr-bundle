<template>
    <div>
        <h3>{{ selectedMonth }}</h3>
        <hr>
    </div>
</template>

<script>
export default {
    name: 'CalendarDateIndicator',
    props: {
        selectedDate: {
            type: Object,
            required: true,
        },
    },
    computed: {
        selectedMonth() {
            return this.selectedDate.format('MMMM YYYY');
        },
    },
};
</script>
