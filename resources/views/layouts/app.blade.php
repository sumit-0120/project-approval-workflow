<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Panel')</title>

    @include('layouts.head')
</head>
<body>

    @include('layouts.header')
    @include('layouts.sidebar')

    <main id="main" class="main">
        @yield('content')
    </main>

    @include('layouts.footer')

</body>
</html>
