# 02 — Repositori Rujukan Admin (Lampiran 6 & Lampiran 8)

**What to build:** Pentadbir sistem mengurus (CRUD) senarai semak dokumen Permohonan (Repositori Lampiran 6) dan templat notifikasi (Repositori Lampiran 8) melalui skrin admin, tanpa perlu perubahan kod. Kandungan ini digunakan oleh tiket-tiket seterusnya (muat naik dokumen, semakan kelengkapan, e-mel notifikasi).

**Blocked by:** 01 — Akaun & Organisasi Pemohon

**Status:** ready-for-agent

- [ ] Admin boleh cipta, kemas kini, dan padam item senarai semak dokumen (Lampiran 6) — setiap item ada label & huraian
- [ ] Admin boleh cipta, kemas kini, dan padam templat notifikasi (Lampiran 8) — setiap templat ada pencetus (event), tajuk, dan kandungan
- [ ] Hanya peranan Admin boleh akses skrin CRUD ini; peranan lain ditolak
- [ ] Senarai semak & templat notifikasi yang disimpan boleh diambil semula oleh sistem (API/query dalaman) untuk digunakan pada tiket seterusnya
- [ ] Sistem hantar notifikasi melalui saluran mel + pangkalan data (Laravel Notification) menggunakan templat tersimpan
