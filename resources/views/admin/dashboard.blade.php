@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển')

@section('content')

<style>
    .content-area {
        min-height: 600px ;
        padding: 24px;
        background-color: #f8f9fa;
    }
</style>

<div class="content-area">
    <h2 class="mb-4">Dashboard</h2>

    <!-- Thống kê nhanh -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Tổng sản phẩm</h5>
                    <p class="card-text fs-4">125</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng hôm nay</h5>
                    <p class="card-text fs-4">32</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-bg-warning mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Người dùng mới</h5>
                    <p class="card-text fs-4">7</p>
                </div>
            </div>
        </div>
    </div>

    
    </div>
</div>

@endsection
