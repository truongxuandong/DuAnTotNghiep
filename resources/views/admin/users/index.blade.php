@extends('admin.layouts.admin')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Users</h1>

    @php
        $columns = [
            ['label' => 'ID', 'key' => 'id', 'class' => 'w-12', 'td_class' => 'font-medium'],
            ['label' => 'Name', 'key' => 'name'],
            ['label' => 'Email', 'key' => 'email', 'link' => true],
            ['label' => 'Position', 'key' => 'position'],
            ['label' => 'Company', 'key' => 'company'],
            ['label' => 'Country', 'key' => 'country'],
        ];

        // example rows; replace with a paginator or collection from controller
        $rows = [
            ['id' => '01','name' => 'Jonathan','email' => 'jonathan@example.com','position'=>'Senior Implementation Architect','company'=>'Hauck Inc','country'=>'Holy See'],
            ['id' => '02','name' => 'Harold','email' => 'harold@example.com','position'=>'Forward Creative Coordinator','company'=>'Metz Inc','country'=>'Iran'],
            ['id' => '03','name' => 'Shannon','email' => 'shannon@example.com','position'=>'Legacy Functionality Associate','company'=>'Zemlak Group','country'=>'South Georgia'],
            ['id' => '04','name' => 'Robert','email' => 'robert@example.com','position'=>'Product Accounts Technician','company'=>'Hoeger','country'=>'San Marino'],
            ['id' => '05','name' => 'Noel','email' => 'noel@example.com','position'=>'Customer Data Director','company'=>'Howell - Rippin','country'=>'Germany'],
        ];
    @endphp

    @include('admin.components.common-table', ['columns' => $columns, 'rows' => $rows])

@endsection
