@extends('layouts.app')

@section('title', 'Manajemen User - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen User</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
                Tambah User
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Username</th>
                    <th scope="col">Role</th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr>
                        <td>{{ $u->id_user }}</td>
                        <td>{{ $u->nama_user }}</td>
                        <td>{{ $u->role }}</td>
                        <td>{{ $u->jabatan }}</td>
                        <td>
                            <a href="{{ route('users.edit', $u->id_user) }}" class="btn btn-sm btn-warning">Edit</a>
                            @if($u->id_user != auth()->id())
                                <form action="{{ route('users.destroy', $u->id_user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
