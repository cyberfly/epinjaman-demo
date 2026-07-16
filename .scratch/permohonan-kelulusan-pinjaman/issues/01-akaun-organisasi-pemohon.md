# 01 — Akaun & Organisasi Pemohon

**What to build:** SID mencipta akaun organisasi Pemohon (bukan pendaftaran layan diri) dengan satu atau lebih pengguna yang bertindak bagi pihaknya. Pengguna baharu menerima e-mel pengesahan pendaftaran akaun dengan pautan sah 24 jam, mengaktifkan akaun, dan log masuk. Asas peranan/kebenaran (Pemohon, Kementerian Pengawal, PSID, KSID, KKID, TSID, SSID, TKSP(I), KSP, YB MK, BUU, Admin) ditubuhkan untuk digunakan oleh semua tiket seterusnya.

**Blocked by:** None — can start immediately

**Status:** ready-for-agent

- [ ] Pegawai SID (atau admin) boleh cipta organisasi Pemohon baharu dalam sistem
- [ ] Pegawai SID boleh tambah lebih daripada satu pengguna kepada satu organisasi Pemohon yang sama
- [ ] Pengguna Pemohon yang baharu didaftarkan menerima e-mel pengesahan pendaftaran akaun dengan pautan sah 24 jam
- [ ] Pautan pengesahan akaun tamat tempoh selepas 24 jam dan tidak boleh digunakan lagi
- [ ] Fortify `Features::registration()` (aliran daftar-sendiri terbuka) dinyahaktifkan/disekat untuk pengguna luar; akaun hanya wujud melalui provisioning SID
- [ ] Satu organisasi Pemohon boleh mempunyai berbilang Permohonan berkaitan dengannya sepanjang masa (struktur data sedia untuk ini walaupun skrin Permohonan belum wujud lagi)
- [ ] Peranan/kebenaran asas (roles) untuk semua peringkat (Pemohon, Kementerian Pengawal, hierarki SID, hierarki Perbendaharaan, BUU, Admin) wujud dan boleh diberikan kepada pengguna
- [ ] Pengguna log masuk berjaya dan dilayan mengikut peranannya (contoh: papan pemuka kosong buat masa ini, tiada ralat)
