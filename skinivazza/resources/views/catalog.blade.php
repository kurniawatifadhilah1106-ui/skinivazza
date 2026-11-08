@extends('layout')@section('c')<h1 class="font-serif text-3xl mb-4">Semua Produk</h1><div class="grid grid-cols-2 md:grid-cols-4 gap-3">@foreach($p as $x)@include('_card')@endforeach</div>@endsection
