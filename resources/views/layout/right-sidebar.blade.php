@include('section.header')

<div class="frameContainer">

  <div class="topMenu">
    @include('component.menu-top')
  </div>

  <main class="frameMain">
    @yield('content')
    @include('component.menu-main')
  </main>

  <aside class="frameList frameHide">
    @yield('sidebar')
  </aside>

</div>

@include('section.footer')
