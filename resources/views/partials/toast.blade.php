@if(session('toast') || session('toast_type'))
    @php
        $toastMsg = session('toast') ?? session('success') ?? session('message') ?? '';
        $toastType = session('toast_type') ?? (session('toast') && session('toast')['type'] ?? null) ?? (session('success') ? 'success' : 'info');
    @endphp

    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div class="toast align-items-center text-bg-{{ $toastType ?? 'primary' }} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {!! is_array($toastMsg) ? e($toastMsg['message'] ?? json_encode($toastMsg)) : e($toastMsg) !!}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif
