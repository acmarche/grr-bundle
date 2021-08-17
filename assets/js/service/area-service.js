import axios from 'axios';

/**
 * @returns {Promise}
 */
export function fetchAreasLocal() {
    return new Promise((resolve, reject) => {
        resolve({
            data: {
                'hydra:member': window.areas,
            },
        });
    });
}

/**
 * @returns {Promise}
 */
export function fetchAreas() {
    const params = {};

    return axios.get('/api/areas', {
        params,
    });
}

/**
 * @param {int|null} areaId
 * @returns {Promise}
 */
export function fetchRooms(areaId) {
    const params = {};

    if (areaId) {
        params.area = areaId;
    }

    return axios.get('/api/rooms', {
        params,
    });
}
