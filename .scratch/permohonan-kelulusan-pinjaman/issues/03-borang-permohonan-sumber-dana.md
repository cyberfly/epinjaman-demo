# 03 — Borang Permohonan & Cabang Sumber Dana

**What to build:** Pemohon mengisi Borang Permohonan Pinjaman berstruktur, memuat naik dokumen sokongan mengikut senarai semak (Lampiran 6), dan menghantarnya. Sistem menyemak Sumber Dana (DE/KWAPBB) dan kewujudan Kementerian Pengawal, lalu menentukan laluan seterusnya mengikut ADR-0002 (KWAPBB tanpa Kementerian Pengawal melangkau semakan kelengkapan & tandatangan Peringkat 1).

**Blocked by:** 01 — Akaun & Organisasi Pemohon, 02 — Repositori Rujukan Admin

**Status:** ready-for-agent

- [ ] Model `Permohonan` (aggregate root) wujud dengan medan berstruktur (jumlah dipohon, tujuan, tempoh, Sumber Dana, dan medan asas lain) dan status fasa kasar
- [ ] Pemohon boleh isi & simpan Borang Permohonan sebagai draf sebelum menghantar
- [ ] Pemohon boleh muat naik dokumen sokongan mengikut senarai semak Lampiran 6 (daripada tiket 02)
- [ ] Sistem menyemak Sumber Dana yang dipilih (DE atau KWAPBB)
- [ ] Apabila Sumber Dana = KWAPBB, sistem menyemak sama ada terdapat Kementerian Pengawal berkaitan
- [ ] Apabila Sumber Dana = DE, atau KWAPBB dengan Kementerian Pengawal = Ya: Permohonan yang dihantar diberi status "menunggu semakan kelengkapan & tandatangan Peringkat 1"
- [ ] Apabila Sumber Dana = KWAPBB dengan Kementerian Pengawal = Tidak: Permohonan yang dihantar terus diberi status "diterima SID" (langkau semakan kelengkapan & tandatangan Peringkat 1), mengikut ADR-0002
- [ ] Pemohon tidak boleh hantar borang tanpa medan wajib diisi
- [ ] Hanya pengguna organisasi Pemohon berkaitan boleh lihat/edit Permohonan tersebut sebelum dihantar
