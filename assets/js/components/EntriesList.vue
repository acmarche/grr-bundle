<template>
    <div>
        <hr>
        <h3>Réservations</h3>
        <table
            v-show="entries.length > 0"
            class="table table-bordered"
        >
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Room</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="entry in entries"
                    :key="entry.id"
                >
                    <td>{{ entry.id }}</td>
                    <td>{{ entry.id }}</td>
                    <td>{{ entry.id }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'EntriesList',
    props: {
        entries: {
            type: Array,
            default() { return []; },
            required: true,
        },
    },
    data() {
        return {
            roomSelected: 0,
        };
    },
};
</script>

<style scoped>

</style>
