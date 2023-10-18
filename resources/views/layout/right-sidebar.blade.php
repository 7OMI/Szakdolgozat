@include('section.header')

<main class="frameMain">
  @yield('content')
</main>

<aside class="frameList">
  @yield('sidebar')
</aside>

@include('section.footer')
