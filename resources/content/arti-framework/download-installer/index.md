---
title: "Download ARTi Framework"
order: 1
published: true
---

<!--
Setiap rilis baru, ubah empat hal bersama-sama setelah installer diunggah ke R2:
versi, tanggal, tautan unduhan, ukuran, dan SHA-256 (blok "Berkas" di bawah).
Tautan halaman ini juga dipasang di rilis lewat publish_release.py --download-page.
-->

<style>
@import url('https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap');
.content {
    max-width: 600px;
    font-family: 'Atkinson Hyperlegible', var(--font-sans);
}
.content h1 { font-size: 1.85rem; }
.content h2 { font-size: 1.5rem; margin-top: 2em; }
.content p, .content li {
    font-size: 1.2rem;
    line-height: 1.75;
    margin: 0 0 0.9em;
}
</style>

# Download ARTi Framework

Di halaman ini ada installer ARTi Framework versi terbaru, cara memasangnya, dan cara memastikan berkas yang Anda unduh utuh. Halaman ini hanya bisa dibuka setelah Anda masuk ke akun belajar, tetapi tautan unduhannya sendiri bisa dipakai dari mana saja. Jangan bagikan tautannya ke orang lain; yang Anda bayar adalah lisensinya, dan lisensi itu terikat pada key Anda.

## Berkas

**ARTi Framework 0.1.9** (18 Oktober 2026), untuk Windows 10 dan 11

[Unduh ArtiFrameworkSetup-0.1.9.exe (sekitar 120 MB)](https://cdn.arti.ai.id/installers/ArtiFrameworkSetup-0.1.9.exe)

SHA-256:

```
2CB480BEC124BE2D84628840F0BBCBB9DF3EF0B7DD1AAD5B85D8461C4F927A97
```

Untuk mencocokkannya, buka PowerShell di folder tempat berkas tersimpan lalu jalankan:

```
Get-FileHash .\ArtiFrameworkSetup-0.1.9.exe -Algorithm SHA256
```

Hasilnya harus sama persis dengan angka di atas. Kalau berbeda, unduh ulang dan jangan jalankan berkas itu.

## Sebelum mulai

- **Key lisensi.** Anda menerimanya setelah pembelian. Installer meminta key itu di halaman terakhir sebelum memasang apa pun, dan memeriksanya lewat internet.
- **Internet.** Perlu sekali saat pemasangan pertama, untuk memeriksa key. Kalau komputer belum punya komponen WebView2 dari Microsoft (jendela dashboard memakainya), installer mengunduhnya sendiri, sekitar 150 MB. Di koneksi lambat ini bisa memakan beberapa menit.
- **Ruang.** Sekitar 600 MB di folder `.arti` milik akun Windows Anda. Tidak perlu hak administrator.
- **Windows.** Installer ini khusus Windows 10 (versi 1803 ke atas) dan Windows 11.

## Memasang

1. Jalankan `ArtiFrameworkSetup-0.1.9.exe`. Kalau Windows SmartScreen memperingatkan penerbit yang tidak dikenal, pilih **More info**, lalu **Run anyway**. Installer ini belum ditandatangani secara digital, itu sebabnya peringatan itu muncul.
2. Setujui lisensi, biarkan folder tujuan di `.arti` (default), lalu ketik key lisensi Anda.
3. Tunggu sampai selesai, sekitar 5 menit. Setelah itu klik **Finish**.
4. Buka Claude Code dan ketik "set up ARTi" untuk membuat profil peneliti Anda.

Kalau jendela dashboard belum terbuka, tunggu beberapa menit lalu buka **ARTi Framework** dari shortcut di Desktop. Selama komponen WebView2 masih diunduh, dashboard bisa terbuka di tab browser dulu, dan akan berpindah ke jendela sendiri saat sudah siap.

## Memperbarui dari versi lama

Pasang versi baru **di atas** yang lama, dengan installer yang sama. Jangan uninstall dulu: uninstall menghapus seluruh folder `.arti`, termasuk memori dan profil Anda.

Sebelum menjalankan installer, tutup dashboard ARTi, semua sesi Claude Code, dan jendela terminal atau File Explorer yang sedang terbuka di dalam folder `.arti`. Kalau ada yang masih dipakai, installer akan berhenti dengan pesan dan tidak mengubah apa-apa; tutup yang disebut, lalu jalankan lagi. Key lisensi tidak diminta lagi. Memori, profil, key, dan data penelitian Anda tidak disentuh. Skill, tool, dan dashboard diganti dengan versi baru secara utuh, jadi perubahan yang Anda buat sendiri di dalamnya ikut tertimpa; simpan catatan pribadi di `memory`, bukan di sana.

## Yang baru di 0.1.9

- Skill baru, ARTi-demo: satu perintah yang menjalankan contoh lengkap dari pertanyaan sampai naskah `.docx`.
- Pencarian literatur sekarang berupa tool baris perintah, bukan server MCP. Kalau Anda pernah menambahkan `arti-ref-search-mcp` ke Claude Code, hapus entri itu.
- Data jurnal Scimago dipisah dari catatan jurnal Anda, jadi pembaruan data tidak menimpa catatan itu.
- Installer lebih tegas: key diperiksa sebelum apa pun ditulis, dan pembaruan menolak berjalan kalau ada yang masih terpakai.
- Dashboard memasang WebView2 sendiri bila belum ada, dan menampilkan statusnya di sisi kiri.

Gambaran umum komponen yang terpasang ada di [[Cara Instalasi ARTi Framework]].
