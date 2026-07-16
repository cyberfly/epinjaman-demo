Status: ready-for-agent

# Sistem ePinjaman — Permohonan & Kelulusan Pinjaman

## Problem Statement

Bahagian Pelaburan Strategik (SID) memproses permohonan pinjaman kerajaan (agensi kerajaan/GLC memohon pinjaman daripada Kerajaan Persekutuan) secara manual/separa-manual merentasi banyak peringkat: pengesahan Kementerian Pengawal, semakan dokumen, rundingan terma, hierarki kelulusan sehingga Menteri Kewangan, penyediaan surat tawaran & perjanjian, sehingga pengesahan Syarat Duluan. Proses ini melibatkan lebih 10 peranan berbeza, tarikh akhir (SLA) berbilang peringkat yang mudah terlepas pandang tanpa amaran proaktif, dan pertukaran dokumen/maklumat (kuiri) yang sukar dijejaki apabila dilakukan di luar satu sistem berpusat. Pemohon, Kementerian Pengawal, dan pegawai dalaman SID/Perbendaharaan tiada satu tempat berpusat untuk menjejak status permohonan, bertindak ke atas tugasan mereka, dan menerima amaran tarikh akhir tepat pada masanya.

## Solution

Bina sistem ePinjaman berasaskan Laravel/Livewire yang memodelkan keseluruhan kitaran hayat Permohonan (dari SID mencipta akses log masuk Pemohon sehingga Syarat Duluan (CP) disahkan dan Permohonan dikunci LENGKAP) sebagai satu aliran kerja berstatus dalam sistem. Setiap peranan (Pemohon, Kementerian Pengawal, hierarki pegawai SID, hierarki kelulusan Perbendaharaan, BUU) log masuk ke sistem yang sama, melihat Tray Tugasan mereka sendiri, dan bertindak ke atas Permohonan mengikut kebenaran peranan masing-masing. Sistem mengira & memaparkan penunjuk Traffic Light (Kuning/Merah) secara proaktif melalui job berjadual harian, menghantar notifikasi e-mel & dalam sistem apabila tarikh akhir menghampiri/terlepas, dan menyimpan rekod audit bagi setiap "tandatangan digital" serta setiap peralihan status.

## User Stories

### Pemohon & Akaun

1. Sebagai pegawai SID, saya mahu cipta akaun organisasi Pemohon (bukan pendaftaran layan diri), supaya akses sistem hanya diberi kepada organisasi yang sah.
2. Sebagai pegawai SID, saya mahu tambah lebih daripada satu pengguna kepada satu organisasi Pemohon, supaya beberapa pegawai organisasi tersebut boleh bertindak bagi pihak organisasi yang sama.
3. Sebagai pengguna Pemohon yang baharu didaftarkan, saya mahu terima e-mel pengesahan pendaftaran akaun dengan pautan sah 24 jam, supaya saya boleh aktifkan akaun saya dengan selamat.
4. Sebagai pengguna Pemohon, saya mahu pautan pengesahan akaun tamat tempoh selepas 24 jam, supaya pautan lama tidak boleh disalahguna.
5. Sebagai organisasi Pemohon, saya mahu boleh mengemukakan lebih daripada satu Permohonan dari semasa ke semasa menggunakan akaun sedia ada, supaya saya tidak perlu daftar semula setiap kali memohon.
6. Sebagai pegawai SID, saya mahu Permohonan-Permohonan lepas & semasa bagi satu organisasi Pemohon dipaparkan bersama, supaya sejarah permohonan organisasi tersebut mudah disemak.

### Borang Permohonan

