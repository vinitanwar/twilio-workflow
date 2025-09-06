@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">
            Recordings for SIP Trunk: <span class="text-indigo-600">{{ $sipTrunk->friendly_name }}</span>
        </h1>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Call SID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recording URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Redacted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recordings as $recording)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $recording->call_sid }}</td>
                            <td class="px-6 py-4 text-sm text-indigo-600">
                                @if($recording->recording_url)
                                    <a href="{{ $recording->recording_url }}" target="_blank" class="hover:underline">Listen</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($recording->is_redacted)
                                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Yes</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection