<!DOCTYPE html>
<html>
<head>
    <title>SmartHome Gateway Dashboard</title>

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ALPINE JS FOR INTERACTION -->
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-gray-900 text-white p-4 shadow">
        <div class="container mx-auto text-xl font-semibold">
            SmartHome Gateway Dashboard
        </div>
    </nav>

    <main class="container mx-auto mt-6">
        @yield('content')
    </main>
</body>
</html>
