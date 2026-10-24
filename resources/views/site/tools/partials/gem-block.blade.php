{{-- The Gem: copyable instruction, knowledge file, and the share link when it exists. Works without any Gem. --}}
<section class="landing-section landing-section--alt" id="gem">
    <div class="container">
        <h2>Instruksi untuk AI-nya.</h2>
        <p>Gem adalah asisten khusus di Gemini. Tidak harus Gem: instruksi yang sama bisa ditempel di awal percakapan di Gemini, ChatGPT, atau Claude.</p>

        <div class="gem">
            <label for="gem-instruction" class="form-label">Instruksi <span class="muted" data-gem-count></span></label>
            <textarea id="gem-instruction" class="form-input gem__text" readonly rows="10" spellcheck="false" data-gem-source>{{ $instruction }}</textarea>
            <div class="hero__actions">
                <button type="button" class="btn btn--primary" data-gem-copy="#gem-instruction">Salin instruksi</button>
                <a href="{{ $knowledgeUrl }}" class="btn btn--outline" download>Unduh berkas pengetahuan</a>
                @if (filled($tool['gem_url']))
                    <a href="{{ $tool['gem_url'] }}" class="btn btn--outline" target="_blank" rel="noopener">Buka Gem di Gemini</a>
                @endif
            </div>
            <p class="muted" data-gem-status role="status"></p>
        </div>

        <details class="faq-item">
            <summary>Cara membuat Gem sendiri (2 menit)</summary>
            <ol class="steps-list">
                <li>Buka gemini.google.com di browser komputer, lalu Gem manager &rarr; Gem baru.</li>
                <li>Tempel instruksi di atas ke kolom instruksi.</li>
                <li>Unggah berkas pengetahuan (tombol di atas) sebagai file pengetahuan Gem.</li>
                <li>Simpan, lalu mulai percakapan dengan menceritakan gambar yang Anda mau.</li>
            </ol>
            <p class="muted">Bila Gemini menolak instruksi karena terlalu panjang, tempel bagian awalnya saja dan unggah sisanya sebagai berkas pengetahuan.</p>
        </details>
    </div>
</section>
