export function downloadBlob(name, blob) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = name;
    document.body.append(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 10000);
}

export const downloadText = (name, text, type = 'text/plain') =>
    downloadBlob(name, new Blob([text], { type: type + ';charset=utf-8' }));

export function canvasToBlob(canvas, type = 'image/png') {
    return new Promise((resolve, reject) => {
        try {
            canvas.toBlob((b) => (b ? resolve(b) : reject(new Error('Browser tidak bisa membuat PNG dari gambar ini.'))), type);
        } catch (e) {
            reject(e);
        }
    });
}

// Canvas limits differ per browser; stay well inside Chrome's and Firefox's.
export const MAX_SIDE = 16000;
export const MAX_AREA = 120e6;
export function canvasFits(w, h) {
    return w > 0 && h > 0 && w <= MAX_SIDE && h <= MAX_SIDE && w * h <= MAX_AREA;
}
