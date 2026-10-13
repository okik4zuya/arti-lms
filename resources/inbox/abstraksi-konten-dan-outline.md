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


## Halusinasi = Meruntuhkan kerdibilitas akademik
- Halusinasi itu sangat berbahaya. bisa menyebabkan kesalahan jariyah. artinya jika AI membuat statement yang salah, lalu kita tambahkan begitu saja ke artikel kita, artikel kita kemudian dirujuk orang, lalu artikal yang merujuk artikel kita dirujuk orang lagi, dan seterusnya, maka kesalahan itu akan terus menerus disebarkan bahkan sampai-sampai dianggap menjadi suatu kebenaran. Ngeri sekali.
- Oleh karena itu, wajar saja jika banyak pro dan kontra mengenai penggunaan AI ini, apalagi jika dikaji lebih dalam dari aspek lainnya.
- Adapun menurut saya, penggunaan AI sah-sah saja, dengan syarat, digunakan dengan bijak dan tetap mempertahankan otak kita tetap berfikir agar tidak tumpul.
[masukkan bagian narasi ai bar-bar, cek di inbox]

- hubungkan dengan solusi agar penggunaan AI tidak meruntuhkan kredibilitas akademik

## Hilang memori = pekerjaan tidak berarti
[Perumpamaan perempuan pemintal benang yang memintal dari sejak pagi, lalu mengurainya di sore hari]
- dikisahkan ada seorang perempuan pemintal benang dengan 


# Chapter 2: Claude: Apakah Ini Jawabannya?
## Taaruf
- Suatu ketika, saya mencoba claude. Dan langsung jatuh cinta.
- Pengalaman saya menggunakan claude, modelnya sangat pintar menganalisis, meskipun dalam hal membuat kata-kata, kalah dengan chatGPT. Tapi justri ini yang saya perlukan. (penjelasan di bagian berikutnya)
- Saya menemukan fitur favorit di Claude, yaitu "project"
- Di sana saya bisa menambah instruksi, menambah project file sebagai sumber diskusi yang dapat diakses di semua sesi chat, dan bahkan saat ini sudah tersedia memori untuk setiap project
- Project files: menjawab halusinasi
- Jadi claude akan mengingat memori hanya pada project tersebut
- Selain itu, claude bisa memproduksi berbagai file seperti docx, xlsx, dan lain sebagainya menggunakan fitur tools yang disediakan

## Claude: Model dengan Branding Logika
- penjelasan tentang claude haiku, sonnet, opus, dan fable
- benchmark dengan model lain di luar anthropic
- claude: model dengan branding logika (setiap model punya branding masing-masing)
- bukti dari programmer: jika menggunakan claude sonnet, setiap diberi perintah, mayoritas kode langsung berjalan sesuai ekspektasi, meskipun ada error, perbaikannya minor
- jika menggunakan model lain, misal gpt dan gemini, setelah menjalankan perintah, biasanya masih banyak error yang harus diperbaiki
- itu karena claude adalah model yang paling mengerti logika dan analisis
- oleh karena itu, sangat cocok diajak jadi asisten dalam menulis artikel, yang tidak hanya butuh pintar membuat kata-kata, tapi harus pandai menganalisis

## Masih Ada yang Kurang
- meskipun saya sudah "nyaman" menggunakan claude, tapi entah kenapa, sepertinya masih ada yang kurang
- masalah memori yang tidak fleksibel dan kemampuan write dan edit file
- tidak leluasa untuk mengerjakan sebuah manuscript yang diiterasi berulang kali dengan memori yang persistent
- saya masih tetap menggunakannya, tapi masih pakai sistem copy paste ke file manuscript yang saya kerjakan
- dan ini memang tidak masalah, hanya saja saya berfikir apakah workflow nya bisa dibuat menjadi lebih efisien?


## Claude Code
- bridging dari "masih ada yang kurang"
- buat tabel untuk menunjukkan kenapa Claude Code dipilih untuk asisten menulis artikel, secara jujur
- kelebihan utama yang diinginkan: kontrol file yang sangat leluasa, memori yang fleksibel (bisa diatur agar persistent), cocok untuk iterasi jangka panjang
- Kolom dan baris:
kolom: Claude Code, Claude Code, Claude AI, NotebookLM, LLMs berbasis chat lain
Baris: File sumber, memori, tools, penggunaan iterasi, keamanan data, (usulkan lainnya
)
- pastikan bagian ini menjawab bagian "masih ada yang kurang"


