# Sistem ePinjaman

Sistem permohonan & kelulusan pinjaman kerajaan (Bahagian Pelaburan Strategik, MOF) — dari pendaftaran pemohon sehingga Syarat Duluan (Conditions Precedent) disahkan dan permohonan dikunci sebagai LENGKAP.

## Language

**Pemohon**:
Organisasi (agensi kerajaan/GLC) yang memohon pinjaman. Akaunnya dicipta oleh SID (bukan daftar sendiri); boleh diwakili oleh lebih daripada satu pengguna yang bertindak bagi pihaknya.
_Avoid_: Applicant, syarikat, client

**Peminjam**:
Status Pemohon selepas sesi tandatangan manual Dokumen Perjanjian selesai. Entiti yang sama, status berubah daripada Pemohon.
_Avoid_: Borrower (guna dalam kod jika perlu istilah Inggeris, tapi label domain kekal "Peminjam")

**Permohonan**:
Satu rekod permohonan pinjaman yang dikemukakan oleh Pemohon. Satu Pemohon (organisasi) boleh mempunyai berbilang Permohonan sepanjang masa (contoh: mohon pinjaman berasingan pada tahun berlainan). Aggregate root utama sistem — memegang status fasa (kasar), Kuiri, langkah kelulusan, dan dokumen berkaitan.
_Avoid_: Application, loan application (guna "Permohonan")

### Hierarki Pegawai SID (Bahagian Pelaburan Strategik)

Rantaian semakan dalaman, dari peringkat operasi ke pengurusan tertinggi:

**PSID**: Pegawai SID — peringkat pemprosesan operasi (semak dokumen, cetus kuiri, sedia memo).
**KSID**: Ketua Cawangan SID.
**KKID**: Ketua Bahagian SID.
**TSID**: Timbalan Ketua Pengarah SID.
**SSID**: Setiausaha/Ketua Pengarah SID.

### Hierarki Kelulusan Perbendaharaan

**TKSP(I)**: Timbalan Ketua Setiausaha Perbendaharaan (Icon/Pelaburan).
**KSP**: Ketua Setiausaha Perbendaharaan.
**YB MK**: Menteri Kewangan — kelulusan muktamad.

### Pihak Lain

**Kementerian Pengawal**:
Kementerian yang menyelia Pemohon bagi permohonan bersumber KWAPBB (jika berkaitan); mengesahkan & menandatangan peringkat 2 sebelum permohonan sampai ke SID.
_Avoid_: Ministry, controlling ministry

**BUU**:
Bahagian Undang-Undang MOF. Menyemak & mengesahkan Dokumen Perjanjian teratur sebelum tandatangan manual.
_Avoid_: Legal division (guna BUU sebagai istilah domain)

## Kuiri

**Kuiri**:
Satu item isu/pertanyaan spesifik (contoh: dokumen tidak lengkap) yang dicetuskan oleh PSID terhadap satu Permohonan, dengan thread balasan & status sendiri (Terbuka / Berpuas Hati). Satu Permohonan boleh ada berbilang Kuiri aktif serentak; semua mesti Berpuas Hati sebelum Permohonan boleh diteruskan ke Rundingan.
_Avoid_: Query, pertanyaan (guna "Kuiri" sebagai istilah domain rasmi)

## Syarat Perjanjian

**CP (Syarat Duluan / Conditions Precedent)**:
Senarai syarat (cagaran, gadaian, bukti akaun/SLA) yang Peminjam perlu lengkapkan & muat naik dalam 60 hari selepas perjanjian, sebelum Permohonan boleh dikunci LENGKAP. Ada traffic light 60 hari.
_Avoid_: Conditions precedent (guna "CP" / "Syarat Duluan")

**CS (Syarat Kemudian / Conditions Subsequent)**:
Kategori syarat berasingan daripada CP yang Peminjam perlu penuhi selepas perjanjian. Proses & tempoh CS belum ditakrifkan dalam carta rujukan semasa — **di luar skop v1**; direkod sebagai medan pengesahan tunggal (boolean, disahkan manual oleh PSID) tanpa aliran kerja/traffic light sendiri buat masa ini.
_Avoid_: Conditions subsequent (guna "CS" / "Syarat Kemudian")