7. Sebagai Pemohon, saya mahu isi Borang Permohonan Pinjaman berstruktur (medan seperti jumlah dipohon, tujuan, tempoh, sumber dana, dan medan lain mengikut Garis Panduan ms 42-49), supaya maklumat permohonan tersimpan secara sistematik dan boleh disemak/dilaporkan.
8. Sebagai Pemohon, saya mahu muat naik dokumen sokongan mengikut senarai semak Repositori Lampiran 6, supaya dokumen lengkap dikemukakan bersama borang.
9. Sebagai Pemohon, saya mahu simpan borang sebagai draf sebelum menghantar, supaya saya boleh sambung kerja lain kali tanpa kehilangan input.
10. Sebagai sistem, saya mahu semak Sumber Dana yang dipilih Pemohon (DE atau KWAPBB), supaya laluan semakan yang betul digunakan.
11. Sebagai sistem, apabila Sumber Dana = DE, saya mahu Permohonan terus melalui semakan kelengkapan borang & tandatangan digital Peringkat 1 Pemohon sebelum dihantar ke Kementerian Pengawal.
12. Sebagai sistem, apabila Sumber Dana = KWAPBB, saya mahu semak sama ada terdapat Kementerian Pengawal berkaitan.
13. Sebagai sistem, apabila Sumber Dana = KWAPBB dan Ada Kementerian Pengawal = Ya, saya mahu Permohonan melalui semakan kelengkapan borang & tandatangan digital Peringkat 1 Pemohon, sama seperti laluan DE (lihat ADR-0002).
14. Sebagai sistem, apabila Sumber Dana = KWAPBB dan Ada Kementerian Pengawal = Tidak, saya mahu Permohonan dihantar terus ke SID tanpa semakan kelengkapan borang atau tandatangan digital Peringkat 1 (mengikut carta proses rasmi, lihat ADR-0002).
15. Sebagai sistem, apabila borang tidak lengkap semasa semakan kelengkapan, saya mahu pulangkan Permohonan kepada Pemohon dengan penunjuk medan/dokumen yang tiada, supaya Pemohon boleh membetulkannya.

### Tandatangan Digital & Penghantaran ke Kementerian Pengawal

16. Sebagai Pemohon, saya mahu turunkan Tandatangan Digital Peringkat 1 ('Disediakan' & 'Disahkan') ke atas borang yang lengkap, supaya Permohonan boleh diteruskan.
17. Sebagai sistem, saya mahu rekod Tandatangan Digital sebagai tindakan diaudit (pengguna, peranan, kenyataan, cap masa) dan bukan integrasi e-tandatangan pihak ketiga untuk v1 (lihat ADR-0001).
18. Sebagai sistem, saya mahu hanya pegawai yang diberi kuasa (mengikut peranan Pemohon) boleh turunkan Tandatangan Digital Peringkat 1, supaya integriti tindakan terjamin.
19. Sebagai sistem, selepas Tandatangan Digital Peringkat 1, saya mahu hantar Permohonan ke Kementerian Pengawal berkaitan (jika berkenaan) dan cetuskan notifikasi, supaya Kementerian Pengawal dimaklumkan.
20. Sebagai pegawai Kementerian Pengawal, saya mahu lihat Permohonan yang dihantar kepada kementerian saya dalam Tray Tugasan saya, supaya saya tahu tindakan yang perlu diambil.
21. Sebagai sistem, saya mahu kira & papar Traffic Light peringkat Semakan (Kuning bila menghampiri 31 Januari, Merah bila melepasi 31 Januari), supaya Kementerian Pengawal & Pemohon dimaklumkan tentang tarikh akhir.
22. Sebagai pegawai Kementerian Pengawal, saya mahu turunkan Tandatangan Digital Peringkat 2 ('Disemak' & 'Diperaku'), supaya Permohonan boleh diteruskan ke SID tidak kira status Traffic Light.
23. Sebagai sistem, selepas Tandatangan Digital Peringkat 2 (atau selepas laluan langkau KWAPBB-tanpa-Kementerian-Pengawal), saya mahu hantar Permohonan ke SID.

### Penerimaan & Semakan Dokumen di SID

