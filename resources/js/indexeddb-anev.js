const DB_NAME = 'polairudDB';
const STORE_NAME = 'anevDraft';

function openAnevDB() {

    return new Promise((resolve, reject) => {

        const request =
            indexedDB.open(DB_NAME, 2);

        request.onupgradeneeded = () => {

            const db = request.result;

            if (
                !db.objectStoreNames.contains(
                    STORE_NAME
                )
            ) {
                db.createObjectStore(
                    STORE_NAME,
                    {
                        keyPath: 'id'
                    }
                );
            }
        };

        request.onsuccess =
            () => resolve(request.result);

        request.onerror =
            () => reject(request.error);

    });

}

export async function saveAnevDraft(
    id,
    data
) {

    const db =
        await openAnevDB();

    const tx =
        db.transaction(
            STORE_NAME,
            'readwrite'
        );

    tx.objectStore(STORE_NAME)
        .put({
            id,
            data,
            updated_at:
                new Date()
                    .toISOString()
        });

}

export async function loadAnevDraft(
    id
) {

    const db =
        await openAnevDB();

    const tx =
        db.transaction(
            STORE_NAME,
            'readonly'
        );

    return new Promise(resolve => {

        const request =
            tx.objectStore(
                STORE_NAME
            ).get(id);

        request.onsuccess =
            () => resolve(
                request.result
            );

    });

}

export async function deleteAnevDraft(id) {

    const db =
        await openAnevDB();

    const tx =
        db.transaction(
            STORE_NAME,
            'readwrite'
        );

    tx.objectStore(STORE_NAME)
        .delete(id);

}