# Chapter 3: ARTi Framework
## Solusi dari masalah saya selama ini (mungkin anda juga)
- bridging dari penutup Chapter 2 (Claude Code): alat yang tepat saja belum cukup, supaya memori benar-benar persistent dan terstruktur, saya butuh sebuah sistem
- tegaskan lagi 2 masalah dari Chapter 1 (halusinasi, memori) + 1 celah dari Chapter 2 (Claude Code hebat sebagai alat, tapi belum jadi sistem/metodologi)
- perkenalkan nama: ARTi Framework, sebagai jawaban saya — bukan sekadar tools tambahan, tapi cara kerja yang saya susun sendiri di atas Claude Code
- "mungkin anda juga": tegaskan bahwa ini bukan cuma keluhan pribadi, kemungkinan besar dialami siapapun yang pernah coba pakai AI untuk menulis artikel ilmiah secara serius
- jujur di awal: framework ini masih terus berkembang, bukan produk jadi yang final, tapi sudah saya pakai sendiri dan terbukti menyelesaikan masalah saya
- tutup dengan bridging ke subchapter berikutnya: nama "ARTi" sendiri punya 3 lapis makna, yang sekaligus jadi filosofi framework ini

## 3 Filosofi
- filosofi 1 — ART + i: "state of the art" (penelitian harus berangkat dari apa yang sudah ada, bukan dari nol) ditambah "i" = inovasi milik peneliti sendiri di atas state of the art itu
- filosofi 2 — Amati, Replikasi, Tambah inovasi: alur kerja 3 tahap, sekaligus jadi 3 bagian besar buku ini setelah Pendahuluan; jelaskan singkat tiap tahap (Amati = memahami gap dan lanskap literatur, Replikasi = menguji kelayakan lewat replikasi sebelum berinovasi, Tambah Inovasi = menambahkan kontribusi baru yang bisa dipertanggungjawabkan)
- filosofi 3 — arti (Bahasa Indonesia = makna): callback langsung ke Chapter 1 ("Hilang Memori = Pekerjaan Tidak Berarti"), pekerjaan yang berarti adalah pekerjaan yang hasilnya menumpuk, bukan yang diulang dari nol tiap sesi
- tunjukkan bagaimana 3 makna ini menyatu jadi satu nama: bukan akronim yang dipaksakan, tapi memang begitu cara saya membangun & memakainya
- bridging ke subchapter berikutnya: fokus dulu ke makna ketiga ("arti"), karena itu jawaban paling langsung untuk masalah di Chapter 1

## Menjadikan pekerjaan berarti
- kembali ke perumpamaan pemintal benang di Chapter 1 (memintal dari pagi, mengurai lagi di sore hari — kerja yang tidak pernah terakumulasi)
- bedanya dengan sistem ARTi: setiap sesi kerja disimpan, dipetakan, dan bisa diambil lagi — jadi benang yang terus dipintal jadi kain utuh, bukan diurai lagi tiap malam
- 3 pilar yang membuat itu mungkin (ditulis sebagai preview, masing-masing dapat pembahasan sendiri di subchapter berikutnya): memori yang persistent, referensi yang terlacak, dan scratchbook yang mendisiplinkan proses
- jujur: bukan berarti nol usaha, tapi usaha yang dikeluarkan sekarang benar-benar "nempel" untuk sesi-sesi berikutnya, tidak hilang begitu chat ditutup
- bridging: supaya tidak abstrak, subchapter berikutnya akan menunjukkan bentuk konkretnya — arsitekturnya seperti apa

## Arsitektur
- gambaran besar: ARTi Framework = Claude Code (mesin) + struktur folder & skill yang saya susun di atasnya (peta jalan)
- komponen utama, ringkas dalam bentuk daftar/tabel: skills (ARTi-setup untuk onboarding project baru, ARTi-idea untuk gap map sampai penentuan jurnal, ARTi-writing untuk draft sampai rebuttal, ARTi-figure untuk diagram & tabel), folder memory/ dan inbox/, dashboard lokal
- semua jalan secara lokal di komputer sendiri, tidak ada server pihak ketiga yang menyimpan data — callback ke poin "keamanan data" di tabel perbandingan Chapter 2
- jujur: bukan software sekali install lalu otomatis jadi, tetap perlu dipakai dan diisi sendiri seiring proses menulis berjalan
- bridging: dari sekian komponen, dua yang paling menjawab masalah Chapter 1 adalah sistem memori dan sistem referensi — dibahas satu-satu di subchapter berikutnya

