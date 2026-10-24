@extends('layouts.app')

@section('title', 'Audit Log Aktivitas Sistem')
@section('page_title', 'Audit Log')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Audit Log Aktivitas Sistem (FR-90, FR-91)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Jejak audit akuntabilitas perubahan data, waktu, aksi, dan penanggung jawab</p>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4 font-semibold">Waktu</th>
                        <th class="py-3 px-4 font-semibold">Pengguna</th>
                        <th class="py-3 px-4 font-semibold">Aksi</th>
                        <th class="py-3 px-4 font-semibold">Entitas & ID</th>
                        <th class="py-3 px-4 font-semibold">IP Address</th>
                        <th class="py-3 px-4 font-semibold">Rincian Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-mono px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-700">
                            {{ $log->entity_type }} @if($log->entity_id) <span class="font-mono text-slate-400">#{{ $log->entity_id }}</span> @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500 font-mono text-[11px] whitespace-nowrap">{{ $log->ip_address ?? '-' }}</td>
                        <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                            @if($log->new_values)
                            <div class="max-w-md truncate" title="{{ json_encode($log->new_values) }}">
                                {{ json_encode($log->new_values) }}
                            </div>
                            @elseif($log->old_values)
                            <div class="max-w-md truncate" title="{{ json_encode($log->old_values) }}">
                                {{ json_encode($log->old_values) }}
                            </div>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">Belum ada catatan aktivitas di log audit.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
