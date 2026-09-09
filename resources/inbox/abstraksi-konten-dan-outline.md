# Chapter 1: Pendahuluan
## Pendahuluan Bagian 1

Sampai detik ini (setidaknya sampai framework ini dibuat), belum ada satupun layanan AI yang memuaskan saya untuk tujuan membantu menulis artikel ilmiah.

Saya sudah mencoba berbagai AI berbasis chat termasuk ChatGPT, Gemini, DeepSeek, Grok, z.ai, dan Claude. Saya juga sudah mencoba beberapa layanan AI berbasis SaaS untuk mencari referensi dan menulis artikel seperti Consensus, SciSpace, Elicit, Scite, dan Bohrium. Bahkan saya juga pernah membangun sendiri aplikasi AI berbasis chat yang mengonsumsi API model AI dari berbagai provider yang saya beri nama "Fine AI". Ceritanya, saya ingin membuat AI chat yang konteks nya bisa di "fine tune" agar memberikan hasil yang maksimal dengan cara "mengoperasi" (tambah, edit, hapus) chat-chat sebelumnya. Saya menggunakannya dalam waktu yang lumayan lama karena saya cukup puas dengan hasilnya, namun ternyata saya menyadari bahwa penggunaan model AI menggunakan API ternyata lebih mahal daripada dengan langganan. Akhirnya tidak saya teruskan.

Pertanyaannya, mengapa saya tidak puas?
Ada 2 poin utama, yaitu halusinasi dan memori.

Pertama, halusinasi.

Halusinasi adalah ketika model AI menjawab secara "ngawur" dan tidak berdasarkan data. Ini sangat berbahaya jika digunakan untuk penulisan artikel ilmiah, dimana kebenaran data menjadi aspek yang sangat penting.

Solusi dari halusinasi adalah dengan cara memberikan sumber selengkap mungkin dan melakukan iterasi berkali-kali.

Namun, seiring bertambahnya referensi dan menumpuknya chat dalam satu sesi, konteks jadi kehilangan kualitasnya. Model AI tidak lagi mengingat aturan-aturan dan sumber yang saya berikan di awal. Akhirnya, semakin kesini semakin ngawur lagi.

Hal ini kemungkinan dikarenakan aplikasi AI berbasis chat membatasi jendela konteks (context window) sehingga chat-chat yang lebih lama tidak lagi masuk ke dalam konteks yang sedang kita diskusikan.

Apakah anda merasakannya juga?

Kedua, memori.
Anggaplah saya sudah mendapatkan suatu sesi chat dengan AI dengan konteks yang sangat bagus dan model menjawab dengan sangat baik. Misalnya karena saya sudah memberi konteks literatur yang banyak, sebutlah 20 literatur. Nah, kemudian saya membuat sesi baru, saya perlu mengulang untuk mengondisikan konteks agar tetap sama seperti sesi sebelumnya, mengupload file-file nya lagi. Mengajak diskusi AI lagi di awal sesi untuk memastikan model AI mengerti konteks diskusi. 
Ini sangat merepotkan. Membuat pekerjaan menjadi tidak efisien.
Apa penyebabnya? Yaitu model AI tidak punya ingatan setiap sesi baru dimulai.

Agar lebih mudah, kalau saya analogikan, model AI itu sangat mirip dengan penderita penyakit Alzheimer. Plek ketiplek.

Dia hanya ingat ingatan lama (yaitu knowledgebase yang sudah dilatihkan kepadanya sampai batas waktu tertentu). Tapi tidak ingat apapun tentang apa yang terjadi kemarin (yaitu sesi chat dengan kita sebelumnya).

Saya jadi ingat sebuah film yang sangat relate. Apakah anda tahu film "50 first dates!"?

Silahkan tonton di bawah ini.

[Video ending 50 first dates]


[penjelasan tentang film, hubungkan dengan kasus analogi AI]

## Pendahuluan Bagian 2: Solusi dari Halusinasi dan Memori
- menambah literatur: di antaranya diselesaikan dengan notebooklm (misalnya), sangat nyaman
- tapi notebooklm punya kelemahan lain yang akan dijelaskan kemudian, yaitu tidak ada kemampuan write dan edit file, kecuali melalui pembuatan file sebagai output
- memori: chatgpt, gemini, dan claude (mungkin AI chat lainnya yang belum sy coba) sudah memiliki fitur memori
- Ini mungkin terlihat seperti menjadi solusi untuk masalah ini, namun, masalahnya adalah sistem memori yang ada bercampur dengan memori dari chat-chat lain yang mengganggu konteks yang sedang dibicarakan
- bahkan pada suatu sesi dimana kita tidak butuh suatu memori di masa lampau, malah mengintervensi output dari suatu sesi chat
- Misalnya kita ingin pertanyaan dijawab singkat, tapi dijawab berdasarkan sudut pandang AI melihat kita sebagai dosen di suatu kampus, yang sebenarnya tidak kita perlukan


## Hilang memori = pekerjaan tidak berarti
[Perumpamaan perempuan pemintal benang yang memintal dari sejak pagi, lalu mengurainya di sore hari]

# Chapter 2: Claude dan Claude Code
## Claude AI: Solusi yang hampir sempurna
- Saya menemukan fitur favorit di Claude, yaitu "project"
- Di sana saya bisa menambah instruksi, menambah project file sebagai sumber diskusi yang dapat diakses di semua sesi chat, dan bahkan saat ini sudah tersedia memori untuk setiap project
- Jadi claude akan mengingat memori hanya pada project tersebut
- Selain itu, claude bisa memproduksi berbagai file seperti docx, xlsx, dan lain sebagainya menggunakan fitur tools yang disediakan

## Claude: Model yang paling mengerti logika dan pintar analisis

## Claude bisa pakai skill agar hasil lebih maksimal

## Claude code: Mengapa claude code

## Perbandingan Claude code dengan yang lain (tabel)

# Chapter 3: ARTi Framework
## Solusi dari masalah saya selama ini (mungkin anda juga)

## 3 Filosofi

## Menjadikan pekerjaan berarti

## Arsitektur

## Sistem Memori

## Sistem Referensi

## dan lainnya

# Chapter 4: Amati - topik 1

dan seterusnya

# Chapter X


# BONUS

# Panduan instalasi

# Prompt Guide

# dan lainnya
