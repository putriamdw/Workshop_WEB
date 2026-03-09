@extends('layouts.main')

@section('content')

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">JS 1 - Spinner Submit</h4>
            </div>

        <div class="card-body">
            {{-- FORM --}}
            <form id="myForm">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" required minlength="6">
                </div>

                <div class="mb-4">
                    <label class="form-label">No. Telepon</label>
                    <input type="tel" id="telepon" class="form-control" required>
                </div>
            </form>

            {{-- BUTTON DI LUAR FORM --}}
            <button type="button" id="btnSubmit" class="btn btn-primary w-100 mt-2">
                Submit
            </button>

            <div id="successMsg" class="alert alert-success mt-3 d-none">
                ✅ Form berhasil disubmit!
            </div>

        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
    function handleSubmit() {
        const btn = document.getElementById('btnSubmit');
        const inputs = [
            document.getElementById('nama'),
            document.getElementById('email'),
            document.getElementById('password'),
            document.getElementById('telepon'),
        ];

        // Reset invalid state
        inputs.forEach(input => input.classList.remove('is-invalid'));

        // (i) Cek apakah semua input required terisi - checkValidity HTML5
        let allValid = true;
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                allValid = false;
            }
        });

        // (ii) Jika ada yang kosong, tampilkan pesan - reportValidity HTML5
        if (!allValid) {
            inputs.find(i => !i.checkValidity())?.reportValidity();
            return;
        }

        // (iii) Semua terisi, ubah button jadi spinner
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Memproses...
        `;

        // Simulasi proses (AJAX/submit form)
        setTimeout(() => {
            btn.innerHTML = '✅ Berhasil!';
            btn.classList.replace('btn-primary', 'btn-success');
            document.getElementById('successMsg').classList.remove('d-none');
        }, 2500);
    }

    document.getElementById("btnSubmit").addEventListener("click", handleSubmit);
</script>
@endsection