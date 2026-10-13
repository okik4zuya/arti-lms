---
title: "Claude Code"
order: 4
published: true
---

<style>
@import url('https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap');
.content {
    max-width: 600px;
    font-family: 'Atkinson Hyperlegible', var(--font-sans);
}
.content h1 { font-size: 1.85rem; }
.content h2 { font-size: 1.5rem; margin-top: 2em; }
.content p {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.9em;
}
</style>

# Claude Code

Di bagian [[Masih Ada yang Kurang]], saya cerita tentang dua ganjalan: memori yang belum sepenuhnya fleksibel, dan kemampuan write/edit file yang belum leluasa untuk manuskrip yang diiterasi berbulan-bulan.

Saya masih pakai cara manual: minta Claude menulis atau menganalisis, lalu saya copy-paste sendiri ke file kerja saya.

Ternyata, jawabannya sudah ada. Namanya Claude Code.

## Apa itu Claude Code?

Kalau Claude (yang saya bahas di bagian sebelumnya) adalah chatbot yang saya ajak "ngobrol" lewat browser, Claude Code adalah versi Claude yang jalan langsung di komputer saya, dengan akses ke file-file saya sendiri.

Awalnya, Claude Code dibuat untuk programmer — semacam asisten coding yang bisa membaca, menulis, dan mengedit kode di dalam sebuah proyek, bukan cuma menjawab pertanyaan tentang kode.

Tapi begitu saya coba pakai untuk menulis manuskrip, saya sadar: manuskrip itu, pada dasarnya, ya cuma file teks juga. Dan semua yang membuat Claude Code enak dipakai untuk coding, ternyata sama enaknya dipakai untuk menulis artikel ilmiah.

Tidak ada lagi acara copy-paste bolak-balik. Claude Code langsung membaca file manuskrip saya, mengedit bagian yang diminta, menyimpannya, dan tetap ingat seluruh riwayat pekerjaan selama saya masih berada di proyek yang sama.

## Kenapa Claude Code, secara jujur

Saya tidak mau bilang Claude Code ini sempurna. Tapi untuk kebutuhan saya — asisten menulis artikel ilmiah yang dikerjakan berbulan-bulan, berulang kali direvisi — ada tiga hal yang paling saya butuhkan, dan Claude Code menyediakan ketiganya:

- **Kontrol file yang sangat leluasa** — bisa baca, tulis, dan edit file manuskrip saya langsung, bukan sekadar menghasilkan file baru sekali jadi.
- **Memori yang fleksibel** — bisa diatur sendiri, mau dibuat persistent lewat file-file catatan yang saya susun sendiri, bukan memori otomatis yang bercampur antar topik seperti yang saya keluhkan di [[Solusi Parsial]].
- **Cocok untuk iterasi jangka panjang** — karena semuanya berbasis file di komputer saya sendiri, saya bisa keluar-masuk proyek kapan saja, dan pekerjaannya tetap ada, tetap "hidup", persis seperti yang saya cari di bagian sebelumnya.

Supaya lebih jujur dan tidak asal klaim, berikut perbandingannya dengan pilihan-pilihan lain yang sudah saya coba:

| | Claude Code | Claude AI | NotebookLM | LLM chat lain |
|---|---|---|---|---|
| File sumber | Baca langsung dari folder proyek di komputer saya, tanpa batas jumlah praktis | Upload manual sebagai project file | Upload manual sebagai sumber | Upload manual, sering hilang di sesi baru |
| Memori | Fleksibel — bisa dibuat persistent lewat file catatan yang saya susun sendiri | Ada, tapi per-Project dan tidak bisa saya atur strukturnya | Tidak ada memori antar sesi | Bawaan, tapi tercampur lintas topik |
| Tools | Bisa baca, tulis, edit file langsung, jalankan perintah di komputer saya | Bisa produksi file (docx, xlsx, dst), tapi bukan edit file yang sudah ada | Bisa membuat ringkasan, FAQ, atau audio overview dari sumber | Bervariasi, umumnya sebatas jawab teks |
| Penggunaan iterasi | Sangat cocok — edit di tempat, riwayat kerja tetap tersambung | Cukup, tapi tetap lewat copy-paste manual ke file kerja | Kurang cocok, lebih untuk membaca/meringkas, bukan menulis | Kurang cocok untuk manuskrip yang sama diedit berulang |
| Keamanan data | Data tetap di komputer saya sendiri, terkirim ke API hanya saat diproses | Tersimpan di server Anthropic | Tersimpan di server Google | Tergantung provider masing-masing |
| Biaya pemakaian | Langganan (atau API, tergantung kebutuhan) | Langganan | Gratis (dengan batas wajar) | Bervariasi |

Dari tabel ini, jelas kenapa Claude Code jadi pilihan saya. Bukan karena ia sempurna di semua baris — NotebookLM misalnya, tetap juara untuk urusan meringkas literatur, dan itu masih saya pakai berdampingan. Tapi untuk pekerjaan inti menulis manuskrip yang panjang dan terus direvisi, Claude Code satu-satunya yang menjawab ketiga kebutuhan saya sekaligus: kontrol file, memori fleksibel, dan iterasi jangka panjang.

## Menjawab "masih ada yang kurang"

Ingat pertanyaan yang saya tinggalkan di bagian sebelumnya? Apakah workflow copy-paste itu bisa dibuat lebih efisien?

Dengan Claude Code, jawabannya ya. Saya tidak lagi jadi juru copy-paste di tengah-tengah. Claude langsung "menyentuh" file manuskrip saya, dan karena memorinya bisa saya atur sendiri lewat file-file catatan, ia bisa tetap ingat konteks pekerjaan meskipun sesi chat-nya sudah berganti berkali-kali.

Tapi punya alat yang tepat saja belum cukup. Supaya memori itu benar-benar persistent dan terstruktur — bukan sekadar "menyimpan file sembarangan" — saya butuh sebuah sistem. Itulah yang akan saya bahas di bagian berikutnya: ARTi Framework.
