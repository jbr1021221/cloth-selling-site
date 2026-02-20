@extends('layouts.admin')

@section('title', 'Vendors')
@section('page-title', 'Vendors')

@section('content')
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-head">Name</th>
                    <th class="table-head">Contact</th>
                    <th class="table-head">Phone</th>
                    <th class="table-head">Categories</th>
                    <th class="table-head">Commission</th>
                    <th class="table-head">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="table-cell font-medium text-white">{{ $vendor->name }}</td>
                        <td class="table-cell text-gray-400">{{ $vendor->contact_person }}</td>
                        <td class="table-cell text-gray-400">{{ $vendor->phone }}</td>
                        <td class="table-cell">
                            <div class="flex flex-wrap gap-1">
                                @if(is_array($vendor->categories))
                                    @foreach($vendor->categories as $cat)
                                        <span class="badge bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 text-xs">{{ $cat }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="table-cell text-gray-300">{{ $vendor->commission_rate }}%</td>
                        <td class="table-cell">
                            <span class="badge {{ $vendor->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-red-500/20 text-red-400 border-red-500/30' }} border">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-cell text-center text-gray-600 py-12">No vendors found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t border-gray-800">{{ $vendors->links() }}</div>
</div>
@endsection
