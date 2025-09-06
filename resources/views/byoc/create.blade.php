@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        Create BYOC for SIP Trunk: <span class="text-indigo-600">{{ $sipTrunk->friendly_name }}</span>
    </h1>

    <form method="POST" action="{{ route('byoc.store', $sipTrunk->id) }}" class="space-y-4 bg-white shadow rounded-lg p-6">
        @csrf

        <div>
            <label for="friendly_name" class="block text-sm font-medium text-gray-700">Friendly Name</label>
            <input id="friendly_name" name="friendly_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="sip_target_uri" class="block text-sm font-medium text-gray-700">Carrier SIP Target URI</label>
            <input id="sip_target_uri" name="sip_target_uri" required placeholder="e.g., sip:carrier.example.com:5060"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div class="pt-4">
            <button type="submit"
                class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2 text-white shadow hover:bg-indigo-700 transition">
                Create BYOC
            </button>
        </div>
    </form>
</div>
@endsection
