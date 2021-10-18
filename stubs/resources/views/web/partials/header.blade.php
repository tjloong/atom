<header class="flex justify-between px-6 py-4 max-w-screen-xl mx-auto">
    <a class="flex-shrink-0 w-16 md:w-28" href="/">
        <img src="https://jiannius.com/logo.svg" alt="{{ config('app.name') }} logo" class="w-full">
    </a>

    <button 
        type="button"
        class="md:hidden"
        onclick="
            document.querySelector('header nav').classList.remove('-right-60');
            document.querySelector('header nav').classList.add('right-0');
        "
    >
        <box-icon name="menu" size="24px"></box-icon>
    </button>

    <nav class="
        fixed -right-60 top-0 bottom-0 z-20 w-60 transition-all duration-75 shadow bg-white px-6 py-4 flex flex-col
        md:relative md:right-0 md:w-auto md:shadow-none md:bg-transparent md:py-0 md:px-0
        md:flex-grow md:flex-row md:items-center md:justify-end
    ">
        <button 
            type="button" 
            class="w-full text-right md:hidden" 
            onclick="
                document.querySelector('header nav').classList.add('-right-60');
                document.querySelector('header nav').classList.remove('right-0');
            "
        >
            <box-icon name="x" size="24px"></box-icon>
        </button>

        <a href="/" class="my-2 md:mx-4">
            <span class="font-light">NEW</span><span class="font-bold">APP</span>
        </a>

        @auth
            <a href="{{ route('dashboard') }}" class="my-2 font-medium md:ml-4">
                Go to Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="my-2 font-medium md:mx-4">
                Login
            </a>
    
            <a href="{{ route('register', ['ref' => 'navbar-get-started']) }}" class="bg-gray-800 px-4 py-2 my-2 rounded shadow text-sm text-white text-center md:ml-4">
                Get Started
            </a>
        @endauth
    </nav>
</header>
