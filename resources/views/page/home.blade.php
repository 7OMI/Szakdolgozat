@extends('layout.full-width')

@section('content')

  <div class="login">
    <form id="form" action="{{ route('login') }}" method="post">
      @csrf

      <div class="logo">
        <svg width="380" height="100" style="background:rgba(0,0,255,0.1);">
          <text x="0" y="55" transform="translate(100)" font-family="Ubuntu">
            <tspan x="92" text-anchor="middle" font-size="45">Leltárkezelő</tspan>
            <tspan x="140" text-anchor="right" font-size="28" dy="25" style="font-style:italic;">&nbsp;</tspan>
            <tspan x="-20" text-anchor="left" font-size="14" dy="0" style="">v1.0</tspan>
          </text>
        </svg>
      </div>

      <span><input class="input" name="email" type="text" placeholder="E-mail cím"></span>
      <span><input class="input" name="password" type="password" placeholder="Jelszó"></span>

      @if($errors->any())
        <div style="padding: 15px; color: red;">
          {!! implode('', $errors->all('<div>:message</div>')) !!}
        </div>
      @endif

      @include('component.button', ['data' => ['sign-in', 'Belépés', '#login', '{"type":"right"}']])

    </form>
  </div>

@endsection
