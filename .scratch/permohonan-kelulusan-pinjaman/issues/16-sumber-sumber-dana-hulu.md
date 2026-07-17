# 16 — Tentukan & Tetapkan Sumber "Sumber Dana" di Hulu

**What to build:** Memandangkan Pemohon tidak lagi memilih Sumber Dana (tiket 14), putuskan di mana ia ditetapkan dan laksanakan penetapnya, supaya setiap Permohonan memperoleh Sumber Dana tanpa input Pemohon dan laluan cabang DE/KWAPBB (ADR-0002) berfungsi hujung-ke-hujung untuk draf baharu. Dua pilihan reka bentuk yang perlu diputuskan:

- (a) **Atribut organisasi Pemohon** — SID menetapkan DE/KWAPBB semasa provision organisasi (aliran tiket 01); setiap Permohonan mewarisinya. Menambah lajur `sumber_dana` pada `pemohons` + medan pada skrin provisioning.
- (b) **Per-Permohonan oleh SID** — SID menetapkan Sumber Dana pada setiap Permohonan melalui skrin/penetap SID baharu, sebelum/semasa semakan.

**Blocked by:** None — tetapi keputusan reka bentuk perlu dibuat dahulu (lihat Status). Berkaitan: tiket 14 (paparan baca-sahaja bergantung pada nilai yang ditetapkan di sini).

**Status:** ready-for-agent

- [ ] Keputusan (a) atau (b) direkod — pertimbangkan ADR baharu memandangkan ia mengubah pemilik & masa penetapan Sumber Dana
- [ ] Sumber Dana ditetapkan di hulu tanpa sebarang input Pemohon
- [ ] Permohonan/draf baharu memperoleh Sumber Dana; laluan cabang DE/KWAPBB (ADR-0002) berfungsi hujung-ke-hujung
- [ ] Medan `ada_kementerian_pengawal` / `kementerian_pengawal_id` yang bergantung disemak semula agar konsisten dengan pendekatan yang dipilih
- [ ] Ujian melindungi penetapan Sumber Dana di hulu & routing yang terhasil

## Comments

**2026-07-17 — Keputusan reka bentuk (triage diselesaikan):** Dipilih **(a) Atribut organisasi Pemohon**, dan medan bergantung (`ada_kementerian_pengawal`, `kementerian_pengawal_id`) turut ditetapkan SID di hulu bersama Sumber Dana. Rasional: cabang routing (ADR-0002) berlaku pada masa submit, jadi nilai mesti wujud sebelum Pemohon submit; pemilikan di peringkat organisasi menjaminnya. Direkod dalam **ADR-0004** (`docs/adr/0004-sumber-dana-atribut-organisasi-pemohon.md`). Setiap Permohonan mewarisi routing daripada organisasi semasa draf dicipta (`SaveDraftPermohonan`); borang Pemohon memaparkan Sumber Dana & Kementerian Pengawal sebagai baca-sahaja sahaja.
