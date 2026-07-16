# 06 — Semakan Senarai Semak Dokumen (PSID)

**What to build:** PSID menyemak Senarai Semak & Dokumen Permohonan (Lampiran 6) bagi Permohonan yang sampai ke SID — sama ada melalui laluan Kementerian Pengawal (tiket 05) atau laluan langkau KWAPBB-tanpa-Kementerian-Pengawal (tiket 03). Apabila dokumen lengkap & teratur, Permohonan diteruskan ke peringkat Rundingan; jika tidak, ditandakan untuk Kuiri (tiket 07).

**Blocked by:** 05 — Tandatangan Digital Peringkat 2 & Penerimaan di SID (dan 03, untuk laluan langkau KWAPBB-tanpa-Kementerian-Pengawal)

**Status:** ready-for-agent

- [ ] PSID melihat Permohonan dalam Tray Tugasan dan boleh buka senarai semak dokumen (Lampiran 6, daripada tiket 02) untuk Permohonan tersebut
- [ ] PSID boleh tandakan setiap item senarai semak sebagai lengkap/tidak lengkap & teratur/tidak teratur
- [ ] Apabila PSID mengesahkan dokumen lengkap & teratur, Permohonan diteruskan ke status "Dalam Rundingan"
- [ ] Apabila PSID mengesahkan dokumen tidak lengkap/tidak teratur, Permohonan ditandakan memerlukan Kuiri (sedia untuk tiket 07)
- [ ] Laluan langkau KWAPBB-tanpa-Kementerian-Pengawal (status "diterima SID" terus daripada tiket 03) turut muncul dalam Tray Tugasan PSID dan boleh disemak sama seperti laluan biasa
- [ ] Hanya peranan PSID boleh bertindak pada peringkat semakan ini; peranan lain ditolak
