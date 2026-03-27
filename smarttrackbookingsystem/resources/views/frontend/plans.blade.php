@extends('layouts.app')

@section('content')
<style>
.pricing-wrapper {
    display: flex;
    gap: 30px;
    justify-content: center;
    align-items: stretch;
    margin-top: 60px;
    flex-wrap: wrap;
}

.pricing-card {
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    width: 300px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    text-align: center;
    position: relative;
    transition: 0.3s;
}

.pricing-card:hover {
    transform: translateY(-8px);
}

.pricing-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 10px;
}

.pricing-price {
    font-size: 42px;
    font-weight: 800;
    color: #2563eb;
    margin: 20px 0;
}

.pricing-price span {
    font-size: 16px;
    font-weight: 400;
    color: #666;
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    text-align: left;
}

.pricing-features li {
    margin-bottom: 10px;
}

.pricing-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #2563eb;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}

.pricing-btn:hover {
    background: #1e40af;
}

/* Recommended (Pro) */
.recommended {
    border: 2px solid #2563eb;
    transform: scale(1.05);
}

.badge {
    position: absolute;
    top: -10px;
    right: 20px;
    background: #2563eb;
    color: #fff;
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
}
</style>

<div class="container wide h-100 d-flex align-items-center justify-content-center">
    <div class="pricing-wrapper">

        @foreach($plans as $plan)
            <div class="pricing-card {{ strtolower($plan->name) == 'pro' ? 'recommended' : '' }}">

                @if(strtolower($plan->name) == 'pro')
                    <div class="badge">Recommended</div>
                @endif

                <div class="pricing-title">{{ $plan->name }}</div>

                <div class="pricing-price">
                    ${{ $plan->price }}
                    <span>/month</span>
                </div>

                <ul class="pricing-features">
                    <li>{{ $plan->max_employees }} Employees</li>
                    <li>{{ $plan->max_services }} Services</li>
                    <li>{{ $plan->max_bookings }} Bookings</li>
                </ul>

                <a href="{{ route('register.business', $plan->id) }}" class="pricing-btn">
                    Choose Plan
                </a>
            </div>
        @endforeach

    </div>
</div>
@endsection