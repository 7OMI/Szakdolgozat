@extends('layout.right-sidebar')

@section('sidebar')
  @include('component.product-list')
@endsection

@section('content')

  TARTALOM
  <a href="/logout">kilépés</a>

@endsection
