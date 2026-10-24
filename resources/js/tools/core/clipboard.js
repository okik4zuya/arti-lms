export async function copyText(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.append(ta);
        ta.select();
        let done = false;
        try { done = document.execCommand('copy'); } catch { /* blocked */ }
        ta.remove();
        return done;
    }
}