## Sistem Memori
- kontraskan dengan memori bawaan Claude AI yang dikeluhkan di Chapter 2 (`Masih Ada yang Kurang`, `Solusi Parsial`): tercampur antar topik, tidak bisa diatur strukturnya
- cara ARTi: folder `memory/` isinya file-file kecil per topik, bukan satu file raksasa — tiap topik gampang dicari, gampang diperbarui
- ada file indeks yang cuma berisi pointer satu baris ke tiap file topik, bukan isi lengkapnya — supaya tidak membebani context window tiap sesi baru dibuka (jawaban langsung untuk keluhan context window di Chapter 1)
- ritual sederhana: kapan ditulis (tiap kali ada keputusan/fakta baru), kapan dibaca (di awal sesi), siapa yang update (saya sendiri, dengan bantuan Claude Code)
- efeknya: sesi chat baru tidak perlu lagi re-upload file atau re-explain konteks dari nol — jawaban langsung untuk masalah #2 (memori) di Chapter 1

## Sistem Referensi
- jawaban untuk masalah #1 di Chapter 1: halusinasi
- setiap klaim yang ditulis wajib ditandai sumbernya (source-tagging) — bukan aturan formalitas, tapi supaya AI tidak bisa "ngarang" tanpa jejak yang bisa saya telusuri balik
- literatur diolah dulu jadi format yang bisa dibaca dan dicari (PDF ke Markdown) sebelum jadi konteks diskusi, bukan sekadar diupload mentah lalu didiskusikan
- beda dengan NotebookLM (disebut di Chapter 1): NotebookLM bagus untuk merangkum referensi, tapi terpisah dari proses menulis; di ARTi, referensi nyambung langsung ke naskah yang sedang dikerjakan
- bridging: sistem referensi menjaga apa yang dikutip tetap jujur, tapi masih ada satu lapis lagi yang menjaga proses menulisnya sendiri tetap jujur — scratchbook

## Scratchbook: File Sakti Anti Halusinasi
- definisi: tempat menampung kutipan dan catatan mentah, ditag sumbernya, belum disintesis jadi kalimat jadi
- kenapa "sakti": karena jadi satu-satunya sumber kebenaran yang wajib dirujuk sebelum menulis apapun — semua klaim di naskah harus bisa ditelusur balik ke scratchbook, bukan ke "ingatan" AI
- alur kerjanya: kutip dulu ke scratchbook + tag sumber → baru diolah jadi draft, bukan langsung minta AI menulis dari kepalanya
- jujur: langkah ini terasa lebih lambat di awal dibanding langsung minta AI menulis, tapi itu justru yang mencegah halusinasi menyelinap masuk
- penutup subchapter: gabungan memori + referensi + scratchbook inilah yang membuat kerja menulis artikel yang diiterasi berbulan-bulan tetap solid — tidak hilang ingatan, tidak halusinasi

## Fitur-fitur ARTi (ambil dari semua memory yang relevan dan landing page feature inventory)

## Perbandingan ARTi framework dengan yang lain

## Penutup
- tegaskan chapter ini baru "peta besar" ARTi Framework, bukan penjelasan lengkap tiap bagian
- komponen lain (detail tiap skill — ARTi-idea, ARTi-writing, scoring novelty, dashboard, dst) sengaja belum dibahas di sini, karena masing-masing dapat ruang sendiri di bagian Amati / Replikasi / Tambah Inovasi
- bridging ke Chapter 4: dari sini, pembahasan masuk ke tahap pertama dari alur kerja — Amati

## Cara Instalasi ARTi Framework
- VSCode dan ekstensi Claude code
- Drawio
- ArtiFrameworkSetup0.1.0.exe
- ArtiPdfSetup.exe


# Chapter 4: Amati - topik 1

dan seterusnya

# Chapter X


# BONUS

# Panduan instalasi

# Prompt Guide

# dan lainnya
