@extends('layouts.tenant')

@section('title', 'Upload Document')

@section('content')

<div class="mx-auto w-full max-w-5xl px-4 py-8 md:py-10">

    {{-- PAGE HEADER --}}
    <div class="mb-5 rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Upload Document</h1>
                <p class="mt-1 text-sm text-slate-600">Add a new file and choose who can access it.</p>
            </div>

            <a
                href="{{ route('documents.index', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 transition hover:bg-white hover:text-slate-800">
                Back to Documents
            </a>
        </div>
    </div>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('documents.store', ['tenant' => request()->route('tenant')]) }}"
        class="space-y-5">
        @csrf

        @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">Please fix the following:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
            <div class="grid grid-cols-1 gap-5">
                {{-- TITLE --}}
                <div>
                    <label for="title" class="mb-1 block text-sm font-medium text-slate-700">
                        Title
                    </label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Document title"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FILE --}}
                <div>
                    <label for="file" class="mb-1 block text-sm font-medium text-slate-700">
                        File
                    </label>
                    <input
                        id="file"
                        type="file"
                        name="file"
                        required
                        class="w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm
                               file:mr-4 file:rounded-md file:border-0
                               file:bg-indigo-50 file:px-4 file:py-2
                               file:text-sm file:font-medium file:text-indigo-600
                               hover:file:bg-indigo-100">
                    @error('file')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- VISIBILITY --}}
                <div>
                    <label for="visibility" class="mb-1 block text-sm font-medium text-slate-700">
                        Visibility
                    </label>
                    <select
                        id="visibility"
                        name="visibility"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500">

                        {{-- Employee --}}
                        @if(auth('tenant')->user()->isEmployee())
                        <option value="private" @selected(old('visibility') === 'private')>Private (Only Me)</option>
                        @endif

                        {{-- Manager --}}
                        @if(auth('tenant')->user()->isManager())
                        <option value="private" @selected(old('visibility') === 'private')>Private (Only Me)</option>
                        <option value="team" @selected(old('visibility') === 'team')>Team</option>
                        @endif

                        {{-- Admin --}}
                        @if(auth('tenant')->user()->isAdmin())
                        <option value="private" @selected(old('visibility') === 'private')>Private (Only Me)</option>
                        <option value="org" @selected(old('visibility') === 'org')>Organization</option>
                        @endif

                    </select>
                    <p class="mt-1 text-xs text-slate-500">Choose who can access this document.</p>
                    @error('visibility')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a
                href="{{ route('documents.index', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                Upload Document
            </button>
        </div>
    </form>

</div>

@endsection