24. Sebagai sistem, apabila Permohonan diterima SID, saya mahu jana No. Rujukan unik dan hantar e-mel penerimaan (Status: Dalam Proses Semakan) kepada Pemohon, supaya Pemohon ada bukti rujukan.
25. Sebagai sistem, saya mahu letakkan Permohonan dalam Tray Tugasan PSID (peringkat operasi SID), supaya proses semakan dokumen bermula.
26. Sebagai PSID, saya mahu semak Senarai Semak & Dokumen Permohonan berdasarkan Repositori Lampiran 6 (ms 47-48), supaya kelengkapan & keteraturan dokumen dapat disahkan.
27. Sebagai PSID, apabila dokumen lengkap & teratur, saya mahu teruskan Permohonan ke peringkat Rundingan (R) bersama Pemohon.
28. Sebagai PSID, apabila dokumen tidak lengkap/tidak teratur, saya mahu cetuskan satu atau lebih item Kuiri terhadap Permohonan tersebut.

### Modul Kuiri

29. Sebagai PSID, saya mahu cipta item Kuiri berasingan bagi setiap isu/dokumen spesifik yang bermasalah, supaya setiap isu boleh dijejak & diselesaikan secara berasingan.
30. Sebagai sistem, saya mahu hantar e-mel notifikasi Kuiri (kepada Pemohon dan/atau Kementerian Pengawal, mengikut siapa relevan) yang mengandungi sebab Kuiri, pautan ke Tray Tindakan, dan tarikh akhir balasan (7 hari).
31. Sebagai Pemohon atau pegawai Kementerian Pengawal, saya mahu balas item Kuiri (dengan mesej dan/atau muat naik dokumen tambahan) dalam thread Kuiri berkenaan.
32. Sebagai PSID, saya mahu tandakan satu item Kuiri sebagai 'Berpuas Hati' atau 'Tidak Berpuas Hati' selepas menyemak balasan.
33. Sebagai sistem, apabila satu item Kuiri ditandakan 'Tidak Berpuas Hati', saya mahu kekalkan item tersebut terbuka untuk pusingan balasan seterusnya.
34. Sebagai sistem, apabila SEMUA item Kuiri aktif bagi satu Permohonan ditandakan 'Berpuas Hati', saya mahu benarkan Permohonan diteruskan ke peringkat Rundingan (R).
35. Sebagai PSID, saya mahu lihat senarai semua item Kuiri (terbuka & selesai) bagi satu Permohonan dalam satu paparan, supaya sejarah kuiri mudah disemak.

### Rundingan & Memo Pertimbangan

36. Sebagai SID dan Pemohon, saya mahu jalankan sesi rundingan & rekod persetujuan terma & syarat pinjaman dalam sistem, supaya terma yang dipersetujui didokumenkan.
37. Sebagai sistem, saya mahu kira & papar Traffic Light peringkat Rundingan (Kuning bila menghampiri 31 Mac, Merah bila melepasi 31 Mac dengan mesej 'Sila Mohon Pelanjutan'), supaya SID & Pemohon dimaklumkan.
38. Sebagai PSID, saya mahu sediakan Memo Pertimbangan (Kertas Pertimbangan) berserta draf Terma & Syarat Utama dalam sistem selepas rundingan selesai (tidak kira status Traffic Light Normal/Kuning/Merah).

### Hierarki Kelulusan

39. Sebagai sistem, saya mahu hantar Memo Pertimbangan melalui rantaian hierarki kelulusan secara berurutan: PSID → KSID → KKID → TSID → SSID → TKSP(I) → KSP → YB MK.
40. Sebagai pegawai di mana-mana peringkat hierarki (KSID, KKID, TSID, SSID, TKSP(I), KSP), saya mahu Endorse & Hantar Memo ke peringkat seterusnya, supaya proses kelulusan bergerak maju.
41. Sebagai pegawai di mana-mana peringkat hierarki, saya mahu Pulangkan Memo untuk Pembetulan kepada peringkat sebelumnya (bukan hanya di peringkat akhir), supaya pembetulan tidak perlu tunggu sehingga YB MK menolak (lihat ADR-0003).
42. Sebagai PSID, saya mahu terima notifikasi apabila Memo dipulangkan untuk pembetulan, berserta sebab pemulangan, supaya saya tahu apa yang perlu dibetulkan.
43. Sebagai YB MK, saya mahu buat Keputusan Kelulusan (Lulus / Tidak Lulus) ke atas Memo Pertimbangan yang sampai ke peringkat saya.
44. Sebagai sistem, apabila Keputusan Kelulusan = Tidak Lulus, saya mahu kembalikan Permohonan ke peringkat Rundingan (R) untuk dirundingkan semula.
45. Sebagai sistem, apabila Keputusan Kelulusan = Lulus, saya mahu jana Surat Tawaran Rasmi & Lampiran Terma & Syarat Utama Pinjaman (format Lampiran 6) secara automatik.

