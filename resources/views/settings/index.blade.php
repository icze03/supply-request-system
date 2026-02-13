@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
            <p class="mt-2 text-gray-600">Configure system-wide settings</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            <!-- Leave Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Leave Settings</h2>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Default Annual Leave (days)
                            </label>
                            <input type="number" name="default_annual_leave" value="{{ $settings['default_annual_leave'] ?? 15 }}" min="0"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Default Sick Leave (days)
                            </label>
                            <input type="number" name="default_sick_leave" value="{{ $settings['default_sick_leave'] ?? 10 }}" min="0"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Default Emergency Leave (days)
                            </label>
                            <input type="number" name="default_emergency_leave" value="{{ $settings['default_emergency_leave'] ?? 5 }}" min="0"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Notice Days
                            </label>
                            <input type="number" name="min_notice_days" value="{{ $settings['min_notice_days'] ?? 3 }}" min="0"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Days in advance to request leave</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Max Consecutive Days
                            </label>
                            <input type="number" name="max_consecutive_days" value="{{ $settings['max_consecutive_days'] ?? 14 }}" min="1"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Maximum days for single request</p>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="allow_weekend_requests" value="1" 
                                   {{ ($settings['allow_weekend_requests'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Allow weekend leave requests</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Email Notifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Email Notifications</h2>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="notify_on_request" value="1" 
                               {{ ($settings['notify_on_request'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Notify managers on new leave request</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="notify_on_approval" value="1" 
                               {{ ($settings['notify_on_approval'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Notify employees on request approval</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="notify_on_rejection" value="1" 
                               {{ ($settings['notify_on_rejection'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Notify employees on request rejection</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="reminder_before_leave" value="1" 
                               {{ ($settings['reminder_before_leave'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Send reminder 1 day before leave starts</span>
                    </label>
                </div>
            </div>

            <!-- System Preferences -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">System Preferences</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name
                        </label>
                        <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'My Company' }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fiscal Year Start
                        </label>
                        <select name="fiscal_year_start" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                            <option value="{{ $month }}" {{ ($settings['fiscal_year_start'] ?? 'January') === $month ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="require_manager_approval" value="1" 
                                   {{ ($settings['require_manager_approval'] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Require manager approval for all leave requests</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="carry_forward_unused" value="1" 
                                   {{ ($settings['carry_forward_unused'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Carry forward unused leave to next year</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection