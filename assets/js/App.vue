<template>
    <div>
        <div>
            {{ isLoggedIn }}
            <button @click="increment">
                Increment
            </button>
            gg
        </div>
        <div class="row">
            <div class="col-3">
                <AreaSelect
                    class="titi"
                    @room-selected="loadEntries"
                />
            </div>
            <div class="col-9">
                <div class="row">
                    <div class="col-6">
                        <CalendarDateIndicator
                            :selected-date="selectedDate"
                        />
                    </div>
                    <div class="col-6">
                        <CalendarDateSelector
                            :current-date="today"
                            :selected-date="selectedDate"
                            @date-selected="selectDate"
                        />
                    </div>
                </div>
                <CalendarWeekdays />
                <ul class="list-group list-group-horizontal mt-2">
                    <CalendarMonthDayItem
                        v-for="day in days"
                        :key="day.date"
                        :day="day"
                        :is-today="day.date === today"
                    />
                </ul>
            </div>
        </div>
        <EntriesList
            :entries="entries"
        />
    </div>
</template>

<script>
import dayjs from 'dayjs';
import weekday from 'dayjs/plugin/weekday';
import weekOfYear from 'dayjs/plugin/weekOfYear';
import AreaSelect from '@/components/AreaSelect.vue';
import EntriesList from '@/components/EntriesList.vue';
import CalendarDateIndicator from '@/components/CalendarDateIndicator.vue';
import CalendarDateSelector from '@/components/CalendarDateSelector.vue';
import CalendarWeekdays from '@/components/CalendarWeekdays';
import CalendarMonthDayItem from '@/components/CalendarMonthDayItem';
import { fetchEntries } from '@/service/entry-service';
import { inject, provide } from 'vue';

dayjs.extend(weekday);
dayjs.extend(weekOfYear);

export default {
    name: 'App',
    components: {
        AreaSelect,
        EntriesList,
        CalendarMonthDayItem,
        CalendarWeekdays,
        CalendarDateIndicator,
        CalendarDateSelector,
    },

    setup() {
        provide('location', 'North Pole');
        provide('geolocation', {
            longitude: 90,
            latitude: 135,
        });
        // second optional param is a default value if it doesn't exist
        const isLoggedIn = inject(['global'], false);
        return {
            isLoggedIn,
        };
    },
    data() {
        return {
            entries: [],
            selectedDate: dayjs(),
        };
    },
    computed: {
        days() {
            return [
                ...this.previousMonthDays,
                ...this.currentMonthDays,
                ...this.nextMonthDays,
            ];
        },

        today() {
            return dayjs().format('YYYY-MM-DD');
        },

        month() {
            return Number(this.selectedDate.format('M'));
        },

        year() {
            return Number(this.selectedDate.format('YYYY'));
        },

        numberOfDaysInMonth() {
            return dayjs(this.selectedDate).daysInMonth();
        },

        currentMonthDays() {
            return [...Array(this.numberOfDaysInMonth)].map((day, index) => ({
                date: dayjs(`${this.year}-${this.month}-${index + 1}`).format(
                    'YYYY-MM-DD',
                ),
                isCurrentMonth: true,
            }));
        },

        previousMonthDays() {
            const firstDayOfTheMonthWeekday = this.getWeekday(
                this.currentMonthDays[0].date,
            );
            const previousMonth = dayjs(`${this.year}-${this.month}-01`).subtract(
                1,
                'month',
            );

            // Cover first day of the month being sunday (firstDayOfTheMonthWeekday === 0)
            const visibleNumberOfDaysFromPreviousMonth = firstDayOfTheMonthWeekday
                ? firstDayOfTheMonthWeekday - 1
                : 6;

            const previousMonthLastMondayDayOfMonth = dayjs(
                this.currentMonthDays[0].date,
            )
                .subtract(visibleNumberOfDaysFromPreviousMonth, 'day')
                .date();

            return [...Array(visibleNumberOfDaysFromPreviousMonth)].map(
                (day, index) => ({
                    date: dayjs(
                        `${previousMonth.year()}-${previousMonth.month()
                + 1}-${previousMonthLastMondayDayOfMonth + index}`,
                    ).format('YYYY-MM-DD'),
                    isCurrentMonth: false,
                }),
            );
        },

        nextMonthDays() {
            const lastDayOfTheMonthWeekday = this.getWeekday(
                `${this.year}-${this.month}-${this.currentMonthDays.length}`,
            );

            const nextMonth = dayjs(`${this.year}-${this.month}-01`).add(1, 'month');

            const visibleNumberOfDaysFromNextMonth = lastDayOfTheMonthWeekday
                ? 7 - lastDayOfTheMonthWeekday
                : lastDayOfTheMonthWeekday;

            return [...Array(visibleNumberOfDaysFromNextMonth)].map((day, index) => ({
                date: dayjs(
                    `${nextMonth.year()}-${nextMonth.month() + 1}-${index + 1}`,
                ).format('YYYY-MM-DD'),
                isCurrentMonth: false,
            }));
        },
    },
    methods: {
        async loadEntries({ roomId }) {
            const response = await fetchEntries(roomId);
            this.entries = response.data['hydra:member'];
        },
        selectDate(newSelectedDate) {
            this.selectedDate = newSelectedDate;
        },
        getWeekday(date) {
            return dayjs(date).weekday();
        },
    },
};
</script>
