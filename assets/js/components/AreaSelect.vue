<template>
    <div class="jfs">
        <h5>Area: {{ areaSelected }} Room {{ roomSelected }}</h5>
        <div class="form-group">
            <label
                class="required"
                for="area"
            >Domaine</label>
            <select
                id="area"
                v-model="areaSelected"
                name="area"
                class="custom-select my-1 mr-sm-2 form-control"
            >
                <option
                    v-for="area in areas"
                    :key="area.id"
                    :value="area.id"
                    :selected="selected(area.id)"
                >
                    {{ area.name }}
                </option>
            </select>
        </div>
        <div class="form-group">
            <label
                class=""
                for="room"
            >Ressource</label>
            <select
                id="room"
                v-model="roomSelected"
                name="room"
                class="custom-select my-1 mr-sm-2 form-control"
            >
                <option
                    v-show="!areaSelected"
                    disabled
                    value=""
                >
                    Please select area before
                </option>
                <option
                    v-for="room in rooms"
                    :key="room.id"
                    :value="room.id"
                >
                    {{ room.name }}
                </option>
            </select>
        </div>
        l:  {{ userLocation }}
    </div>
</template>

<script>
import { fetchAreas, fetchRooms } from '@/service/area-service';
import { inject } from 'vue';

export default {
    name: 'AreaSelect',
    //  inject: ['location', 'geolocation'],
    emits: ['room-selected'],
    setup() {
        const userLocation = inject('location', 'The Universe');
        const userGeolocation = inject('geolocation');
        return {
            userLocation,
            userGeolocation,
        };
    },
    data() {
        return {
            areas: Array,
            rooms: Array,
            areaSelected: 0,
            roomSelected: 0,
        };
    },
    watch: {
        areaSelected() {
            this.loadRooms();
        },
        roomSelected() {
            this.loadEntries();
        },
    },
    created() {
        this.loadAreas();
    },
    methods: {
        async loadAreas() {
            const response = await fetchAreas();
            this.areas = response.data['hydra:member'];
        },
        async loadRooms() {
            const response = await fetchRooms(this.areaSelected);
            this.rooms = response.data['hydra:member'];
        },
        loadEntries() {
            this.$emit('room-selected', { roomId: this.roomSelected });
        },
        selected(areaId) {
            return this.areaSelected === areaId ? 'selected' : 'no';
        },
    },
};
</script>
