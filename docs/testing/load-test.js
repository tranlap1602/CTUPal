import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
    stages: [
        { duration: '20s', target: 15 },
        { duration: '30s', target: 30 },
        { duration: '30s', target: 50 },
        { duration: '1m', target: 60 },
        { duration: '30s', target: 20 },
        { duration: '30s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<1000'],
    },
};

const BASE_URL = 'http://100.100.242.95';

export default function () {
    let res = http.get(BASE_URL);
    check(res, {
        'status is 200': (r) => r.status === 200,
        'response time < 1000ms': (r) => r.timings.duration < 1000,
    });

    sleep(1.5);

    res = http.get(`${BASE_URL}/login.php`);
    check(res, { 'login page loaded': (r) => r.status === 200 });

    sleep(1.5);
}
