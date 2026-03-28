@extends('admin.layout')

@section('title', 'QR Code - ' . $formStore->name)

@push('styles')
<style>
/* CARD STYLE */
.card-modern {
    box-shadow: 0 20px 60px rgba(0,0,0,0.08);
}

/* QR FIX: Menghilangkan paksa padding agar QR maksimal */
.qr-content {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
}

.qr-content svg, .qr-content img {
    width: 100% !important;
    height: auto !important;
    display: block;
    margin: 0 auto;
}

/* HOVER EFFECT */
.hover-lift {
    transition: all 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.12);
}

/* COPY SUCCESS */
.copy-success {
    background: #dcfce7 !important;
    border-color: #22c55e !important;
}

/* PRINT MODE */
@media print {
    nav, .no-print {
        display: none !important;
    }

    body {
        background: white !important;
        margin: 0;
        padding: 0;
    }

    .print-area {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        max-width: 400px;
        box-shadow: none !important;
        border: 1px solid #eee;
        border-radius: 2rem;
    }

    .bg-blue-600 {
        background-color: #2563eb !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">

    <div class="no-print flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">QR Scan Form</h2>
            <p class="text-sm text-gray-500">Pratinjau kartu untuk <strong>{{ $formStore->name }}</strong></p>
        </div>

        <a href="{{ route('admin.form-stores.index') }}"
           class="text-blue-600 hover:text-blue-800 font-bold text-sm flex items-center gap-2 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            KEMBALI
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-10">

        <div class="print-area bg-white rounded-[2.5rem] overflow-hidden card-modern border border-gray-100">

            <div class="bg-blue-600 p-8 text-center">
                <h1 class="text-xl md:text-2xl font-black text-white uppercase tracking-tight">
                    {{ str_replace(['_', '-'], ' ', $formStore->name) }}
                </h1>
                <p class="text-blue-100 text-[10px] mt-2 uppercase tracking-[0.3em] font-bold opacity-90">
                    Scan to Open Form
                </p>
            </div>

            <div class="p-8 flex flex-col items-center">

                <div class="bg-gray-50 p-4 rounded-[2rem] border-2 border-dashed border-gray-200 w-full flex justify-center items-center">
                    <div class="qr-content bg-white p-2 rounded-xl shadow-sm w-full max-w-[310px]">
                        {!! $qr !!}
                    </div>
                </div>

                

            </div>
        </div>

        <div class="no-print space-y-6">
            <div class="bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm">
                <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                    Opsi QR Code
                </h3>

                <div class="flex flex-col gap-4">
                    <a href="{{ route('public.form.store.qr.download', $formStore->custom_url_slug) }}"
                       class="hover-lift w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl text-center shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                        DOWNLOAD PNG
                    </a>

                    <button onclick="window.print()"
                            class="hover-lift w-full bg-white border-2 border-gray-200 py-4 rounded-2xl font-black text-gray-600 hover:bg-gray-50 flex items-center justify-center gap-2">
                        CETAK KARTU
                    </button>
                </div>
            </div>

            <div class="bg-blue-50/50 border border-blue-100 rounded-[2rem] p-6 flex gap-4">
                <div class="text-blue-600 font-black text-xl">ℹ</div>
                <p class="text-[11px] text-blue-800 leading-relaxed font-medium">
                    QR Code sudah dioptimalkan agar berada tepat di tengah bingkai. Disarankan menggunakan kertas foto atau sticker untuk hasil cetak terbaik.
                </p>
            </div>
        </div>

    </div>
</div>

<script>
function copyLink(el) {
    const linkText = document.getElementById('formLink').innerText;
    navigator.clipboard.writeText(linkText).then(() => {
        const pTag = el.querySelector('p');
        const originalText = pTag.innerText;
        el.classList.add('copy-success');
        pTag.innerText = "LINK DISALIN!";
        setTimeout(() => {
            el.classList.remove('copy-success');
            pTag.innerText = originalText;
        }, 1500);
    });
}
</script>
@endsection