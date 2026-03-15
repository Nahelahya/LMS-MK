<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Project</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="m-0 p-0 font-sans">
    
    <div class="relative min-h-screen w-full flex flex-col bg-cover bg-center bg-no-repeat bg-fixed" 
        style="background-image: url('{{ asset('images/bg-pkm.jpg') }}');">
        
        <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

        <div class="relative z-10 flex flex-col min-h-screen">
            
            <header class="pt-12 text-center">
                <h1 class="text-4xl font-bold text-white tracking-widest drop-shadow-lg uppercase">
                    LMS PROJECT NAME
                </h1>
            </header>
            
            <main class="grow flex justify-center items-center p-4">
                @yield('content')
            </main>

            <footer class="pb-8 text-center text-gray-300 text-sm">
                <p>&copy; 2026 <strong class="text-white">NAHdev</strong>. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>