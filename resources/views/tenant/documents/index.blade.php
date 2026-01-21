@extends('layouts.tenant')

@section('title', 'Documents')

@section('content')

<div x-data="{
    confirmOpen: false,
    confirmFormId: '',
    confirmLabel: '',
    open: false, previewUrl: null
}">


    {{-- UPLOAD BUTTON --}}
    <a href="/org/{{ request()->route('tenant') }}/documents/upload"
        class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">
        Upload Document
    </a>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-indigo-900">
                        Title
                    </th>
                    <th class="p-3 text-left font-semibold text-indigo-900">
                        Uploaded By
                    </th>
                    <th class="p-3 text-left font-semibold text-indigo-900">
                        Visibility
                    </th>
                    <th class="p-3 text-left font-semibold text-indigo-900">
                        Actions
                    </th>
                </tr>
            </thead>


            <tbody>
                @forelse($documents as $doc)

                @php
                $user = auth('tenant')->user();

                $canDelete =
                ($user->role === 'employee'
                && $doc->visibility === 'private'
                && $doc->uploaded_by === $user->id)
                ||
                ($user->role === 'manager'
                && in_array($doc->visibility, ['private', 'team'])
                && $doc->uploaded_by === $user->id)
                ||
                ($user->role === 'admin');
                @endphp

                <tr class="odd:bg-slate-50 even:bg-white hover:bg-slate-100 transition">
                    {{-- TITLE --}}
                    <td class="p-3">
                        {{ $doc->title }}
                    </td>

                    {{-- UPLOADED BY --}}
                    <td class="p-3">
                        {{ $doc->uploader?->name ?? 'System' }}<br>
                        <span class="text-xs text-slate-500">
                            {{ $doc->created_at->format('d M Y') }}
                        </span>
                    </td>

                    {{-- VISIBILITY --}}
                    <td class="p-3">
                        {{ ucfirst($doc->visibility) }}
                    </td>

                    {{-- ACTIONS --}}
                    <td class="p-3">
                        <div class="flex items-center gap-4">

                            {{-- PREVIEW (IMAGE / PDF ONLY) --}}
                            @if(str_starts_with($doc->mime_type, 'image/')
                            || $doc->mime_type === 'application/pdf')
                            <a href="#"
                                title="Preview"
                                class="hover:opacity-80"
                                @click.prevent="
                    previewUrl = '{{ route('documents.preview', [
                        'tenant' => request()->route('tenant'),
                        'document' => $doc->id
                    ]) }}';
                    open = true;
               ">
                                <img
                                    src="{{ asset('icons/documents/view.png') }}"
                                    alt="Preview"
                                    class="w-5 h-5 object-contain"
                                    loading="lazy">
                            </a>
                            @endif

                            {{-- DOWNLOAD --}}
                            <a href="{{ route('documents.download', [
                'tenant' => request()->route('tenant'),
                'document' => $doc->id
            ]) }}"
                                title="Download"
                                class="hover:opacity-80">
                                <img
                                    src="{{ asset('icons/documents/download.png') }}"
                                    alt="Download"
                                    class="w-5 h-5 object-contain"
                                    loading="lazy">
                            </a>

                            {{-- DELETE --}}
                            @if($canDelete)
                            <form
                                id="delete-document-{{ $doc->id }}"
                                method="POST"
                                action="{{ route('documents.destroy', ['tenant'=>request()->route('tenant'), 'document'=>$doc->id]) }}">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="button"
                                    title="Delete"
                                    @click="
            confirmFormId = 'delete-document-{{ $doc->id }}';
            confirmLabel = '{{ $doc->title }}';
            confirmOpen = true;
        ">
                                    <img src="{{ asset('icons/documents/delete.png') }}" class="w-5 h-5">
                                </button>
                            </form>

                            @endif

                        </div>
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-slate-500">
                        No documents available.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL BACKDROP --}}
    <div x-cloak
        x-show="open"
        class="fixed inset-0 bg-black/60 z-40"
        @click="open = false">
    </div>

    {{-- MODAL --}}
    <div x-cloak
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-6">

        <div
            @click.stop
            class="bg-white rounded-xl shadow-xl w-full max-w-5xl h-[85vh] flex flex-col">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-5 py-3 border-b">
                <h3 class="text-sm font-semibold text-slate-700">
                    Document Preview
                </h3>

                <button
                    @click="open = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full
                       hover:bg-slate-100 text-slate-500 hover:text-red-600 transition"
                    aria-label="Close preview">
                    ✕
                </button>
            </div>

            {{-- CONTENT --}}
            <div class="flex-1 bg-slate-50">
                <iframe
                    x-show="previewUrl"
                    :src="previewUrl"
                    class="w-full h-full border-0">
                </iframe>
            </div>

            {{-- FOOTER --}}
            <div class="px-5 py-3 border-t flex justify-end bg-white">
                <button
                    @click="open = false"
                    class="px-4 py-2 text-sm rounded-md
                       bg-slate-100 text-slate-700
                       hover:bg-slate-200 transition">
                    Close
                </button>
            </div>

        </div>
    </div>

    <x-confirm-delete />
</div>

@endsection