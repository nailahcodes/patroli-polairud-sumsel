import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import './indexeddb-abk';
import './indexeddb-anev';

import { registerSW }
    from 'virtual:pwa-register';

registerSW({
    immediate: true
});

