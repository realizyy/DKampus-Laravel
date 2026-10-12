<!DOCTYPE html>
<html lang="id">
<body style="margin:0;background:#FBF7F1;font-family:Arial,Helvetica,sans-serif;color:#1F1A14;">
    <div style="max-width:440px;margin:0 auto;padding:32px 20px;">
        <p style="margin:0 0 24px;font-size:20px;font-weight:700;color:#C2530C;">Dkampus</p>
        <div style="background:#FFFDF9;border:1px solid #E8E0D4;border-radius:14px;padding:24px;">
            <h1 style="margin:0 0 8px;font-size:20px;">{{ $purpose === 'reset' ? 'Atur ulang kata sandi' : 'Verifikasi akun' }}</h1>
            <p style="margin:0 0 20px;font-size:15px;line-height:1.5;color:#51483C;">
                {{ $purpose === 'reset' ? 'Masukkan kode berikut untuk mereset kata sandi akunmu.' : 'Masukkan kode berikut untuk memverifikasi akunmu.' }}
            </p>
            <p style="margin:0 0 20px;font-family:'Courier New',monospace;font-size:34px;font-weight:700;letter-spacing:8px;text-align:center;">{{ $code }}</p>
            <p style="margin:0;font-size:13px;line-height:1.5;color:#655B4D;">
                Kode berlaku {{ $ttlMinutes }} menit. Jangan bagikan kode ini ke siapa pun, termasuk pihak yang mengaku dari Dkampus.
                Jika kamu tidak merasa memintanya, abaikan email ini.
            </p>
        </div>
    </div>
</body>
</html>
