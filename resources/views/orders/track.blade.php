@extends('layouts.app')
@section('title', 'Tra c?u h�nh tr�nh don h�ng - BeatyCare ??')
@section('canonical', route('orders.track'))

@section('content')
<div class="container py-4 view-inline-1">
    <div class="text-center mb-4">
        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 fw-semibold mb-2">
            <i class="bi bi-truck me-1"></i> TRA C?U �ON H�NG NHANH
        </span>
        <h1 class="fw-bold fs-2 text-dark mb-2">Theo d�i h�nh tr�nh don h�ng</h1>
        <p class="text-muted">Nh?p m� don h�ng v� s? di?n tho?i (ho?c email) d? tra c?u tr?ng th�i don h�ng m� kh�ng c?n dang nh?p.</p>
    </div>

    <!-- FORM TRA C?U -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('orders.track.submit') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-dark">M� don h�ng</label>
                    <input type="text" name="order_id" class="form-control rounded-pill px-3" placeholder="V� d?: 1024 ho?c #1024" value="{{ old('order_id', request('order_id', isset($order) ? $order->id : '')) }}" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-dark">S? di?n tho?i ho?c Email d?t h�ng</label>
                    <input type="text" name="contact" class="form-control rounded-pill px-3" placeholder="S? di?n tho?i ho?c email..." value="{{ old('contact', request('contact')) }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm">
                        <i class="bi bi-search me-1"></i>Tra c?u
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(isset($order))
        @php
            $statusSteps = [
                'processing' => ['label' => 'Ch? x�c nh?n', 'icon' => 'bi-hourglass-split'],
                'confirmed' => ['label' => '�� x�c nh?n', 'icon' => 'bi-check2'],
            ];
            if ($order->payment_method !== 'COD') {
                $statusSteps['paid'] = ['label' => '�� thanh to�n', 'icon' => 'bi-credit-card'];
            }
            $statusSteps += [
                'packing' => ['label' => '�ang d�ng g�i', 'icon' => 'bi-box-seam'],
                'shipping' => ['label' => '�ang giao h�ng', 'icon' => 'bi-truck'],
                'completed' => ['label' => '�� nh?n h�ng', 'icon' => 'bi-check2-circle'],
            ];
            $currentStep = array_search($order->status, array_keys($statusSteps), true);
            $currentStep = $currentStep === false ? 0 : $currentStep;
        @endphp

        <!-- K?T QU? �ON H�NG -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-danger rounded-pill mb-1">M� don: #{{ $order->id }}</span>
                    <h3 class="fw-bold fs-5 mb-0 text-dark">�?t ng�y {{ $order->created_at->format('d/m/Y H:i') }}</h3>
                </div>
                <div class="text-end">
                    <span class="text-muted small">T?ng thanh to�n:</span>
                    <div class="text-danger fw-bold fs-4">{{ number_format($order->total, 0, ',', '.') }} ?</div>
                </div>
            </div>

            <!-- TIMELINE -->
            @if(!in_array($order->status, ['cancelled', 'refund_pending', 'refunded']))
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-signpost-2 text-danger me-2"></i>H�nh tr�nh giao h�ng</h6>
                    <div class="d-flex justify-content-between align-items-center overflow-x-auto py-2 view-inline-2">
                        @foreach($statusSteps as $step => $stepData)
                            @php $stepIndex = array_search($step, array_keys($statusSteps), true); @endphp
                            <div class="text-center position-relative flex-fill px-1">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $stepIndex <= $currentStep ? 'bg-danger text-white shadow-sm' : 'bg-light text-muted' }} view-inline-3">
                                    <i class="bi {{ $stepData['icon'] }} fs-5"></i>
                                </div>
                                <div class="small fw-semibold {{ $stepIndex <= $currentStep ? 'text-dark' : 'text-muted' }} view-inline-4">
                                    {{ $stepData['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-warning rounded-4 border-0 mb-4">
                    <i class="bi bi-info-circle me-1"></i>Tr?ng th�i don h�ng: <strong>{{ $order->status === 'cancelled' ? '�� h?y' : '�ang x? l� ho�n ti?n' }}</strong>
                </div>
            @endif

            <!-- DANH S�CH M�N H�NG -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check text-danger me-2"></i>S?n ph?m trong don ({{ $order->items->count() }})</h6>
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($order->items as $item)
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light-subtle">
                        <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : asset('images/placeholder.svg') }}" class="rounded-3 object-fit-cover" width="60" height="60" alt="{{ $item->product->name ?? 'S?n ph?m' }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                        <div class="flex-grow-1">
                            <h4 class="fw-semibold fs-6 mb-1 text-dark">{{ $item->product->name ?? 'S?n ph?m' }}</h4>
                            @if($item->variation)
                                <span class="badge bg-secondary-subtle text-secondary small">
                                    {{ collect([$item->variation->color, $item->variation->size_value ? $item->variation->size_value.$item->variation->size_unit : null, $item->variation->storage])->filter()->implode(' � ') }}
                                </span>
                            @endif
                            <div class="small text-muted mt-1">{{ number_format($item->price, 0, ',', '.') }} ? x {{ $item->quantity }}</div>
                        </div>
                        <strong class="text-danger">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} ?</strong>
                    </div>
                @endforeach
            </div>

            <!-- TH�NG TIN GIAO H�NG -->
            <div class="row g-3 border-top pt-3">
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Ngu?i nh?n:</h6>
                    <div class="small text-muted">{{ $order->receiver_name ?: ($order->user->name ?? 'Qu� kh�ch') }}</div>
                    <div class="small text-muted">{{ $order->receiver_phone ?: ($order->phone ?: '�') }}</div>
                    <div class="small text-muted">{{ $order->shipping_address ?: 'Giao h�ng ti�u chu?n' }}</div>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-2">Thanh to�n & V?n chuy?n:</h6>
                    <div class="small text-muted">Phuong th?c: <strong>{{ $order->payment_method }}</strong></div>
                    <div class="small text-muted">�on v? v?n chuy?n: <strong>{{ config('shop.shipping_providers.' . $order->shipping_provider, $order->shipping_provider ?? 'Giao H�ng Nhanh') }}</strong></div>
                    @if($order->tracking_number)
                        <div class="small text-muted">M� v?n don: <strong class="text-primary">{{ $order->tracking_number }}</strong></div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
