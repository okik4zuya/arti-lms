---
title: "2 Masalah: Halusinasi dan Memori"
order: 1
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

# 2 Masalah: Halusinasi dan Memori

Sampai detik ini (setidaknya sampai framework ini dibuat), belum ada satupun layanan AI yang memuaskan saya untuk tujuan membantu menulis artikel ilmiah.

Saya sudah mencoba berbagai AI berbasis chat, termasuk ChatGPT, Gemini, DeepSeek, Grok, z.ai, dan Claude.

Saya juga sudah mencoba beberapa layanan AI berbasis SaaS untuk mencari referensi dan menulis artikel, seperti Consensus, SciSpace, Elicit, Scite, dan Bohrium.

Bahkan saya juga pernah membangun sendiri aplikasi AI berbasis chat yang mengonsumsi API model AI dari berbagai provider, saya beri nama "Fine AI".

Ceritanya, saya ingin membuat AI chat yang konteksnya bisa di-"fine tune" agar memberikan hasil yang maksimal, dengan cara "mengoperasi" (tambah, edit, hapus) chat-chat sebelumnya.

Saya menggunakannya dalam waktu yang lumayan lama karena saya cukup puas dengan hasilnya.

Namun ternyata saya menyadari, penggunaan model AI menggunakan API itu lebih mahal daripada langganan biasa.

Akhirnya tidak saya teruskan.

Pertanyaannya, mengapa saya tidak puas?

Ada 2 poin utama, yaitu halusinasi dan memori.

## Pertama, halusinasi

Halusinasi adalah ketika model AI menjawab secara "ngawur" dan tidak berdasarkan data.

Ini sangat berbahaya jika digunakan untuk penulisan artikel ilmiah, dimana kebenaran data menjadi aspek yang sangat penting.

Solusi dari halusinasi adalah dengan cara memberikan sumber selengkap mungkin, dan melakukan iterasi berkali-kali.

Namun, seiring bertambahnya referensi dan menumpuknya chat dalam satu sesi, konteks jadi kehilangan kualitasnya.

Model AI tidak lagi mengingat aturan-aturan dan sumber yang saya berikan di awal.

Akhirnya, semakin ke sini, semakin ngawur lagi.

Hal ini kemungkinan dikarenakan aplikasi AI berbasis chat membatasi jendela konteks (context window), sehingga chat-chat yang lebih lama tidak lagi masuk ke dalam konteks yang sedang kita diskusikan.

Apakah Anda merasakannya juga?

## Kedua, memori

Anggaplah saya sudah mendapatkan suatu sesi chat dengan AI, dengan konteks yang sangat bagus, dan model menjawab dengan sangat baik.

Misalnya karena saya sudah memberi konteks literatur yang banyak, sebutlah 20 literatur.

Nah, kemudian saya membuat sesi baru.

Saya perlu mengulang untuk mengondisikan konteks agar tetap sama seperti sesi sebelumnya, mengupload file-filenya lagi.

Mengajak diskusi AI lagi di awal sesi, untuk memastikan model AI mengerti konteks diskusi.

Ini sangat merepotkan.

Membuat pekerjaan menjadi tidak efisien.

Apa penyebabnya?

Yaitu, model AI tidak punya ingatan setiap sesi baru dimulai.

Agar lebih mudah, kalau saya analogikan, model AI itu sangat mirip dengan penderita penyakit Alzheimer. Plek ketiplek.

Dia hanya ingat ingatan lama, yaitu knowledgebase yang sudah dilatihkan kepadanya sampai batas waktu tertentu.

Tapi tidak ingat apapun tentang apa yang terjadi kemarin, yaitu sesi chat dengan kita sebelumnya.

Saya jadi ingat sebuah film yang sangat relate.

Apakah Anda tahu film "50 First Dates"?

Ceritanya, tokoh utama perempuan mengalami amnesia jangka pendek akibat kecelakaan.

Setiap bangun tidur, ingatannya kembali ke "hari kecelakaan itu", dan semua yang terjadi sehari sebelumnya hilang tak berbekas.

Tokoh utama pria, yang mencintainya, terpaksa memperkenalkan diri lagi, menceritakan ulang siapa dirinya, apa yang sudah mereka lalui bersama — setiap. hari.

Bukan karena hubungan mereka tidak berkembang, tapi karena setiap pagi, dari sudut pandang si perempuan, hubungan itu belum pernah dimulai.

Begitu juga rasanya bekerja dengan AI yang tidak punya memori.

Setiap sesi baru adalah "hari kecelakaan" itu lagi.

Kita yang harus jadi si tokoh pria — mengulang perkenalan, mengulang konteks, mengulang semua yang sudah pernah kita bangun bersama — hanya supaya AI-nya "ingat" lagi siapa kita dan apa yang sedang kita kerjakan.

Bedanya, di film itu penuh cinta dan kesabaran, karena hanya menyangkut dua orang.

Kalau ini terjadi setiap kali kita mau menulis artikel ilmiah, yang penuh dengan detail teknis, referensi, dan aturan penulisan yang njlimet?

Itu bukan cinta lagi namanya. Itu pekerjaan tanpa akhir.
