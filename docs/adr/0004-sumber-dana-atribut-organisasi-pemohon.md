# Sumber Dana (dan medan Kementerian Pengawal berkaitan) ialah atribut organisasi Pemohon yang ditetapkan SID

Cerita 7 memutuskan Pemohon tidak lagi memilih Sumber Dana pada Borang Permohonan — ia baca-sahaja (tiket 14). Ini menimbulkan soalan: di mana Sumber Dana ditetapkan, memandangkan cabang routing (ADR-0002) memerlukan nilainya sudah wujud pada masa Permohonan dihantar (submit)?

Kami memutuskan Sumber Dana menjadi **atribut organisasi Pemohon**, ditetapkan oleh SID semasa provision organisasi (aliran tiket 01), dan setiap Permohonan **mewarisinya semasa draf dicipta**. Medan yang bergantung padanya — `ada_kementerian_pengawal` dan `kementerian_pengawal_id` — turut menjadi atribut organisasi yang ditetapkan SID pada masa yang sama, supaya semua penentu routing berada di satu tempat dan konsisten. Borang Pemohon hanya mengendalikan butiran pinjaman (tajuk, jumlah, tujuan, tempoh) serta dokumen sokongan; penentu routing dipaparkan baca-sahaja.

Pilihan alternatif — SID menetapkan Sumber Dana per-Permohonan melalui skrin berasingan — ditolak kerana routing berlaku pada masa submit, sedangkan SID tidak menyentuh Permohonan sehingga selepas ia diterima di SID; menetapkan per-Permohonan "semasa semakan" terlalu lewat untuk cabang routing berfungsi. Pemilikan di peringkat organisasi menjamin nilai sentiasa wujud sebelum Pemohon submit.

Implikasi: cabang DE/KWAPBB (ADR-0002) terus membaca medan pada model Permohonan tanpa perubahan — hanya sumber nilai berubah (diwarisi daripada organisasi, bukan input Pemohon). Andaian: semua Permohonan bagi satu organisasi menggunakan Sumber Dana yang sama; jika keperluan masa depan memerlukan sumber berlainan per-Permohonan, keputusan ini perlu dilawati semula.
