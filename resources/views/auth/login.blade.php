@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8">

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
            {{ __('Login') }}
        </h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    {{ __('Email Address') }}
                </label>

                <input id="email" type="email"
                    class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('email') border-red-500 @enderror"
                    name="email" value="{{ old('email') }}" required autofocus>

                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    {{ __('Password') }}
                </label>

                <input id="password" type="password"
                    class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('password') border-red-500 @enderror"
                    name="password" required>

                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('remember') ? 'checked'
                        : '' }}>
                    <span class="text-sm text-gray-600">
                        {{ __('Remember Me') }}
                    </span>
                </label>

                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                    {{ __('Forgot Password?') }}
                </a>
                @endif
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    {{ __('Login') }}
                </button>
            </div>

        </form>
    </div>
</div>
@endsection