### Tawaran & Penerimaan

46. Sebagai sistem, selepas Surat Tawaran dijana, saya mahu hantar Notifikasi Penawaran Pinjaman kepada Pemohon.
47. Sebagai sistem, saya mahu kira & papar Traffic Light peringkat Setuju Terima (Kuning bila menghampiri had, Merah bila melepasi 14 hari), supaya Pemohon dimaklumkan tentang tempoh setuju terima.
48. Sebagai Pemohon, saya mahu semak Surat Tawaran dan turunkan Tandatangan Digital pada Surat Akuan Penerimaan (mengikut Garis Panduan ms 55-56), tidak kira status Traffic Light.

### Perjanjian & Peralihan kepada Peminjam

49. Sebagai sistem, selepas Surat Akuan Penerimaan ditandatangan, saya mahu kira & papar Traffic Light Penyediaan Dokumen Perjanjian (Kuning bila menghampiri had, Merah bila melepasi 90 hari), supaya Pemohon dimaklumkan.
50. Sebagai Pemohon, saya mahu muat naik Draf Dokumen Perjanjian, tidak kira status Traffic Light.
51. Sebagai SID dan BUU (Bahagian Undang-Undang MOF), saya mahu semak, runding & sahkan Draf Dokumen Perjanjian teratur sebelum sesi tandatangan manual.
52. Sebagai SID, saya mahu rekod status & tarikh Sesi Tandatangan Manual Dokumen Perjanjian selesai (1 Set Asal Dimatikan Setem + 8 Set Salinan Asal) dalam sistem, sebagai bukti proses fizikal telah berlaku.
53. Sebagai sistem, selepas Sesi Tandatangan Manual direkod selesai, saya mahu kemas kini status pengguna organisasi daripada Pemohon kepada Peminjam secara automatik.
54. Sebagai Peminjam, saya mahu rekod status & tarikh urusan Penyeteman dan Pengesahan Cap Duti Setem LHDNM ke atas Perjanjian dalam sistem, sebagai bukti proses fizikal telah berlaku.

### Syarat Duluan (CP) & Syarat Kemudian (CS)

55. Sebagai sistem, selepas rekod Penyeteman LHDNM, saya mahu kira & papar Traffic Light Syarat Duluan (Kuning bila menghampiri had, Merah bila melepasi 60 hari), supaya Peminjam dimaklumkan.
56. Sebagai Peminjam, saya mahu lengkapkan & muat naik dokumen Syarat Duluan (Cagaran, Gadaian, Bukti Akaun/SLA) mengikut senarai yang ditetapkan, tidak kira status Traffic Light.
57. Sebagai PSID, saya mahu semak & sahkan setiap item Syarat Duluan yang dimuat naik Peminjam.
58. Sebagai PSID, saya mahu tandakan medan pengesahan tunggal 'Syarat Kemudian (CS) Disahkan' secara manual (tanpa aliran kerja/tempoh terperinci — di luar skop v1, lihat CONTEXT.md).
59. Sebagai sistem, saya mahu kunci Permohonan sebagai status LENGKAP hanya apabila SEMUA item CP disahkan PSID DAN medan CS Disahkan ditandakan.
60. Sebagai sistem, selepas Permohonan dikunci LENGKAP, saya mahu halang sebarang pengubahsuaian lanjut ke atas Permohonan tersebut (rekod menjadi baca-sahaja).

### Tray Tugasan, Notifikasi & Traffic Light (rentas peringkat)

