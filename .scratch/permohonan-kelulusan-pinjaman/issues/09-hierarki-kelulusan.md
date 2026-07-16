# 09 — Hierarki Kelulusan

**What to build:** Memo Pertimbangan (tiket 08) dihantar melalui rantaian hierarki kelulusan secara berurutan: PSID → KSID → KKID → TSID → SSID → TKSP(I) → KSP → YB MK. Setiap peringkat boleh Endorse & Hantar ke peringkat seterusnya, ATAU Pulangkan untuk Pembetulan ke peringkat sebelumnya (ADR-0003). YB MK membuat Keputusan Kelulusan akhir (Lulus/Tidak Lulus).

**Blocked by:** 08 — Rundingan, Traffic Light 31 Mac & Memo Pertimbangan

**Status:** ready-for-agent

- [ ] Memo Pertimbangan bergerak melalui peringkat hierarki secara berurutan mengikut susunan PSID → KSID → KKID → TSID → SSID → TKSP(I) → KSP → YB MK
- [ ] Pegawai di setiap peringkat (KSID, KKID, TSID, SSID, TKSP(I), KSP) boleh Endorse & Hantar Memo ke peringkat seterusnya
- [ ] Pegawai di setiap peringkat (bukan hanya di hujung) boleh Pulangkan Memo untuk Pembetulan ke peringkat SEBELUMNYA, berserta sebab pemulangan
- [ ] Apabila Memo dipulangkan, pegawai di peringkat yang menerima pemulangan (contoh: PSID) menerima notifikasi berserta sebab
- [ ] YB MK boleh buat Keputusan Kelulusan (Lulus / Tidak Lulus) ke atas Memo yang sampai ke peringkat YB MK
- [ ] Apabila Keputusan = Tidak Lulus, Permohonan kembali ke status "Dalam Rundingan" (tiket 08)
- [ ] Apabila Keputusan = Lulus, Permohonan bersedia untuk penjanaan Surat Tawaran (tiket 10)
- [ ] Hanya pegawai peringkat berkaitan (mengikut kedudukan Memo semasa) boleh bertindak; peranan lain ditolak
- [ ] Pegawai SID/audit boleh lihat log sejarah penuh pergerakan Memo (endorse, pulang balik, sebab) bagi satu Permohonan
