@extends('layouts.tenant')

@section('title', 'Documents')

@section('content')

@php
$docCount = $documents->count();
@endphp

<div
    x-data="{
        confirmOpen: false,
        confirmFormId: '',
        confirmLabel: '',
        open: false,
        previewUrl: null,
        search: '',
        visibilityFilter: '',
        init() {
            this.$watch('open', value => {
                document.body.classList.toggle('overflow-hidden', value);
            });
        },
        matches(text, visibility) {
            const query = this.search.trim().toLowerCase();
            const matchesText = query === '' || text.includes(query);
            const matchesVisibility = this.visibilityFilter === '' || visibility === this.visibilityFilter;
            return matchesText && matchesVisibility;
        },
        clearFilters() {
            this.search = '';
            this.visibilityFilter = '';
        }
    }"
    class="space-y-5">

    {{-- PAGE HEADER --}}
    <div class="rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Documents</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $docCount }} {{ \Illuminate\Support\Str::plural('document', $docCount) }} available in this workspace
                </p>
            </div>

            <a
                href="{{ route('documents.upload', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                Upload Document
            </a>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="w-full md:w-72">
                <label for="doc-search" class="sr-only">Search documents</label>
                <input
                    id="doc-search"
                    x-model="search"
                    type="text"
                    placeholder="Search by title or uploader"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="w-full md:w-48">
                <label for="doc-visibility-filter" class="sr-only">Filter by visibility</label>
                <select
                    id="doc-visibility-filter"
                    x-model="visibilityFilter"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Visibility</option>
                    <option value="private">Private</option>
                    <option value="team">Team</option>
                    <option value="company">Company</option>
                </select>
            </div>

            <button
                type="button"
                @click="clearFilters"
                class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50">
                Clear
            </button>
        </div>
    </div>

    {{-- TABLE (DESKTOP) --}}
    <div class="hidden overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm md:block">
        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-indigo-900">Title</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Uploaded By</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Visibility</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($documents as $doc)
                @php
                $user = auth('tenant')->user();
                $canDelete =
                ($user->role === 'employee' && $doc->visibility === 'private' && $doc->uploaded_by === $user->id)
                || ($user->role === 'manager' && in_array($doc->visibility, ['private', 'team']) && $doc->uploaded_by === $user->id)
                || ($user->role === 'admin');
                @endphp

                <tr
                    x-show="matches(@js(strtolower($doc->title . ' ' . ($doc->uploader?->name ?? 'system'))), '{{ $doc->visibility }}')"
                    x-cloak
                    class="odd:bg-white even:bg-slate-50 transition hover:bg-slate-100">
                    <td class="p-3 font-medium text-slate-800">{{ $doc->title }}</td>

                    <td class="p-3 text-slate-700">
                        {{ $doc->uploader?->name ?? 'System' }}<br>
                        <span class="text-xs text-slate-500">{{ $doc->created_at->format('d M Y') }}</span>
                    </td>

                    <td class="p-3">
                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                            {{ ucfirst($doc->visibility) }}
                        </span>
                    </td>

                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            @if(str_starts_with($doc->mime_type, 'image/') || $doc->mime_type === 'application/pdf')
                            <button
                                type="button"
                                title="Preview"
                                aria-label="Preview document"
                                class="rounded p-1 transition hover:bg-slate-200"
                                @click="
                                    previewUrl = '{{ route('documents.preview', ['tenant' => request()->route('tenant'), 'document' => $doc->id]) }}';
                                    open = true;
                                ">
                                <img src="{{ asset('icons/documents/view.png') }}" alt="Preview" class="h-5 w-5 object-contain" loading="lazy">
                            </button>
                            @endif

                            <a
                                href="{{ route('documents.download', ['tenant' => request()->route('tenant'), 'document' => $doc->id]) }}"
                                title="Download"
                                aria-label="Download document"
                                class="rounded p-1 transition hover:bg-slate-200">
                                <img src="{{ asset('icons/documents/download.png') }}" alt="Download" class="h-5 w-5 object-contain" loading="lazy">
                            </a>

                            @if($canDelete)
                            <form
                                id="delete-document-{{ $doc->id }}"
                                method="POST"
                                action="{{ route('documents.destroy', ['tenant' => request()->route('tenant'), 'document' => $doc->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    title="Delete"
                                    aria-label="Delete document"
                                    class="rounded p-1 transition hover:bg-red-50"
                                    @click="
                                        confirmFormId = 'delete-document-{{ $doc->id }}';
                                        confirmLabel = '{{ $doc->title }}';
                                        confirmOpen = true;
                                    ">
                                    <img src="{{ asset('icons/documents/delete.png') }}" class="h-5 w-5" alt="Delete">
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">No documents available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARDS (MOBILE) --}}
    <div class="space-y-3 md:hidden">
        @forelse($documents as $doc)
        @php
        $user = auth('tenant')->user();
        $canDelete =
        ($user->role === 'employee' && $doc->visibility === 'private' && $doc->uploaded_by === $user->id)
        || ($user->role === 'manager' && in_array($doc->visibility, ['private', 'team']) && $doc->uploaded_by === $user->id)
        || ($user->role === 'admin');
        @endphp
        <article
            x-show="matches(@js(strtolower($doc->title . ' ' . ($doc->uploader?->name ?? 'system'))), '{{ $doc->visibility }}')"
            x-cloak
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <h3 class="font-semibold text-slate-800">{{ $doc->title }}</h3>
                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                    {{ ucfirst($doc->visibility) }}
                </span>
            </div>

            <p class="mt-2 text-sm text-slate-600">
                {{ $doc->uploader?->name ?? 'System' }} • {{ $doc->created_at->format('d M Y') }}
            </p>

            <div class="mt-3 flex items-center gap-2">
                @if(str_starts_with($doc->mime_type, 'image/') || $doc->mime_type === 'application/pdf')
                <button
                    type="button"
                    title="Preview"
                    aria-label="Preview document"
                    class="rounded p-1 transition hover:bg-slate-200"
                    @click="
                        previewUrl = '{{ route('documents.preview', ['tenant' => request()->route('tenant'), 'document' => $doc->id]) }}';
                        open = true;
                    ">
                    <img src="{{ asset('icons/documents/view.png') }}" alt="Preview" class="h-5 w-5 object-contain">
                </button>
                @endif

                <a
                    href="{{ route('documents.download', ['tenant' => request()->route('tenant'), 'document' => $doc->id]) }}"
                    title="Download"
                    aria-label="Download document"
                    class="rounded p-1 transition hover:bg-slate-200">
                    <img src="{{ asset('icons/documents/download.png') }}" alt="Download" class="h-5 w-5 object-contain">
                </a>

                @if($canDelete)
                <form
                    id="mobile-delete-document-{{ $doc->id }}"
                    method="POST"
                    action="{{ route('documents.destroy', ['tenant' => request()->route('tenant'), 'document' => $doc->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        title="Delete"
                        aria-label="Delete document"
                        class="rounded p-1 transition hover:bg-red-50"
                        @click="
                            confirmFormId = 'mobile-delete-document-{{ $doc->id }}';
                            confirmLabel = '{{ $doc->title }}';
                            confirmOpen = true;
                        ">
                        <img src="{{ asset('icons/documents/delete.png') }}" class="h-5 w-5" alt="Delete">
                    </button>
                </form>
                @endif
            </div>
        </article>
        @empty
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-500 shadow-sm">
            No documents available.
        </div>
        @endforelse
    </div>

    {{-- PREVIEW MODAL --}}
    <div
        x-cloak
        x-show="open"
        @keydown.escape.window="open = false"
        @click.self="open = false"
        class="fixed inset-0 z-[100] h-screen w-screen overflow-y-auto bg-black/60">

        <div class="flex min-h-full items-center justify-center p-4 md:p-6">
            <div
                @click.stop
                class="flex h-[85vh] w-full max-w-5xl flex-col rounded-xl border border-slate-200 bg-white shadow-xl"
                x-transition>

                <div class="flex items-center justify-between border-b px-5 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Document Preview</h3>
                    <button
                        @click="open = false"
                        class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-red-600"
                        aria-label="Close preview">
                        ✕
                    </button>
                </div>

                <div class="flex-1 bg-slate-50">
                    <iframe
                        x-show="previewUrl"
                        :src="previewUrl"
                        class="h-full w-full border-0"></iframe>
                </div>

                <div class="flex justify-end border-t bg-white px-5 py-3">
                    <button
                        @click="open = false"
                        class="rounded-md bg-slate-100 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-delete />
</div>

@endsection
