ide untuk narasi di ebook dan landing page ( mungkin juga untuk penyampaian selama workshop)

Saya tidak tahu ini akan masuk bagian mana, tapi tolong disimpan saja dulu.

## Argumen — penggunaan AI yang wajar vs "bar-bar"

"Menurut saya penggunaan AI sah-sah saja, malah dengan cara ARTi framework itulah yang seharusnya. Karena di ARTi framework, semua ide dari kita, kita kumpulkan, kita berikan list perintah apa saja yang harus dilakukan. kalau dianalogikan mungkin mirip seperti  kita membuat todo list yang harus dilakukan, lalu meminta mahasiswa untuk mengerjakan. Itu kan praktik yang selama ini sudah terjadi, bahkan sebelum ada AI, dan itu sudah sangat wajar di dunia dosen.
Justru, yang salah adalah penggunaan AI yang bar-bar. Maksudnya, kita memberikan perintah dengan konteks yang minim, menyerahkan semua pengambilan keputusan kepada AI, lalu kita menerima hasilnya yang belum terjamin kebenarannya, bahkan kebanyakan ngawur, atau halusinasi. Dengan cara ini, maka perannya jadi terbalik, AI yang jadi pemilik ide, malah kita yang disuruh AI untuk melakukan sesuatu. 
Agar ketajaman berfikir kita tetap terjaga, saya sangat menyarankan agar pola penggunaannya seperti pola pertama (yang diadopsi oleh ARTi framework). Kita harus siap memberikan informasi yang dibutuhkan sebagai konteks dalam diskusi. Misalnya, memberikan outline mentah dari kita, fulltext artikel baik sebagai referensi maupun contoh artikel untuk membuat jurnal profil misalnya, atau informasi lain yang diperlukan untuk membuat konteks yang sangat jelas dan detail.
Mengenai pengumpulan bahan dan informasi untuk konteks diskusi, mungkin di antara kita ada yang berfikir, itu kan sama saja seperti kita yang dikerjain AI, kita disuruh mencari referensi, menyiapkan fulltext paper, dsb. Ok kalau dilihat sekilas, memang seperti itu kelihatannya. Tapi kita harus bisa membedakan, mana tugas yang perlu "expertise" dan tugas yang bisa dikerjakan tanpa "expertise". Mumpung kita berbicara ini, saya ingin menegaskan sekaligus mengingatkan Bapak/Ibu dosen, bahwa kita adalah "expert", lebih expert dari AI yang kita pakai. Maka jangan biarkan pengambilan keputusan yang membutuhkan "expertise" itu kita serahkan kepada yang tidak kompeten, yang saya maksud adalah AI itu sendiri. Atau kalau Bapak/Ibu tidak setuju dengan pernyataan saya, mungkin saya akan bilang bahwa "jangan biarkan pengambilan keputusan yang membutuhkan expertise itu kita serahkan kepada yang kompetensinya masih diragukan, dalam hal ini AI".
Sesungguhnya saya ingin mengatakan bahwa, jika kita serahkan sepenuhnya kepada AI, maka hancurlah dunia pendidikan kita. Apa fungsi kita sebagai dosen? yang seharusnya membagikan ilmu dan memberikan pendidikan.
Baik, kembali kepada topik. Bahwa kita harus membedakan mana yang butuh expertise dan mana yang tidak. Menurut saya, membuat outline dan menentukan referensi mana yang harus dipilih untuk dijadikan dasar state of the art, adalah tugas yang membutuhkan expertise. Membutuhkan pengalaman dalam domain tertentu yang hanya diketahui oleh ahli yang memang berkecimpung di dunia tersebut, yaitu Bapak/Ibu, bukan AI.
Adapun tugas seperti 
- mensintesis sebuah paragraf dari informasi kunci dan outline yang tersedia, 
- mebuat teks analisis yang koheren berdasarkan poin-poin analisis yang tidak terstruktur
- membuat gambar dari data yang diberikan dengan aturan-aturan tertentu
- menyempurnakan tata bahasa
- dan tugas lainnya
itu adalah tugas yang bisa dilakukan oleh siapapun, tanpa expertise di bidang khusus."

Bagian lain:

## Analogi — Lucy / 50 First Dates dan memory system

"Sederhananya, saya menganalogikan AI atau LLM adalah seperti seorang penderita alzheimer. Yaitu orang yang hanya mengingat ingatan lama saja, adapun dia tidak ingat apa2 mengenai apa yang terjadi dalam waktu terbaru, misal lupa apapun yang terjadi 1 tahun terakhir sampai hari ini. Apakah anda pernah menonton sebuah film berjudul "50 first dates"? Film ini tentang sepasang kekasih yang istrinya (Lucy) menderita penyakit alzheimer. Setiap bangun pagi, dia menonton video yang disiapkan suaminya mengenai apa yang telah terjadi padanya, termasuk pernikahannya, orang-orang terdekatnya, dan semua momentum penting yang bisa membantu mengingat tentang dirinya.

[Cuplikan video ending 50 first dates]

Jika dianalogikan dengan AI, maka AI itu seperti Lucy, dia selalu ingat semua informasi dan sejarah tentang dirinya sampai waktu tertentu, dalam hal ini dia ingat semua informasi dan data yang telah dilatih kepadanya. Namun, sesi apapun setelah itu dia selalu lupa. ketika kita membuat sesi baru, maka AI akan selalu mengulang dari awal ingatannya. Nah disinilah kita perlu memberi konteks atau ingatan terbaru agar kita mudah untuk melanjutkan diskusi. Dalam kasus Lucy, pemberian konteks itu melalui media video yang disiapkan suaminya, sehingga setelah menonton video tersebut, lucy tahu bahwa orang yang ada di kapal adalah suaminya, anaknya, dan bapaknya, sehingga tidak merasa kaget. Adapun untuk kasus penggunaan LLM, maka kita perlu membuat konteks mengenai obrolan kita yang terakhir agar jawaban selanjutnya selalu sesuai dengan perkembangan obrolan.
Dalam praktek penggunaan LLMs berbasis chat seperti chatGPT dan Gemini, bisa dibilang kita tidak bisa mentransfer konteks dari sesi satu ke sesi lainnya. Meskipun ada fitur memory, kita tidak bisa menggunakannya secara fleksibel dan terisolasi di setiap project yang kita kerjakan. Ini adalah salah satu hal yang mendorong saya untuk membangun ARTi framework.
Untuk memberikan konteks, maka dalam ARTi framework, kami membangun memory system yang dirancang sedemikian rupa sehingga model AI yang akan selalu ingat status terakhir dari diskusi. Misalnya, ketika kita bekerja dengan penulisan introduction, memory akan memberikan informasi mengenai berapa paragraf yang sudah ditulis, sudah berapa kali revisi, preferensi user apa saja, masukan dan permintaan sebelumnya, dan lain sebagainya. Dengan demikian, model AI tidak akan mengulang kembali pekerjaan pada sesi yang baru."
