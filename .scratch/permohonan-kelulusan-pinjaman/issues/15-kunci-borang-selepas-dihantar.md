# 15 — Kunci Borang Permohonan Selepas Dihantar

**What to build:** Sebaik sahaja Permohonan meninggalkan status Draf, Borang Permohonan menyembunyikan butang Simpan Draf & Hantar dan memaparkan notis "telah dihantar / baca-sahaja" (status semasa dipapar); sebarang panggilan hantar semula ditolak (403). Perubahan ini telah dilaksanakan dalam working tree — tiket ini merasmikan kriteria penerimaannya; kerja yang tinggal ialah mengesahkan & commit.

**Blocked by:** None — can start immediately (dibina atas tiket 03 yang telah siap).

**Status:** ready-for-agent

- [ ] Apabila Permohonan bukan Draf, butang `save-draft-button` & `submit-permohonan-button` tidak dipaparkan pada borang
- [ ] Apabila Permohonan bukan Draf, notis baca-sahaja "Permohonan telah dihantar" dipaparkan berserta status semasa
- [ ] Apabila Permohonan masih Draf, butang Simpan Draf & Hantar dipaparkan seperti biasa
- [ ] Panggilan `submit` ke atas Permohonan bukan-Draf ditolak (403)
- [ ] Ujian melindungi ketiga-tiga keadaan: bukan-Draf sembunyi butang + papar notis; Draf papar butang; `submit` bukan-Draf → 403
