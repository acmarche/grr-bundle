import axios from 'axios';

export function fetchEntries(roomIri) {
    const params = {};

    if (roomIri) {
        params.room = roomIri;
    }

    return axios.get('/api/entries', {
        params,
    });
}
