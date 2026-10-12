<?php $title = '- Segment Requests'; ?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Segment Requests (Long NR)</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Laboratory bay reservations, specialized hardware segment allocations, and faculty class bookings</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/segment-requests/create" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>+ Request for Segment</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/segment-requests" class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <select name="laboratory_id" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">All Laboratories</option>
                    <?php foreach ($laboratories as $lab): ?>
                        <option value="<?= $lab['id'] ?>" <?= ((string)$labId === (string)$lab['id']) ? 'selected' : '' ?>><?= escape($lab['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($status === 'pending') ? 'selected' : '' ?>>Pending Review</option>
                    <option value="approved" <?= ($status === 'approved') ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($status === 'rejected') ? 'selected' : '' ?>>Declined</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold transition-colors">
                    Filter Requests
                </button>
                <a href="/segment-requests" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium">Reset</a>
            </div>
        </div>
    </form>

    <!-- Requests Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-semibold uppercase">
                        <th class="px-5 py-3.5 rounded-l-2xl">Long NR Reference</th>
                        <th class="px-4 py-3.5">Requester & Email</th>
                        <th class="px-4 py-3.5">Target Lab & Segment</th>
                        <th class="px-4 py-3.5">Date & Time</th>
                        <th class="px-4 py-3.5">Users / Capacity</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 rounded-r-2xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($requests)): ?>
                        <tr><td colspan="7" class="py-12 text-center text-gray-400">No segment requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($requests as $sr): ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-primary-600 dark:text-primary-400">
                                <?= escape($sr['request_number']) ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-gray-900 dark:text-white"><?= escape($sr['requester_name']) ?></span>
                                <p class="text-[11px] text-gray-400"><?= escape($sr['requester_email']) ?></p>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">
                                <span class="font-semibold text-gray-900 dark:text-white"><?= escape($sr['segment_name']) ?></span>
                                <p class="text-[11px] text-gray-400"><?= escape($sr['laboratory_name']) ?> (<?= escape($sr['room_number']) ?>)</p>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">
                                <?= escape($sr['request_date']) ?>
                                <?php if (!empty($sr['start_time'])): ?>
                                    <br><span class="text-[11px] text-gray-400"><?= escape($sr['start_time']) ?> - <?= escape($sr['end_time']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-gray-800 dark:text-gray-200">
                                <?= $sr['expected_users'] ?> users
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    <?= $sr['status'] === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($sr['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                                    <?= ucfirst($sr['status']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <?php if ($sr['status'] === 'pending'): ?>
                                    <form method="POST" action="/segment-requests/<?= $sr['id'] ?>/approve" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-semibold">Approve</button>
                                    </form>
                                    <form method="POST" action="/segment-requests/<?= $sr['id'] ?>/reject" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-[10px] font-semibold">Decline</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-[11px] text-gray-400 italic"><?= ucfirst($sr['status']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
