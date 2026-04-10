<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floating Notice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS -->
    <link href="{{ url('assets/floating-notice/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ url('assets/floating-notice/css/style.css') }}" rel="stylesheet" />
</head>

<body>

    <!-- Header Section -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <img src="{{ url('assets/floating-notice/image/logo.png') }}" alt="">
            </div>
            <nav>
                <ul class="nav-menu">

                    <li class="nav-item"><a href="{{ url('/') }}" class="nav-link text-white">Contact
                            Trustvisory.com</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content Section -->
    <main class="main-container">
        <div class="container">
            @yield("content")
        </div>
    </main>

    <!-- Footer Section -->
    <footer>
        <p style="margin:0;">Public Awareness by Trustvisory.com</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ksm0n6paQqqOQ3MxWJxsb0EsBrKE1f3ZP6NKxKoLLz5ly5zk4Q/OHsE9PjUQxOV8" crossorigin="anonymous">
    </script>

</body>

</html>
