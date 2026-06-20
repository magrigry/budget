import { ref, onMounted } from 'vue';

export type GeolocationState =
    | 'unknown'
    | 'fetching'
    | 'granted'
    | 'denied'
    | 'unavailable';

export interface Coordinates {
    latitude: number;
    longitude: number;
}

const state = ref<GeolocationState>('unknown');
const coordinates = ref<Coordinates | null>(null);

async function checkPermission(): Promise<void> {
    if (!navigator.geolocation) {
        state.value = 'unavailable';

        return;
    }

    if (!navigator.permissions) {
        return;
    }

    const result = await navigator.permissions.query({ name: 'geolocation' });

    if (result.state === 'granted') {
        state.value = 'granted';
        requestAndFetch().catch(() => {});
    } else if (result.state === 'denied') {
        state.value = 'denied';
    }

    result.addEventListener('change', () => {
        if (result.state === 'granted') {
            state.value = 'granted';
            requestAndFetch().catch(() => {});
        } else if (result.state === 'denied') {
            state.value = 'denied';
        } else {
            state.value = 'unknown';
        }
    });
}

function requestAndFetch(): Promise<Coordinates> {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            state.value = 'unavailable';
            reject(new Error('unavailable'));

            return;
        }

        state.value = 'fetching';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                state.value = 'granted';
                const coords: Coordinates = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };
                coordinates.value = coords;
                resolve(coords);
            },
            () => {
                state.value = 'denied';
                reject(new Error('denied'));
            },
        );
    });
}

export function mapsUrl(latitude: number, longitude: number): string {
    return `https://www.google.com/maps?q=${latitude},${longitude}`;
}

export function useGeolocation() {
    onMounted(() => {
        checkPermission();
    });

    return {
        state,
        coordinates,
        requestAndFetch,
    };
}
