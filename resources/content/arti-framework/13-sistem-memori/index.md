---
title: "Sistem Memori"
order: 5
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

# Sistem Memori

Di [[Masih Ada yang Kurang]] dan [[Solusi Parsial]], saya sempat keluhkan memori bawaan Claude AI (dan chatbot lain sejenisnya).

Masalahnya bukan karena tidak ada memori sama sekali. Masalahnya, memori itu tercampur antar topik, dan saya tidak bisa mengatur strukturnya sendiri.

Tanya soal manuskrip A, tapi jawabannya kebawa konteks manuskrip B yang sama sekali tidak relevan. Atau lebih parah, konteks pribadi yang tidak saya perlukan malah ikut mempengaruhi jawaban.

## Cara ARTi mengatur memori

Di ARTi Framework, memori tidak disimpan sebagai satu gumpalan besar yang bercampur begitu saja.

Ada folder khusus bernama `memory/`. Isinya bukan satu file raksasa, tapi file-file kecil, satu file per topik.

Misalnya, kalau saya sedang mengerjakan tiga hal berbeda — riset gap literatur, desain eksperimen, dan draf pembahasan — masing-masing punya file catatannya sendiri.

Karena kecil dan per topik, tiap file gampang dicari. Gampang diperbarui juga, tanpa takut mengacaukan catatan topik lain yang tidak sedang saya kerjakan.

## File indeks sebagai penunjuk jalan

Supaya tidak membebani context window setiap kali sesi baru dibuka — ingat, ini salah satu penyebab masalah di [[2 Masalah Utama]] — ada satu file indeks.

File indeks ini isinya cuma pointer, satu baris per topik. Bukan isi lengkapnya.

Jadi di awal sesi, yang dibaca dulu cukup file indeks yang ringkas ini. Baru kalau memang perlu detail dari topik tertentu, file topik yang bersangkutan dibuka.

Cara ini menjaga supaya konteks yang masuk di awal sesi tetap relevan dan tidak berlebihan, sekaligus tetap bisa mengakses detail lengkap kapan saja dibutuhkan.

## Ritual sederhana

Supaya sistem ini benar-benar jalan, ada ritual sederhana yang saya pegang konsisten.

Kapan ditulis? Setiap kali ada keputusan atau fakta baru yang muncul selama sesi kerja.

Kapan dibaca? Di awal setiap sesi baru, sebelum mulai kerja apa pun.

Siapa yang memperbarui? Saya sendiri, dengan bantuan Claude Code — saya yang memutuskan apa yang penting dicatat, Claude Code yang membantu menuliskannya rapi ke file yang tepat.

## Efeknya

Efek dari sistem ini terasa langsung: sesi chat baru tidak perlu lagi re-upload file atau re-explain konteks dari nol.

Ini jawaban langsung untuk masalah kedua yang saya ceritakan di [[2 Masalah Utama]] — hilangnya memori setiap sesi baru dimulai.

Tidak ada lagi cerita seperti penderita Alzheimer yang saya singgung di awal buku ini. Ingatan lama tetap ada, tersimpan rapi, tinggal dibaca lagi kapan pun dibutuhkan.

Tapi memori saja belum cukup untuk menjawab masalah pertama — halusinasi. Untuk itu, saya punya sistem lain, yang akan saya bahas di bagian berikutnya: sistem referensi.
