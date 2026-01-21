@extends('layouts.tenant')

@section('title', 'Upload Document')

@section('content')

<div class="min-h-screen flex justify-center py-10 px-4">

    <form method="POST"
        enctype="multipart/form-data"
        action="/org/{{ request()->route('tenant') }}/documents/upload"
        class="w-full max-w-2xl">
        @csrf

        <div class="bg-white rounded-2xl p-8 space-y-6 ring-1 ring-slate-200/70">

            {{-- HEADER --}}
            <div>
                <h2 class="text-lg font-medium text-slate-900 tracking-tight">
                    Upload Document
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Add a document and set its visibility
                </p>
            </div>

            {{-- TITLE --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    required
                    placeholder="Document title"
                    class="w-full rounded-lg border border-slate-200 bg-white
                           px-3 py-2 text-sm
                           focus:outline-none focus:ring-0
                           focus:border-indigo-500
                           focus:bg-indigo-50/30
                           transition">
            </div>

            {{-- FILE --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    File
                </label>

                <input
                    type="file"
                    name="file"
                    required
                    class="w-full text-sm cursor-pointer
                           file:mr-4 file:px-4 file:py-2
                           file:rounded-lg file:border-0
                           file:bg-indigo-50 file:text-indigo-600
                           file:text-sm file:font-medium
                           hover:file:bg-indigo-100
                           focus:outline-none">
            </div>

            {{-- VISIBILITY --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Visibility
                </label>

                <select
                    name="visibility"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-white
                           px-3 py-2 text-sm
                           focus:outline-none focus:ring-0
                           focus:border-indigo-500
                           focus:bg-indigo-50/30
                           transition">

                    {{-- Employee --}}
                    @if(auth('tenant')->user()->role === 'employee')
                    <option value="private">Private (Only Me)</option>
                    @endif

                    {{-- Manager --}}
                    @if(auth('tenant')->user()->role === 'manager')
                    <option value="private">Private (Only Me)</option>
                    <option value="team">Team</option>
                    @endif

                    {{-- Admin --}}
                    @if(auth('tenant')->user()->role === 'admin')
                    <option value="private">Private (Only Me)</option>
                    <option value="org">Organization</option>
                    @endif

                </select>

                <p class="text-xs text-slate-400 mt-1">
                    Choose who can access this document
                </p>
            </div>

            {{-- ACTION --}}
            <div class="pt-4 flex items-center gap-3">
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg
                           text-sm font-medium
                           hover:bg-indigo-700 transition">
                    Upload Document
                </button>

                <a
                    href="{{ route('documents.index', ['tenant' => request()->route('tenant')]) }}"
                    class="px-5 py-2.5 rounded-lg border border-slate-200
                           text-sm text-slate-600
                           hover:bg-slate-50 hover:text-slate-800 transition">
                    Cancel
                </a>
            </div>

        </div>
    </form>

</div>

@endsection