61. Sebagai mana-mana pengguna sistem (Pemohon, Kementerian Pengawal, hierarki SID, hierarki Perbendaharaan), saya mahu lihat Tray Tugasan yang memaparkan hanya Permohonan yang memerlukan tindakan saya, supaya saya tidak perlu cari secara manual.
62. Sebagai sistem, saya mahu jalankan job berjadual harian yang menyemak semua Permohonan aktif terhadap kelima-lima had masa (31 Jan, 31 Mac, 14 hari, 90 hari, 60 hari), mengemas kini status Traffic Light masing-masing, dan menghantar notifikasi e-mel + dalam sistem apabila status bertukar kepada Kuning atau Merah.
63. Sebagai pengguna sistem, saya mahu terima notifikasi e-mel mengikut templat Repositori Lampiran 8 bagi setiap peristiwa berkaitan (pendaftaran akaun, penerimaan permohonan, kuiri, tawaran pinjaman, amaran traffic light), supaya saya dimaklumkan tanpa perlu log masuk setiap masa.
64. Sebagai pentadbir sistem, saya mahu urus (CRUD) senarai semak dokumen (Repositori Lampiran 6) dan templat notifikasi (Repositori Lampiran 8) melalui skrin admin, supaya perubahan kandungan tidak memerlukan perubahan kod.

### Kebenaran & Audit

65. Sebagai sistem, saya mahu hadkan setiap tindakan (semak, tandatangan, endorse, pulangkan, lulus) kepada peranan yang dibenarkan sahaja mengikut peringkat semasa Permohonan, supaya tiada tindakan tanpa kebenaran berlaku.
66. Sebagai pegawai (mana-mana peringkat), saya mahu cuba akses tindakan di luar kebenaran peranan saya ditolak dengan mesej jelas, supaya kesilapan operasi dapat dielakkan.
67. Sebagai pegawai SID/audit, saya mahu lihat log sejarah penuh setiap peralihan status, tandatangan, dan tindakan kuiri bagi satu Permohonan, supaya jejak audit lengkap tersedia.

## Implementation Decisions

