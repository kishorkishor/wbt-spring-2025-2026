<?php $user = $_SESSION['user']; $isEdit = !empty($editing); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Handler Dashboard &mdash; Hitman Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="app-body">

<!-- Navbar -->
<header class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="index.php?page=handler">
            <span class="brand-icon">&#127919;</span>
            <span>HitSys</span>
        </a>
        <div class="nav-user">
            <span class="user-pill">
                <span class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($user['name']) ?></span>
                    <span class="user-role">Handler</span>
                </span>
            </span>
            <a href="index.php?page=logout" class="btn-logout">Logout</a>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Manage Contracts</h1>
            <p class="page-sub">Add, edit, search and remove contracts in the registry</p>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php $messages = ['added' => 'Contract added successfully.',
                           'updated' => 'Contract updated successfully.',
                           'deleted' => 'Contract deleted successfully.'];
              $msg = $messages[$_GET['msg']] ?? null; ?>
        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ============ Add / Edit Form ============ -->
    <div class="card form-card">
        <h3 class="card-title">
            <?= $isEdit ? '&#9998; Edit Contract (#' . intval($editing['id']) . ')' : '+ Add New Contract' ?>
        </h3>
        <form method="POST"
              action="index.php?page=handler&action=<?= $isEdit ? 'update&id=' . intval($editing['id']) : 'add' ?>"
              class="form" novalidate>
            <div class="field-row">
                <div class="field">
                    <label for="target_name">Target Name</label>
                    <input type="text" id="target_name" name="target_name"
                           value="<?= htmlspecialchars($editing['target_name'] ?? '') ?>"
                           placeholder="e.g. Victor Novikov" required>
                </div>
                <div class="field">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location"
                           value="<?= htmlspecialchars($editing['location'] ?? '') ?>"
                           placeholder="e.g. Paris, France" required>
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="bounty">Bounty ($)</label>
                    <input type="number" id="bounty" name="bounty" min="0"
                           value="<?= htmlspecialchars($editing['bounty'] ?? '') ?>"
                           placeholder="0" required>
                </div>
                <div class="field">
                    <label for="fee">Handler Fee ($)</label>
                    <input type="number" id="fee" name="fee" step="0.01" min="0"
                           value="<?= htmlspecialchars($editing['fee'] ?? '') ?>"
                           placeholder="0.00" required>
                </div>
            </div>
            <div class="form-actions">
                <?php if ($isEdit): ?>
                    <a href="index.php?page=handler" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Contract</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary">Save Contract</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- ============ Contracts Table ============ -->
    <div class="card">
        <div class="card-toolbar">
            <div class="search-wrap">
                <span class="search-icon">&#128269;</span>
                <input type="text" id="searchInput" class="search-input"
                       placeholder="Search by target name or location...">
            </div>
            <span class="badge" id="resultCount"><?= count($contracts) ?> total</span>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Target Name</th>
                        <th>Location</th>
                        <th>Bounty</th>
                        <th>Handler Fee</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($contracts)): ?>
                        <tr><td colspan="6" class="empty">No contracts yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($contracts as $i => $contract): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($contract['target_name']) ?></td>
                                <td><?= htmlspecialchars($contract['location']) ?></td>
                                <td>$<?= number_format($contract['bounty'], 0) ?></td>
                                <td>$<?= number_format($contract['fee'], 2) ?></td>
                                <td class="text-right">
                                    <a class="btn-sm btn-edit"
                                       href="index.php?page=handler&action=edit&id=<?= $contract['id'] ?>">Edit</a>
                                    <a class="btn-sm btn-delete"
                                       href="index.php?page=handler&action=delete&id=<?= $contract['id'] ?>"
                                       onclick="return confirm('Delete this contract?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="footer">&copy; <?= date('Y') ?> Hitman Management System</footer>

<!-- =========== Inline AJAX search =========== -->
<script>
(function () {
    var input    = document.getElementById('searchInput');
    var body     = document.getElementById('tableBody');
    var counter  = document.getElementById('resultCount');
    var timer;

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function render(rows) {
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="6" class="empty">No matching results.</td></tr>';
            counter.textContent = '0 results';
            return;
        }
        var html = '';
        rows.forEach(function (c, i) {
            html +=
                '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + esc(c.target_name) + '</td>' +
                    '<td>' + esc(c.location) + '</td>' +
                    '<td>$' + parseInt(c.bounty).toLocaleString() + '</td>' +
                    '<td>$' + parseFloat(c.fee).toFixed(2) + '</td>' +
                    '<td class="text-right">' +
                        '<a class="btn-sm btn-edit" href="index.php?page=handler&action=edit&id=' + c.id + '">Edit</a>' +
                        '<a class="btn-sm btn-delete" href="index.php?page=handler&action=delete&id=' + c.id +
                        '" onclick="return confirm(\'Delete this contract?\')">Delete</a>' +
                    '</td>' +
                '</tr>';
        });
        body.innerHTML = html;
        counter.textContent = rows.length + (input.value.trim() ? ' results' : ' total');
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetch('index.php?page=ajax&type=contract&q=' + encodeURIComponent(input.value.trim()),
                  { credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(render)
                .catch(function (e) { console.error(e); });
        }, 200);
    });
})();
</script>

</body>
</html>
