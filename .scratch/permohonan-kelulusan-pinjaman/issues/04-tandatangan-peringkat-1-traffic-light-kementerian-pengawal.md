# 04 — Tandatangan Digital Peringkat 1, Enjin Traffic Light & Hantar ke Kementerian Pengawal

**What to build:** Untuk Permohonan yang berstatus "menunggu semakan kelengkapan & tandatangan Peringkat 1" (tiket 03), sistem menyemak kelengkapan borang, Pemohon menurunkan Tandatangan Digital Peringkat 1 (rekod audit dalaman, ADR-0001), dan Permohonan dihantar ke Kementerian Pengawal. Enjin Traffic Light generik dibina di sini (dipakai buat kali pertama untuk had 31 Januari) dan dipakai semula oleh tiket-tiket seterusnya.

**Blocked by:** 03 — Borang Permohonan & Cabang Sumber Dana

**Status:** ready-for-agent

- [ ] Sistem menyemak kelengkapan borang & dokumen; jika tidak lengkap, Permohonan dipulangkan kepada Pemohon dengan penunjuk medan/dokumen yang tiada
- [ ] Pemohon (pengguna organisasi Pemohon yang diberi kuasa) boleh turunkan Tandatangan Digital Peringkat 1 ("Disediakan" & "Disahkan") ke atas borang yang lengkap
- [ ] Tandatangan direkod sebagai tindakan diaudit (pengguna, peranan, kenyataan, cap masa) — bukan integrasi e-tandatangan pihak ketiga
- [ ] Selepas tandatangan, Permohonan dihantar ke Kementerian Pengawal berkaitan dan notifikasi dicetuskan
- [ ] Enjin Traffic Light generik wujud: menerima satu tarikh had & satu Permohonan, mengembalikan status Normal/Kuning/Merah
- [ ] Job berjadual harian menyemak Permohonan aktif terhadap had 31 Januari (peringkat Semakan), mengemas kini status Traffic Light, dan menghantar notifikasi (e-mel + dalam sistem) apabila status bertukar Kuning atau Merah
- [ ] UI memaparkan penunjuk Traffic Light (Kuning: peringatan menghampiri had; Merah: notifikasi melepasi had) pada Permohonan berkenaan
