@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Create SIP Trunk</h1>

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('trunks.store') }}" class="space-y-4 bg-white shadow rounded-lg p-6">
            @csrf

            <div>
                <label for="friendly_name" class="block text-sm font-medium text-gray-700">Friendly Name</label>
                <input type="text" name="friendly_name" id="friendly_name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required>
                @error('friendly_name')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="domain_name" class="block text-sm font-medium text-gray-700">Domain Name</label>
                <input type="text" name="domain_name" id="domain_name" placeholder="e.g., yourcompany.sip.twilio.com"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required>
                @error('domain_name')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="origination_uri" class="block text-sm font-medium text-gray-700">Origination URI</label>
                <input type="text" name="origination_uri" id="origination_uri" placeholder="e.g., sip:pbx.yourdomain.com"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required>
                @error('origination_uri')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username (optional)</label>
                <input type="text" name="username" id="username"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password (optional)</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2 text-black shadow hover:bg-indigo-700 transition">
                    Create
                </button>
            </div>
        </form>
    </div>
@endsection