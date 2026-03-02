@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')


<h3 class="page-title mb-3">Dashboard</h3>

<h4>
    Hello, {{ auth()->user()->name }} 👋🏻
</h4>

<p>
    Role:
    <span class="badge 
        {{ auth()->user()->role == 'admin' ? 'bg-danger' : 'bg-primary' }}">
        {{ auth()->user()->role }}
    </span>
</p>

<div class="row mt-4">

    <div class="col-md-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Buku</h6>
                <h2 class="fw-bold text-primary">{{ $totalBuku }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Kategori</h6>
                <h2 class="fw-bold text-success">{{ $totalKategori }}</h2>
            </div>
        </div>
    </div>

</div>

@if(auth()->user()->role == 'admin')
    <div class="alert alert-info mt-4">
        Anda login sebagai <strong>Admin</strong>. 
        Anda bisa mengelola data Buku dan Kategori.
    </div>

    <div class="mt-2">
        <a href="{{ route('buku.index') }}" class="btn btn-primary">
            Kelola Buku
        </a>
        <a href="{{ route('kategori.index') }}" class="btn btn-success">
            Kelola Kategori
        </a>
    </div>
@else
    <div class="alert alert-secondary mt-4">
        Anda login sebagai <strong>User</strong>. 
        Anda hanya bisa melihat data.
    </div>

    <div class="mt-2">
        <a href="{{ route('buku.index') }}" class="btn btn-primary">
            Lihat Buku
        </a>
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
            Lihat Kategori
        </a>
    </div>
@endif

@endsection

