# 14 — Sumber Dana Baca-Sahaja pada Borang Permohonan

**What to build:** Pemohon tidak lagi boleh *memilih* Sumber Dana pada Borang Permohonan (spec cerita 7). Medan Sumber Dana dipaparkan sebagai baca-sahaja daripada nilai sedia ada pada Permohonan, dan digugurkan daripada laluan tulis borang Pemohon. Laluan cabang DE/KWAPBB (ADR-0002) terus berfungsi menggunakan nilai yang telah ditetapkan.

**Blocked by:** None — can start immediately (dibina atas tiket 03 yang telah siap). Berkaitan: tiket 16 menetapkan sumber nilai Sumber Dana di hulu.

**Status:** ready-for-agent

- [ ] Borang Permohonan memaparkan Sumber Dana sebagai medan baca-sahaja — bukan `<flux:select wire:model.live="sumber_dana">` yang boleh dipilih
- [ ] `sumber_dana` digugurkan daripada laluan tulis borang Pemohon (formData/saveDraft/submit) — percubaan Pemohon menetapkannya diabaikan/ditolak
- [ ] Nilai Sumber Dana yang dipaparkan diambil daripada Permohonan sedia ada; nilai null dipaparkan sebagai '—' atau setara
- [ ] Suis `ada_kementerian_pengawal` & pilihan Kementerian Pengawal yang bergantung pada Sumber Dana terus memaparkan/menyembunyi berdasarkan nilai tersimpan (bukan pilihan langsung Pemohon)
- [ ] Logik cabang KWAPBB + Kementerian Pengawal & tandatangan Peringkat 1 (ADR-0002) terus membaca nilai tersimpan tanpa perubahan tingkah laku
- [ ] `PermohonanBorangScreenTest` dikemas kini: Sumber Dana dipapar baca-sahaja; select boleh-pilih tiada; input `sumber_dana` daripada Pemohon tidak mengubah rekod
- [ ] Nota skop: sehingga tiket 16 mendarat, draf baharu tiada Sumber Dana melainkan diseed — aliran hujung-ke-hujung untuk draf baharu bergantung pada tiket 16
