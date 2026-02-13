<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Voucher - {{ $request->serial_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        
        .no-print {
            display: none !important;
        }
        
        .voucher-page {
            border: 1px solid black !important;
            box-shadow: none !important;
            page-break-after: always;
            padding: 15mm !important;
            margin: 0 !important;
        }
        
        .voucher-page:last-child {
            page-break-after: avoid;
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }
        
        table {
            page-break-inside: avoid;
        }
    }

    /* Ensure borders print correctly */
    table,
    th,
    td {
        border-color: black !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <div class="max-w-4xl mx-auto p-8">
            <!-- Print Button (hidden when printing) -->
            <div class="no-print mb-6 flex justify-end gap-3">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                    Print Voucher
                </button>
                <a href="{{ route('admin.releases.index') }}" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50 font-medium">
                    ← Back to Releases
                </a>
            </div>

            @php
                // Calculate how many pages we need for standard requests
                $itemsPerPage = 10;
                $items = $request->request_type === 'standard' ? $request->items : collect([]);
                $totalItems = $request->request_type === 'standard' ? $items->count() : 1;
                $totalPages = $request->request_type === 'standard' ? max(1, ceil($totalItems / $itemsPerPage)) : 1;
            @endphp

            @for($page = 0; $page < $totalPages; $page++)
                @php
                    $startIndex = $page * $itemsPerPage;
                    $endIndex = min($startIndex + $itemsPerPage, $totalItems);
                    $pageItems = $request->request_type === 'standard' 
                        ? $items->slice($startIndex, $itemsPerPage) 
                        : collect([]);
                    $emptyRows = $itemsPerPage - $pageItems->count();
                    $isLastPage = ($page === $totalPages - 1);
                @endphp

                <!-- Voucher Content -->
                <div class="voucher-page bg-white border-2 border-black p-8 {{ $page > 0 ? 'mt-8' : '' }}">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h1 class="text-xl font-bold uppercase mb-0">CALIFORNIA CLOTHING INC.</h1>
                        <p class="text-xs font-semibold uppercase mt-0">SUPPLIES ISSUE SLIP</p>
                    </div>

                    <!-- Top Section: Department and Control Number -->
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div class="space-y-2">
                            <div>
                                <label class="text-xs">Requisitioning Section/Dept:</label>
                                <div class="border-b border-black">
                                    <span class="text-sm">{{ $request->department->name }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs">Cost Center:</label>
                                <div class="border-b border-black">
                                    <span class="text-sm">{{ $request->department->code }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs">Control No.:</label>
                            <div class="border-b border-black">
                                <span class="font-mono text-sm font-semibold">{{ $request->serial_number }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-4">
                        <table class="w-full border border-black">
                            <thead>
                                <tr class="border-b border-black">
                                    <th class="border-r border-black px-2 py-1.5 text-xs font-bold uppercase text-left w-20">ITEM CODE</th>
                                    <th class="border-r border-black px-2 py-1.5 text-xs font-bold uppercase text-left">DESCRIPTION</th>
                                    <th class="border-r border-black px-2 py-1.5 text-xs font-bold uppercase text-center w-12">QTY</th>
                                    <th class="border-r border-black px-2 py-1.5 text-xs font-bold uppercase text-center w-16">UNIT</th>
                                    <th class="border-r border-black px-2 py-1.5 text-xs font-bold uppercase text-center w-20">UNIT PRICE</th>
                                    <th class="px-2 py-1.5 text-xs font-bold uppercase text-center w-20">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($request->request_type === 'standard')
                                    {{-- Display items for this page --}}
                                    @foreach($pageItems as $item)
                                        <tr class="border-b border-black">
                                            <td class="border-r border-black px-2 py-1 text-xs">{{ $item->item_code }}</td>
                                            <td class="border-r border-black px-2 py-1 text-xs">{{ $item->item_name }}</td>
                                            <td class="border-r border-black px-2 py-1 text-xs text-center">{{ $item->quantity }}</td>
                                            <td class="border-r border-black px-2 py-1 text-xs text-center">{{ $item->supply->unit ?? 'pcs' }}</td>
                                            <td class="border-r border-black px-2 py-1 text-xs text-center"></td>
                                            <td class="px-2 py-1 text-xs text-center"></td>
                                        </tr>
                                    @endforeach
                                    
                                    {{-- Fill remaining rows with empty lines --}}
                                    @for($i = 0; $i < $emptyRows; $i++)
                                        <tr class="border-b border-black">
                                            <td class="border-r border-black px-2 py-1 text-xs h-7">&nbsp;</td>
                                            <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                            <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                            <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                            <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                            <td class="px-2 py-1 text-xs">&nbsp;</td>
                                        </tr>
                                    @endfor
                                @else
                                    {{-- Special/Custom request - only on first page --}}
                                    @if($page === 0)
                                        <tr class="border-b border-black">
                                            <td class="border-r border-black px-2 py-1 text-xs">CUSTOM</td>
                                            <td class="border-r border-black px-2 py-1 text-xs">{{ $request->special_item_description }}</td>
                                            <td class="border-r border-black px-2 py-1 text-xs text-center">1</td>
                                            <td class="border-r border-black px-2 py-1 text-xs text-center">LOT</td>
                                            <td class="border-r border-black px-2 py-1 text-xs text-center"></td>
                                            <td class="px-2 py-1 text-xs text-center"></td>
                                        </tr>
                                        @for($i = 0; $i < 9; $i++)
                                            <tr class="border-b border-black">
                                                <td class="border-r border-black px-2 py-1 text-xs h-7">&nbsp;</td>
                                                <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                                <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                                <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                                <td class="border-r border-black px-2 py-1 text-xs">&nbsp;</td>
                                                <td class="px-2 py-1 text-xs">&nbsp;</td>
                                            </tr>
                                        @endfor
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Signature Section - Displayed on all pages with data -->
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs">Prepared:</label>
                                <div class="border-b border-black h-6"></div>
                                <label class="text-xs mt-1 block">Date:</label>
                                <div class="border-b border-black h-6">
                                    <span class="text-xs">{{ $request->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs">Approved:</label>
                                <div class="border-b border-black h-6">
                                    <span class="text-xs">{{ $request->managerApprover->name ?? '' }}</span>
                                </div>
                                <label class="text-xs mt-1 block">Date:</label>
                                <div class="border-b border-black h-6">
                                    <span class="text-xs">{{ $request->manager_approved_at ? $request->manager_approved_at->format('Y-m-d H:i:s') : '' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs">Issued:</label>
                                <div class="border-b border-black h-6">
                                    <span class="text-xs">{{ $request->adminReleaser->name ?? '' }}</span>
                                </div>
                                <label class="text-xs mt-1 block">Date:</label>
                                <div class="border-b border-black h-6">
                                    <span class="text-xs">{{ $request->admin_released_at ? $request->admin_released_at->format('Y-m-d H:i:s') : '' }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs">Received:</label>
                                <div class="border-b border-black h-6"></div>
                                <label class="text-xs mt-1 block">Date:</label>
                                <div class="border-b border-black h-6"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</body>
</html>