<template>
    <div
        class="btn-group2"
    >
        <button
            class="btn btn-primary mr-2"
            type="button"
            @click="selectPrevious"
        >
            <i class="fas fa-angle-right" />
        </button>
        <button
            class="btn btn-primary mr-2"
            type="button"
            @click="selectCurrent"
        >
            Today
        </button>
        <button
            class="btn btn-primary"
            type="button"
            @click="selectNext"
        >
            <i class="fas fa-angle-left" />
        </button>
    </div>
</template>

<script>
import dayjs from 'dayjs';

export default {
    name: 'CalendarDateSelector',

    props: {
        currentDate: {
            type: String,
            required: true,
        },
        selectedDate: {
            type: Object,
            required: true,
        },
    },

    methods: {
        selectPrevious() {
            const newSelectedDate = dayjs(this.selectedDate).subtract(1, 'month');
            this.$emit('date-selected', newSelectedDate);
        },

        selectCurrent() {
            const newSelectedDate = dayjs(this.currentDate);
            this.$emit('date-selected', newSelectedDate);
        },

        selectNext() {
            const newSelectedDate = dayjs(this.selectedDate).add(1, 'month');
            this.$emit('date-selected', newSelectedDate);
        },
    },
};
</script>
