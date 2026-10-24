# Diagram alur ARTi: skema diagram/1 dan contoh

## Skema
- `arti`: harus `"diagram/1"`.
- `direction`: `"TB"` atau `"LR"` (default `"TB"`).
- `nodes`: daftar objek `{ id, label, style }`, 1 sampai 60 item.
  - `id`: `^[a-z0-9_]+$`, unik.
  - `label`: teks 1 sampai 120 karakter. Alat membungkus teks sendiri.
  - `style`: `dark` (isi #4D4D4D, teks putih), `plain` (putih, garis hitam), `soft` (abu terang #F2F2F2, garis #4D4D4D). Bila tidak ditulis: `plain`.
- `edges`: daftar objek `{ from, to, label }`. `label` opsional, maksimal 40 karakter. `from` dan `to` harus id node yang ada.
- Tidak ada kunci lain. Tidak ada koordinat: tata letak dilakukan otomatis oleh alat.

## Kode kesalahan yang mungkin dikirim balik pengguna
E_JSON, E_MARKER, E_DIRECTION, E_NODES, E_NODES_TOO_MANY, E_NODE, E_NODE_ID, E_NODE_DUP, E_LABEL_MISSING, E_LABEL_LONG, E_STYLE, E_EDGES, E_EDGE, E_EDGE_UNKNOWN_NODE, E_EDGE_LABEL. Peringatan: W_UNKNOWN_KEY, W_EDGE_SELF, W_EDGE_LABEL_LONG, W_ORPHAN, W_BIG.

## Kapan memilih arah
- `TB`: pohon keputusan, alur penyaringan PRISMA, desain studi bercabang.
- `LR`: urutan linear pendek (4 sampai 6 langkah) seperti metode atau prosedur.

## Contoh 1: desain studi (TB)
```json
{
  "arti": "diagram/1",
  "direction": "TB",
  "nodes": [
    { "id": "pop", "label": "Populasi: 120 mahasiswa semester 3", "style": "dark" },
    { "id": "pre", "label": "Pretest", "style": "plain" },
    { "id": "kelas_a", "label": "Kelas eksperimen: pembelajaran berbasis proyek", "style": "plain" },
    { "id": "kelas_b", "label": "Kelas kontrol: pembelajaran konvensional", "style": "plain" },
    { "id": "post", "label": "Posttest", "style": "plain" },
    { "id": "uji", "label": "Analisis: uji-t sampel independen", "style": "dark" }
  ],
  "edges": [
    { "from": "pop", "to": "pre" },
    { "from": "pre", "to": "kelas_a", "label": "acak" },
    { "from": "pre", "to": "kelas_b", "label": "acak" },
    { "from": "kelas_a", "to": "post" },
    { "from": "kelas_b", "to": "post" },
    { "from": "post", "to": "uji" }
  ]
}
```

## Contoh 2: alur penyaringan PRISMA (TB, kotak "soft" untuk yang dikeluarkan)
```json
{
  "arti": "diagram/1",
  "direction": "TB",
  "nodes": [
    { "id": "ident", "label": "Identifikasi: 1.240 artikel dari 3 basis data", "style": "plain" },
    { "id": "dup", "label": "Duplikat dihapus: 310", "style": "soft" },
    { "id": "skrin", "label": "Penyaringan judul dan abstrak: 930 artikel", "style": "plain" },
    { "id": "ex1", "label": "Dikeluarkan: 780", "style": "soft" },
    { "id": "teks", "label": "Penilaian teks lengkap: 150 artikel", "style": "plain" },
    { "id": "ex2", "label": "Dikeluarkan: 98 (alasan dicatat)", "style": "soft" },
    { "id": "inc", "label": "Termasuk dalam tinjauan: 52 artikel", "style": "dark" }
  ],
  "edges": [
    { "from": "ident", "to": "dup" },
    { "from": "ident", "to": "skrin" },
    { "from": "skrin", "to": "ex1" },
    { "from": "skrin", "to": "teks" },
    { "from": "teks", "to": "ex2" },
    { "from": "teks", "to": "inc" }
  ]
}
```
Angka pada contoh hanya ilustrasi. Jangan menyalinnya; tanyakan angka sebenarnya kepada pengguna.

## Contoh 3: alur metode (LR)
```json
{
  "arti": "diagram/1",
  "direction": "LR",
  "nodes": [
    { "id": "sampel", "label": "Pengambilan sampel", "style": "plain" },
    { "id": "prep", "label": "Preparasi dan penimbangan", "style": "plain" },
    { "id": "ukur", "label": "Pengukuran", "style": "plain" },
    { "id": "olah", "label": "Pengolahan data", "style": "plain" },
    { "id": "hasil", "label": "Hasil dan interpretasi", "style": "dark" }
  ],
  "edges": [
    { "from": "sampel", "to": "prep" },
    { "from": "prep", "to": "ukur", "label": "3 ulangan" },
    { "from": "ukur", "to": "olah" },
    { "from": "olah", "to": "hasil" }
  ]
}
```
