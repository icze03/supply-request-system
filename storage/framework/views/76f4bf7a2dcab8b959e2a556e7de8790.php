<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Voucher - <?php echo e($request->serial_number); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #e5e7eb;
            color: #000;
        }

        /* ── Screen wrapper ── */
        .screen-wrapper {
            max-width: 820px;
            margin: 0 auto;
            padding: 24px;
        }

        /* ── Toolbar ── */
        .no-print {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-bottom: 20px;
        }
        .btn-print {
            padding: 8px 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-back {
            padding: 8px 20px;
            background: #fff;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        /* ── A4 page ── */
        .voucher-page {
            width: 210mm;
            height: 297mm;           /* exact A4 height */
            overflow: hidden;        /* nothing bleeds past the page */
            background: #fff;
            border: 2px solid #000;
            padding: 10mm 12mm 8mm 12mm;
            margin: 0 auto 32px auto;

            /* flex column so the table section can stretch */
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .voucher-header {
            text-align: center;
            margin-bottom: 5px;
            flex-shrink: 0;
        }
        .voucher-header h1 {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .voucher-header p {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1px;
        }

        /* ── Meta grid ── */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 6px;
            flex-shrink: 0;
        }
        .meta-field { margin-bottom: 3px; }
        .meta-label {
            font-size: 8px;
            color: #555;
            display: block;
            margin-bottom: 1px;
        }
        .meta-value {
            font-size: 10px;
            font-weight: 600;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            min-height: 13px;
            display: block;
        }

        /* ── Table wrapper stretches to fill remaining height ── */
        .table-wrapper {
            flex: 1;              /* grow to fill space between meta and signatures */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-bottom: 6px;
        }

        /* ── Items table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;

            /* make the table itself stretch inside the wrapper */
            flex: 1;
            height: 100%;          /* fills .table-wrapper */
        }

        .items-table thead {
            display: table-header-group;  /* keep header sticky at top */
        }

        .items-table tbody {
            /* tbody fills remaining height so rows auto-size */
            height: 100%;
        }

        .items-table th {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            background: #f3f4f6;
            letter-spacing: 0.3px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 9px;
            vertical-align: middle;
        }

        /* Data rows have a natural height; filler rows stretch */
        .items-table tr.data-row td  { height: auto; }
        .items-table tr.filler-row   { height: 100%; } /* grows to fill space */
        .items-table tr.filler-row td { height: 100%; }

        .col-code  { width: 14%; }
        .col-desc  { width: 31%; }
        .col-req   { width: 7%;  }
        .col-alloc { width: 7%;  }
        .col-unit  { width: 8%;  }
        .col-price { width: 13%; }
        .col-total { width: 13%; }
        /* remaining 7% shared by borders */

        .text-center { text-align: center; }
        .text-right  { text-align: right;  }

        .partial-cell {
            background-color: #fefce8 !important;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .grand-total-row td {
            border-top: 2px solid #000;
            font-weight: 700;
            font-size: 9px;
        }

        /* ── Signature section ── */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            flex-shrink: 0;       /* never let signatures get squeezed */
        }
        .sig-block { margin-bottom: 5px; }
        .sig-label {
            font-size: 8px;
            color: #555;
            display: block;
            margin-bottom: 1px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            min-height: 15px;
            font-size: 9px;
            padding-bottom: 1px;
        }
        .sig-date-label {
            font-size: 8px;
            color: #555;
            display: block;
            margin-top: 3px;
            margin-bottom: 1px;
        }

        /* ── Print ── */
        @media print {
            body { background: #fff; }

            .no-print { display: none !important; }

            .screen-wrapper {
                padding: 0;
                max-width: none;
            }

            .voucher-page {
                border: 1px solid #000 !important;
                margin: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                padding: 10mm 12mm 8mm 12mm !important;
                page-break-after: always;
                box-shadow: none !important;
            }

            .voucher-page:last-child {
                page-break-after: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>
<div class="screen-wrapper">

    <!-- Toolbar -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Print Voucher</button>
        <a class="btn-back" href="<?php echo e(route('admin.releases.index')); ?>">← Back to Releases</a>
    </div>

    <?php
        /*
         * We don't use a fixed $itemsPerPage anymore.
         * Instead we page at a generous ceiling (e.g. 20) so that on a
         * single-page request the table simply has one filler row that
         * stretches to fill remaining space. The CSS does the real work.
         *
         * For multi-page requests keep a safe per-page cap so content
         * never overflows the physical page. 18 lines is safe at 9px row height.
         */
        $itemsPerPage = 18;

        $items      = $request->request_type === 'standard' ? $request->items : collect([]);
        $totalItems = $request->request_type === 'standard' ? $items->count() : 1;
        $totalPages = $request->request_type === 'standard'
            ? max(1, ceil($totalItems / $itemsPerPage))
            : 1;

        // Grand total across ALL items
        $grandTotal = 0;
        if ($request->request_type === 'standard') {
            foreach ($request->items as $item) {
                $allocated   = $item->allocated_quantity ?? $item->quantity;
                $unitCost    = $item->supply->unit_cost ?? 0;
                $grandTotal += $allocated * $unitCost;
            }
        }
    ?>

    <?php for($page = 0; $page < $totalPages; $page++): ?>
        <?php
            $pageItems  = $request->request_type === 'standard'
                ? $items->slice($page * $itemsPerPage, $itemsPerPage)
                : collect([]);

            $isLastPage = ($page === $totalPages - 1);
        ?>

        <div class="voucher-page">

            
            <div class="voucher-header">
                <h1>CALIFORNIA CLOTHING INC.</h1>
                <p>Supplies Issue Slip</p>
            </div>

            
            <div class="meta-grid">
                <div>
                    <div class="meta-field">
                        <span class="meta-label">Requisitioning Section / Department:</span>
                        <span class="meta-value"><?php echo e($request->department->name); ?></span>
                    </div>
                    <div class="meta-field">
                        <span class="meta-label">Cost Center:</span>
                        <span class="meta-value"><?php echo e($request->department->cost_center ?? $request->department->code); ?></span>
                    </div>
                </div>
                <div>
                    <div class="meta-field">
                        <span class="meta-label">Control No.:</span>
                        <span class="meta-value" style="font-family:monospace;"><?php echo e($request->serial_number); ?></span>
                    </div>
                    <?php if($request->ro_number): ?>
                    <div class="meta-field">
                        <span class="meta-label">RO Number:</span>
                        <span class="meta-value" style="font-family:monospace;"><?php echo e($request->ro_number); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($totalPages > 1): ?>
                    <div class="meta-field">
                        <span class="meta-label">Page:</span>
                        <span class="meta-value"><?php echo e($page + 1); ?> of <?php echo e($totalPages); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="col-code">Item Code</th>
                            <th class="col-desc">Description</th>
                            <th class="col-req">Req</th>
                            <th class="col-alloc">Alloc</th>
                            <th class="col-unit">Unit</th>
                            <th class="col-price">Unit Price</th>
                            <th class="col-total">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if($request->request_type === 'standard'): ?>

                            
                            <?php $__currentLoopData = $pageItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $allocated = $item->allocated_quantity ?? $item->quantity;
                                    $isPartial = $allocated < $item->quantity;
                                    $unitCost  = $item->supply->unit_cost ?? null;
                                    $lineTotal = $unitCost !== null ? $allocated * $unitCost : null;
                                ?>
                                <tr class="data-row">
                                    <td class="text-center" style="font-family:monospace;font-size:8px;"><?php echo e($item->item_code); ?></td>
                                    <td><?php echo e($item->item_name); ?></td>
                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                    <td class="text-center <?php echo e($isPartial ? 'partial-cell' : ''); ?>"><?php echo e($allocated); ?></td>
                                    <td class="text-center"><?php echo e($item->supply->unit ?? 'pcs'); ?></td>
                                    <td class="text-right"><?php echo e($unitCost !== null ? '₱' . number_format($unitCost, 2) : ''); ?></td>
                                    <td class="text-right"><?php echo e($lineTotal !== null ? '₱' . number_format($lineTotal, 2) : ''); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <tr class="filler-row">
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>

                            
                            <?php if($isLastPage): ?>
                                <tr class="grand-total-row">
                                    <td colspan="5"></td>
                                    <td class="text-right">Grand Total</td>
                                    <td class="text-right">
                                        <?php echo e($grandTotal > 0 ? '₱' . number_format($grandTotal, 2) : '—'); ?>

                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php else: ?>
                            
                            <?php if($page === 0): ?>
                                <tr class="data-row">
                                    <td class="text-center" style="font-family:monospace;font-size:8px;">CUSTOM</td>
                                    <td><?php echo e($request->special_item_description); ?></td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">LOT</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr class="filler-row">
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            
            <div class="signature-grid">
                <div>
                    <div class="sig-block">
                        <span class="sig-label">Prepared by:</span>
                        <div class="sig-line"></div>
                        <span class="sig-date-label">Date:</span>
                        <div class="sig-line"><?php echo e($request->created_at->format('M d, Y  h:i A')); ?></div>
                    </div>
                    <div class="sig-block">
                        <span class="sig-label">Approved by:</span>
                        <div class="sig-line"><?php echo e($request->managerApprover->name ?? ''); ?></div>
                        <span class="sig-date-label">Date:</span>
                        <div class="sig-line"><?php echo e($request->manager_approved_at ? $request->manager_approved_at->format('M d, Y  h:i A') : ''); ?></div>
                    </div>
                </div>
                <div>
                    <div class="sig-block">
                        <span class="sig-label">Issued by:</span>
                        <div class="sig-line"><?php echo e($request->adminReleaser->name ?? ''); ?></div>
                        <span class="sig-date-label">Date:</span>
                        <div class="sig-line"><?php echo e($request->admin_released_at ? $request->admin_released_at->format('M d, Y  h:i A') : ''); ?></div>
                    </div>
                    <div class="sig-block">
                        <span class="sig-label">Received by:</span>
                        <div class="sig-line"></div>
                        <span class="sig-date-label">Date:</span>
                        <div class="sig-line"></div>
                    </div>
                </div>
            </div>

        </div>
    <?php endfor; ?>

</div>
</body>
</html><?php /**PATH C:\laragon\www\supply-request-system\resources\views/admin/voucher.blade.php ENDPATH**/ ?>