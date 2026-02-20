@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-gray-800">
        <form method="GET" action="{{ route('admin.users') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                   class="input text-sm w-64">
            <button type="submit" class="btn-primary text-sm px-4">Search</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-head">Name</th>
                    <th class="table-head">Email</th>
                    <th class="table-head">Phone</th>
                    <th class="table-head">Role</th>
                    <th class="table-head">Orders</th>
                    <th class="table-head">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="table-cell">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-white font-medium text-sm">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="table-cell text-gray-400">{{ $user->email }}</td>
                        <td class="table-cell text-gray-400">{{ $user->phone ?? '—' }}</td>
                        <td class="table-cell">
                            <span class="badge {{ $user->role === 'admin' ? 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30' : 'bg-gray-500/20 text-gray-400 border-gray-500/30' }} border">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="table-cell text-gray-300">{{ $user->orders_count }}</td>
                        <td class="table-cell text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-cell text-center text-gray-600 py-12">No customers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t border-gray-800">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