- **Aggregate root**: `Permohonan` (loan application) — memegang medan borang berstruktur, status fasa (enum kasar), Sumber Dana, rujukan Pemohon, dan hubungan kepada Kuiri, langkah kelulusan, dan dokumen. Satu `Pemohon` (organisasi) `hasMany` `Permohonan`.
- **Status Permohonan**: enum fasa kasar (contoh: Draf, Menunggu Tandatangan Kementerian Pengawal, Dalam Semakan SID, Dalam Kuiri, Dalam Rundingan, Dalam Kelulusan, Ditawarkan, Dalam Perjanjian, Dalam Penyediaan CP, Lengkap). Kedudukan tepat (nod semasa, siapa perlu bertindak, langkah hierarki semasa) direkod dalam struktur langkah/tugasan berasingan, bukan dikodkan ke dalam enum status.
- **Seam ujian utama (disahkan bersama pengguna)**: satu kelas Action per peralihan (contoh: hantar borang, sahkan kelengkapan, tandatangan peringkat 1, hantar ke Kementerian Pengawal, tandatangan peringkat 2, terima di SID, sahkan senarai semak, cetus Kuiri, balas Kuiri, selesai Kuiri, rekod rundingan, sedia memo, endorse hierarki, pulangkan hierarki, keputusan kelulusan, jana tawaran, terima tawaran, muat naik draf perjanjian, sahkan perjanjian, rekod tandatangan manual, tukar status Peminjam, rekod penyeteman, muat naik CP, sahkan item CP, sahkan CS, kunci LENGKAP). Setiap Action diuji terus (panggil Action, sahkan status/medan model + event/notifikasi dihantar). Livewire component adalah wrapper nipis yang memanggil Action berkenaan sahaja — logik perniagaan tidak diulang di lapisan Livewire.
- **Peranan & pengguna**: satu jadual `users` merentasi semua peranan (Pemohon, Kementerian Pengawal, PSID, KSID, KKID, TSID, SSID, TKSP(I), KSP, YB MK, BUU, Admin). Kebenaran dikawal melalui role-based access (Laravel Policy/Gate per Permohonan + peranan pengguna).
- **Fortify self-registration**: ciri `Features::registration()` sedia ada dalam scaffold Fortify perlu **dinyahaktifkan/disekat** untuk akaun Pemohon, kerana akaun Pemohon dicipta oleh SID (bukan layan diri) — lihat CONTEXT.md ("SID Jana ID Log Masuk & Akses"). E-mel pengesahan pendaftaran (pautan 24 jam) kekal digunakan sebagai aliran aktivasi akaun yang dicipta SID, bukan aliran daftar-sendiri terbuka.
- **Tandatangan Digital**: direkod sebagai tindakan diaudit dalaman (user_id, peranan, kenyataan, cap masa) untuk v1 — lihat ADR-0001. Bukan integrasi e-signature pihak ketiga.
- **Cabang Sumber Dana**: KWAPBB tanpa Kementerian Pengawal melangkau semakan kelengkapan borang (E2) & tandatangan Peringkat 1 (D) sepenuhnya, mengikut carta proses literal — lihat ADR-0002.
- **Hierarki kelulusan**: setiap peringkat (bukan hanya YB MK di hujung) mempunyai keupayaan "Pulangkan untuk Pembetulan" ke peringkat sebelumnya — lihat ADR-0003.
- **Kuiri**: model senarai item berasingan (bukan satu mesej tunggal) — satu Permohonan boleh ada berbilang Kuiri aktif serentak, setiap satu dengan thread balasan & status sendiri; semua mesti Berpuas Hati sebelum Permohonan diteruskan ke Rundingan.
- **CS (Syarat Kemudian)**: di luar skop aliran kerja terperinci v1 — direkod sebagai medan pengesahan tunggal (boolean) tanpa traffic light/tempoh sendiri, kerana proses CS tidak dilukis dalam carta rujukan semasa.
- **Traffic Light**: job berjadual (Laravel scheduled command, jalan harian) menyemak semua Permohonan aktif terhadap lima had masa (31 Jan, 31 Mac, 14 hari, 90 hari, 60 hari), mengemas kini status warna, dan hantar notifikasi proaktif (e-mel + dalam sistem) apabila status bertukar Kuning/Merah — bukan pengiraan on-the-fly semasa paparan sahaja.
- **Tray Tugasan / One Stop Center**: inbox tugasan dalaman sistem epinjaman sendiri; tiada integrasi sistem luar diperlukan untuk v1.
- **Repositori Lampiran 6/8**: modul admin CRUD dalam sistem (bukan hardcoded) bagi senarai semak dokumen & templat notifikasi.
- **Proses fizikal (tandatangan manual, penyeteman LHDNM)**: direkod dalam sistem sebagai status + tarikh + pegawai yang mengesahkan (boleh disertakan muat naik imbasan sebagai bukti), tanpa automasi lanjut kerana proses sebenar berlaku di luar sistem.
- **Notifikasi**: mail + database channel (Laravel Notification), templat mengikut Repositori Lampiran 8 (admin-manageable).
- **Kunci LENGKAP**: apabila Permohonan mencapai status Lengkap, rekod menjadi baca-sahaja (tiada Action lanjut dibenarkan ke atasnya).

## Testing Decisions

- Ujian yang baik menguji **kelakuan luaran** Action (input → status/medan Permohonan terhasil, event/notifikasi yang dihantar) — bukan butiran pelaksanaan dalaman (contoh: bukan sahkan query SQL tertentu dijalankan).
- **Modul yang diuji**:
  - Setiap Action peralihan (lapisan seam utama) — ujian Feature Pest, `RefreshDatabase`, memanggil Action terus dengan model `Permohonan`/`Pemohon`/`User` daripada factory, sahkan status/medan terhasil betul.
  - Kebenaran (Policy/Gate) — ujian memastikan peranan yang tidak dibenarkan ditolak (403) bagi setiap Action.
  - Job Traffic Light berjadual — ujian memanipulasi tarikh (Carbon::setTestNow) merentasi lima had masa, sahkan status warna & notifikasi dihantar (Notification::fake()).
  - Modul Kuiri — ujian kitaran cetus → balas → tidak berpuas hati → cetus semula → berpuas hati semua item → Permohonan diteruskan.
  - Hierarki kelulusan — ujian endorse berurutan penuh, dan ujian pulangkan-untuk-pembetulan di pelbagai peringkat pertengahan.
  - Beberapa ujian `Livewire::test()` sebagai smoke test bagi setiap skrin utama (borang permohonan, tray tugasan, semakan kuiri, kelulusan hierarki) — sahkan komponen memaparkan data betul & memanggil Action yang betul apabila tindakan pengguna disimulasikan; tidak mengulang ujian logik status yang sudah dilindungi di lapisan Action.
