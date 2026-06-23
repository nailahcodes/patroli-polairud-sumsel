const DB_NAME = 'polairudDB';
const STORE_NAME = 'abkDraft';

function openDB() {

    return new Promise((resolve, reject) => {

        const request =
            indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = () => {

            const db = request.result;

            if (!db.objectStoreNames.contains(STORE_NAME)) {

                db.createObjectStore(
                    STORE_NAME,
                    { keyPath: 'id' }
                );

            }

        };

        request.onsuccess =
            () => resolve(request.result);

        request.onerror =
            () => reject(request.error);

    });

}

export async function deleteDraft(id) {

    const db = await openDB();

    const tx = db.transaction(
        STORE_NAME,
        'readwrite'
    );

    tx.objectStore(STORE_NAME)
        .delete(id);

}

async function saveDraft(id, data) {

    const db = await openDB();

    const tx =
        db.transaction(STORE_NAME, 'readwrite');

    tx.objectStore(STORE_NAME)
        .put({
            id,
            data,
            updated_at:
                new Date().toISOString()
        });

}

async function loadDraft(id) {

    const db = await openDB();

    const tx =
        db.transaction(STORE_NAME, 'readonly');

    return new Promise(resolve => {

        const request =
            tx.objectStore(STORE_NAME)
                .get(id);

        request.onsuccess =
            () => resolve(request.result);

    });

}