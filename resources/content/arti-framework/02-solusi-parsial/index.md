---
title: Solusinya
order: 2
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

# Solusinya

Setelah mengeluh panjang lebar soal halusinasi dan memori, pertanyaan berikutnya yang wajar muncul: lalu, apa solusinya?

Ternyata, keduanya sudah punya jawaban parsial masing-masing. Saya bilang parsial, karena — seperti akan Anda lihat — belum ada yang benar-benar tuntas.

## Solusi untuk halusinasi: menambah literatur

Cara paling ampuh menekan halusinasi adalah dengan memberi AI literatur selengkap mungkin sebagai pijakan jawaban, bukan membiarkannya menjawab dari "ingatan" sendiri yang bisa saja salah atau sudah kedaluwarsa.

Salah satu tool yang cukup nyaman untuk ini adalah NotebookLM. Anda upload sekumpulan referensi, dan AI-nya akan menjawab dengan basis dokumen-dokumen tersebut, lengkap dengan sitasinya. Sangat nyaman.

Tapi — dan ini akan saya bahas lebih detail nanti — NotebookLM punya kelemahan lain. Ia tidak punya kemampuan untuk menulis dan mengedit file secara langsung, kecuali lewat pembuatan file sebagai output. Jadi ia bagus untuk riset dan membaca, tapi bukan tempat untuk benar-benar menulis artikel.

## Solusi untuk memori: fitur memori bawaan

Untuk masalah memori, beberapa AI chat sekarang sudah punya fitur memori bawaan — ChatGPT, Gemini, dan Claude termasuk di antaranya (mungkin ada AI chat lain yang belum sempat saya coba).

Ini mungkin terlihat seperti solusi yang pas untuk masalah "50 First Dates" yang saya ceritakan sebelumnya. Akhirnya, AI-nya bisa ingat, kan?

Sayangnya, tidak sesederhana itu.

Masalahnya, sistem memori yang ada bercampur dengan memori dari chat-chat lain. Semua topik, semua sesi, semua konteks yang pernah saya bahas dengan AI itu — numplek jadi satu memori besar. Ini justru mengganggu konteks yang sedang saya bicarakan saat ini.

Bahkan pada suatu sesi di mana saya sama sekali tidak butuh suatu memori masa lampau, memori itu tetap muncul dan mengintervensi output dari sesi chat yang sedang berjalan.

Contoh sederhana: saya ingin sebuah pertanyaan dijawab singkat dan langsung ke inti. Tapi jawabannya malah dibumbui berdasarkan sudut pandang AI yang "mengenal" saya sebagai dosen di suatu kampus — informasi yang memang pernah saya sebut di sesi lain, tapi sama sekali tidak relevan dan tidak saya perlukan di sesi ini.

Jadi begitulah. Literatur menambal halusinasi, tapi menyisakan masalah baru soal write-edit file. Memori bawaan menambal soal "lupa siapa kita", tapi menyisakan masalah baru soal konteks yang tercampur dan sulit dikendalikan.

Dua solusi, dua kelemahan baru. Dan dari sinilah cerita framework ini sebenarnya dimulai.