- **Prior art**: tiada ujian sedia ada dalam repo (`tests/Feature`, `tests/Unit` masih default Laravel/Fortify scaffold) — ikuti struktur Pest sedia ada (`tests/Pest.php`, `RefreshDatabase` trait) dan konvensyen `pest-testing` skill projek ini (Feature test untuk aliran, Livewire component test untuk pendawaian skrin).

## Out of Scope

- Integrasi e-tandatangan digital pihak ketiga yang sah dari segi undang-undang (ADR-0001) — fasa akan datang.
- Aliran kerja & traffic light terperinci bagi Syarat Kemudian (CS) — belum ditakrifkan dalam carta rujukan; hanya medan boolean disediakan.
- Integrasi sistem One Stop Center luaran — Tray Tugasan kekal dalaman sahaja untuk v1.
- Skema medan tepat Borang Permohonan (perlukan kandungan penuh Garis Panduan ms 42-49 yang belum diperoleh) — struktur medan asas boleh dianggar, tetapi senarai medan muktamad perlu disahkan sebelum implementasi borang.
- Integrasi LHDNM sebenar untuk pengesahan cap duti setem — direkod status/tarikh manual sahaja.
- Reka bentuk visual/UI terperinci (wireframe, styling Flux/Tailwind spesifik) — spec ini menumpu pada tingkah laku, bukan visual.
- Pelaporan/analitik merentasi Permohonan (dashboard statistik, eksport) — tiada nod dalam carta rujukan yang meminta ini.

## Further Notes

- Sesi grilling domain (`/grill-with-docs`) menghasilkan `CONTEXT.md` (glosari — Pemohon, Peminjam, Permohonan, hierarki SID/Perbendaharaan, Kuiri, CP/CS) dan tiga ADR di `docs/adr/` yang mesti dihormati semasa implementasi:
  - ADR-0001: Tandatangan Digital sebagai rekod audit dalaman untuk v1
  - ADR-0002: Permohonan KWAPBB tanpa Kementerian Pengawal langkau semakan kelengkapan & tandatangan Peringkat 1
  - ADR-0003: Setiap peringkat hierarki kelulusan boleh pulangkan Memo Pertimbangan ke peringkat sebelumnya
- Codebase semasa ialah scaffold Laravel 13 + Fortify + Livewire 4 + Flux UI v2 yang baharu (hanya model `User`, tiada domain model lain wujud lagi) — semua Action, model, dan skrin dalam spec ini adalah pembinaan baharu.
- Diagram sumber: `docs/cadangan-permohonan-kelulusan.mmd` (carta alir Mermaid, Bahasa Melayu) — rujuk semula bila ada percanggahan tafsiran semasa implementasi.
- Skop spec ini merangkumi keseluruhan kitaran hayat end-to-end mengikut keputusan grilling ("end-to-end penuh"). Oleh saiznya (~67 cerita pengguna, banyak peringkat kebenaran & status), pertimbangkan pecahkan kepada berbilang tiket implementasi (melalui `/to-tickets`) mengikut fasa (contoh: Fasa 1 — Akaun & Borang; Fasa 2 — Semakan & Kuiri; Fasa 3 — Rundingan & Kelulusan; Fasa 4 — Tawaran & Perjanjian; Fasa 5 — CP/CS & Kunci Lengkap) daripada satu tiket monolitik.
