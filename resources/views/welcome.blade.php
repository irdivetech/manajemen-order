@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    <!-- Header Area -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Welcome Back, {{ Auth::user()?->name ?? 'Admin' }}! 👋</h2>
            <p class="text-slate-400 text-sm mt-1">Here's what's happening on the production floor today.</p>
        </div>
        <button class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl font-medium shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Order
        </button>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Card 1 -->
        <div class="glass-dark rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-slate-400 text-sm font-medium mb-1">Total Orders</p>
                <h3 class="text-3xl font-bold text-white">1,284</h3>
                <p class="text-emerald-400 text-xs font-medium mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    +12% from last month
                </p>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="glass-dark rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-slate-400 text-sm font-medium mb-1">Active Production</p>
                <h3 class="text-3xl font-bold text-indigo-400">142</h3>
                <p class="text-indigo-300 text-xs font-medium mt-2 flex items-center gap-1">
                    Items currently in sewing
                </p>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="glass-dark rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-slate-400 text-sm font-medium mb-1">Nearing Deadline</p>
                <h3 class="text-3xl font-bold text-amber-400">18</h3>
                <p class="text-amber-300 text-xs font-medium mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Due in next 3 days
                </p>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="glass-dark rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-slate-400 text-sm font-medium mb-1">Monthly Revenue</p>
                <h3 class="text-3xl font-bold text-emerald-400">$45.2K</h3>
                <p class="text-emerald-300 text-xs font-medium mt-2 flex items-center gap-1">
                    All paid invoices
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2 glass-dark rounded-2xl border border-slate-800/50 flex flex-col">
            <div class="p-6 border-b border-slate-800/50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
                <a href="#" class="text-sm text-indigo-400 hover:text-indigo-300 font-medium">View All →</a>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-900/30 text-slate-400">
                            <th class="px-6 py-4 font-medium">Order #</th>
                            <th class="px-6 py-4 font-medium">Customer</th>
                            <th class="px-6 py-4 font-medium">Product</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <!-- Dummy Row 1 -->
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4"><span class="font-medium text-indigo-400">ORD-2026-0102</span></td>
                            <td class="px-6 py-4 text-slate-300">PT Maju Jaya</td>
                            <td class="px-6 py-4 text-slate-400">Polo Shirts (100 pcs)</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                    Sewing
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-emerald-400">$2,450.00</td>
                        </tr>
                        <!-- Dummy Row 2 -->
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4"><span class="font-medium text-indigo-400">ORD-2026-0101</span></td>
                            <td class="px-6 py-4 text-slate-300">CV Abadi</td>
                            <td class="px-6 py-4 text-slate-400">Jackets (50 pcs)</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    Fabric Cutting
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-emerald-400">$3,200.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="glass-dark rounded-2xl border border-slate-800/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-6">Production Updates</h3>
            <div class="space-y-6">
                
                <div class="relative pl-6 pb-6 border-l border-slate-700/50 last:border-0 last:pb-0">
                    <div class="absolute -left-[5px] top-1 w-2.5 h-2.5 bg-indigo-500 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.5)]"></div>
                    <p class="text-sm font-medium text-slate-200">ORD-2026-0102 moved to Sewing</p>
                    <p class="text-xs text-slate-500 mt-1">By Alice Admin • 2 hours ago</p>
                </div>

                <div class="relative pl-6 pb-6 border-l border-slate-700/50 last:border-0 last:pb-0">
                    <div class="absolute -left-[5px] top-1 w-2.5 h-2.5 bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <p class="text-sm font-medium text-slate-200">ORD-2026-0098 Shipped</p>
                    <p class="text-xs text-slate-500 mt-1">By Bob Owner • 5 hours ago</p>
                </div>

                <div class="relative pl-6 pb-6 border-l border-slate-700/50 last:border-0 last:pb-0">
                    <div class="absolute -left-[5px] top-1 w-2.5 h-2.5 bg-amber-500 rounded-full shadow-[0_0_10px_rgba(245,158,11,0.5)]"></div>
                    <p class="text-sm font-medium text-slate-200">Payment received for ORD-2026-0099</p>
                    <p class="text-xs text-slate-500 mt-1">System • Yesterday</p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
