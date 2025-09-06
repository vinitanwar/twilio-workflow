@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">SIP Trunks</h1>
            <a href="{{ route('trunks.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-black rounded-lg shadow hover:bg-indigo-700 transition">
                + Create New Trunk
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($trunks->isEmpty())
            <div class="text-gray-500 text-center py-10">
                No SIP trunks found.
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Origination URI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BYOC</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Recordings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($trunks as $trunk)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $trunk->friendly_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $trunk->domain_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $trunk->origination_uri }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($trunk->byocTrunk()->exists())
                                        <span class="px-2 py-1 text-green-800 bg-green-100 rounded text-xs">
                                            {{ $trunk->byocTrunk->friendly_name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <a href="{{ route('byoc.create', $trunk->id) }}"
                                            class="inline-flex px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs hover:bg-yellow-200 transition">
                                            Add BYOC
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('trunks.recordings', $trunk->id) }}"
                                        class="inline-flex px-3 py-1 bg-blue-100 text-blue-800 rounded text-xs hover:bg-blue-200 transition">
                                        View Recordings
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection