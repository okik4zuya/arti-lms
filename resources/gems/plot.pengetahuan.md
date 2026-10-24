# Grafik ARTi: aturan Vega-Lite dan contoh

## Aturan singkat
- Vega-Lite v6, `$schema` selalu `https://vega.github.io/schema/vega-lite/v6.json`.
- Data hanya di `data.values` (array objek). Tidak ada `url`.
- Tema ARTi dipasang oleh alat: Arial, ukuran huruf sumbu 13 dan judul 14 piksel, palet kategori Okabe-Ito (biru #0072B2, oranye #E69F00, hijau #009E73, merah-oranye #D55E00, biru muda #56B4E9, ungu kemerahan #CC79A7, kuning #F0E442, hitam), latar putih. Jangan menimpa kecuali pengguna meminta.
- Pengguna mencetak grafik pada lebar 85 mm (satu kolom) atau 170 mm (dua kolom). Teks akhir harus minimal 7 pt. Grafik default (400 piksel plus sumbu) aman pada 170 mm; untuk 85 mm tulis `"width": 300` bila perlu.
- Pilih `type` sumbu: `nominal` (kategori tanpa urutan), `ordinal` (kategori berurutan, termasuk tahun sebagai teks), `quantitative` (angka), `temporal` (tanggal).
- Batang berkelompok memakai `xOffset`; batang bertumpuk memakai `color` pada `y` yang dijumlahkan.

## Kode kesalahan yang mungkin dikirim balik
E_JSON, E_SCHEMA, E_DATA_URL, E_COMPILE, E_RENDER. Peringatan: W_SCHEMA_MISSING, W_SCHEMA_VERSION, W_ROWS, W_VEGALITE, W_TEXT_SMALL.

## Contoh 1: batang per tahun
```json
{
  "$schema": "https://vega.github.io/schema/vega-lite/v6.json",
  "title": "Jumlah artikel per tahun",
  "data": { "values": [
    { "tahun": "2019", "jumlah": 14 }, { "tahun": "2020", "jumlah": 22 }, { "tahun": "2021", "jumlah": 31 },
    { "tahun": "2022", "jumlah": 47 }, { "tahun": "2023", "jumlah": 63 }, { "tahun": "2024", "jumlah": 58 }
  ] },
  "mark": "bar",
  "encoding": {
    "x": { "field": "tahun", "type": "ordinal", "title": "Tahun terbit", "axis": { "labelAngle": 0 } },
    "y": { "field": "jumlah", "type": "quantitative", "title": "Jumlah artikel" }
  }
}
```

## Contoh 2: batang berkelompok
```json
{
  "$schema": "https://vega.github.io/schema/vega-lite/v6.json",
  "data": { "values": [
    { "kelompok": "Kontrol", "tes": "Pretest", "skor": 52 }, { "kelompok": "Kontrol", "tes": "Posttest", "skor": 58 },
    { "kelompok": "Eksperimen", "tes": "Pretest", "skor": 51 }, { "kelompok": "Eksperimen", "tes": "Posttest", "skor": 74 }
  ] },
  "mark": "bar",
  "encoding": {
    "x": { "field": "kelompok", "type": "nominal", "title": null, "axis": { "labelAngle": 0 } },
    "xOffset": { "field": "tes" },
    "y": { "field": "skor", "type": "quantitative", "title": "Skor rata-rata" },
    "color": { "field": "tes", "type": "nominal", "title": null }
  }
}
```

## Contoh 3: garis dengan dua seri
```json
{
  "$schema": "https://vega.github.io/schema/vega-lite/v6.json",
  "data": { "values": [
    { "jam": 0, "seri": "Baru", "nilai": 0.0 }, { "jam": 2, "seri": "Baru", "nilai": 1.8 }, { "jam": 4, "seri": "Baru", "nilai": 3.1 },
    { "jam": 6, "seri": "Baru", "nilai": 3.9 }, { "jam": 0, "seri": "Acuan", "nilai": 0.0 }, { "jam": 2, "seri": "Acuan", "nilai": 0.9 },
    { "jam": 4, "seri": "Acuan", "nilai": 1.5 }, { "jam": 6, "seri": "Acuan", "nilai": 1.9 }
  ] },
  "mark": { "type": "line", "point": true },
  "encoding": {
    "x": { "field": "jam", "type": "quantitative", "title": "Waktu (jam)" },
    "y": { "field": "nilai", "type": "quantitative", "title": "Serapan (mg/g)" },
    "color": { "field": "seri", "type": "nominal", "title": null }
  }
}
```

## Contoh 4: sebar dengan garis regresi (hanya bila diminta)
```json
{
  "$schema": "https://vega.github.io/schema/vega-lite/v6.json",
  "data": { "values": [
    { "dosis": 10, "respons": 12 }, { "dosis": 20, "respons": 19 }, { "dosis": 30, "respons": 33 }, { "dosis": 40, "respons": 38 },
    { "dosis": 50, "respons": 51 }, { "dosis": 60, "respons": 55 }, { "dosis": 70, "respons": 69 }, { "dosis": 80, "respons": 74 }
  ] },
  "layer": [
    { "mark": "point" },
    { "mark": { "type": "line", "color": "#000000", "strokeDash": [6, 4] }, "transform": [ { "regression": "respons", "on": "dosis" } ] }
  ],
  "encoding": {
    "x": { "field": "dosis", "type": "quantitative", "title": "Dosis (mg)" },
    "y": { "field": "respons", "type": "quantitative", "title": "Respons (a.u.)" }
  }
}
```
Semua angka pada contoh hanya ilustrasi. Jangan menyalinnya; pakai data pengguna.
