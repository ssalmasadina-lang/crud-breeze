
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Berkas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Pesan berhasil berwarna hijau --}}
                @if (session('success'))
                    <div style="background-color: #d1fae5; color: #047857; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Tombol Unggah Berkas --}}
                <a href="{{ route('uploads.create') }}"
                   style="display: inline-block; background-color: #2563eb; color: white; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; text-decoration: none;">
                    + Unggah Berkas
                </a>

                {{-- Tabel Daftar Berkas --}}
                <table class="w-full border text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">No</th>
                            <th class="border p-2">Judul</th>
                            <th class="border p-2">Nama File</th>
                            <th class="border p-2">Tanggal Unggah</th>
                            <th class="border p-2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($uploads as $upload)
                            <tr>
                                <td class="border p-2">
                                    {{ $uploads->firstItem() + $loop->index }}
                                </td>

                                <td class="border p-2">
                                    {{ $upload->judul }}
                                </td>

                                <td class="border p-2">
                                    <a href="{{ asset('storage/' . $upload->filepath) }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       style="color: #2563eb; text-decoration: underline;">
                                        {{ $upload->filename }}
                                    </a>
                                </td>

                                <td class="border p-2">
                                    {{ $upload->created_at->format('d-m-Y H:i') }}
                                </td>

                                {{-- Aksi: Lihat, Edit, Hapus --}}
                                <td class="border p-2">
                                    <a href="{{ route('uploads.show', $upload) }}"
                                       style="color: #2563eb; text-decoration: none;">
                                        Lihat
                                    </a>

                                    |

                                    <a href="{{ route('uploads.edit', $upload) }}"
                                       style="color: #d97706; text-decoration: none;">
                                        Edit
                                    </a>

                                    |

                                    <form action="{{ route('uploads.destroy', $upload) }}"
                                          method="POST"
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                style="color: #dc2626; background: none; border: none; padding: 0; cursor: pointer;"
                                                onclick="return confirm('Yakin ingin menghapus berkas ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="border p-2 text-center">
                                    Belum ada berkas yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $uploads->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
