<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="'Email'" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input
                    id="password"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password" />

                <!-- Toggle Password -->
                <button
                    type="button"
                    onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">

                    <!-- Eye Slash -->
                    <svg id="eyeSlash"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19
                    c-4.478 0-8.268-2.943-9.542-7
                    a9.956 9.956 0 012.223-3.592M6.228 6.228
                    A9.956 9.956 0 0112 5c4.478 0
                    8.268 2.943 9.542 7a9.97 9.97
                    0 01-4.293 5.274M6.228 6.228L3 3m3.228
                    3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228
                    l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243
                    4.243L9.88 9.88" />
                    </svg>

                    <!-- Eye -->
                    <svg id="eyeOpen"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 hidden"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                    c4.478 0 8.268 2.943 9.542 7
                    -1.274 4.057-5.064 7-9.542 7
                    -4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password Row -->
        <div class="flex items-center justify-between mt-5 text-sm">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded-lg border-gray-300 dark:border-slate-700 text-blue-600 shadow-sm focus:ring-blue-500 bg-transparent"
                    name="remember">

                <span class="ms-2 text-xs font-semibold text-slate-500 dark:text-slate-400 select-none">
                    Remember me
                </span>
            </label>

            @if (Route::has('password.request'))
            <a
                class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline transition"
                href="{{ route('password.request') }}">
                Lupa kata sandi?
            </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-4 rounded-2xl transition duration-300 shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                Login
            </button>
        </div>
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeSlash = document.getElementById('eyeSlash');
            const eyeOpen = document.getElementById('eyeOpen');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';

                eyeSlash.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';

                eyeSlash.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>