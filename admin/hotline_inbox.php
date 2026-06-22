<?php
/**
 * HMI IT Telkom - HOTLINE INBOX
 */
$pageTitle = 'Hotline Inbox';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validateCSRF($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';
        if ($action === 'mark_read') {
            $pdo->prepare("UPDATE hotline_messages SET status='dibaca' WHERE id=?")->execute([(int) $_POST['id']]);
            flash('success', 'Pesan ditandai sudah dibaca.', 'success');
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM hotline_messages WHERE id=?")->execute([(int) $_POST['id']]);
            flash('success', 'Pesan dihapus.', 'success');
        }
    }
    redirect(adminUrl('/hotline_inbox.php'));
}

$messages = $pdo->query("SELECT * FROM hotline_messages ORDER BY created_at DESC")->fetchAll();
?>

<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding:16px 20px;border-bottom:1px solid #E0E0E0;"><strong>📬 Pesan Masuk (
            <?= count($messages) ?>)
        </strong></div>

    <?php if (empty($messages)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Belum ada pesan masuk.</div>
    <?php else: ?>
        <?php foreach ($messages as $m): ?>
            <div
                style="padding:16px 20px;border-bottom:1px solid #F5F5F5;<?= $m['status'] === 'baru' ? 'background:#FAFFF9;border-left:3px solid #1B5E20;' : '' ?>">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px;">
                    <div>
                        <strong>
                            <?= sanitize($m['nama_pengirim']) ?>
                        </strong>
                        <span style="font-size:0.8rem;color:#757575;margin-left:8px;">
                            <?= sanitize($m['email_pengirim']) ?>
                        </span>
                        <?php if ($m['status'] === 'baru'): ?>
                            <span
                                style="background:#E8F5E9;color:#1B5E20;font-size:0.7rem;padding:2px 8px;border-radius:50px;margin-left:8px;font-weight:600;">BARU</span>
                        <?php endif; ?>
                    </div>
                    <span style="font-size:0.8rem;color:#9E9E9E;">
                        <?= timeAgo($m['created_at']) ?>
                    </span>
                </div>
                <div style="font-weight:600;margin-bottom:6px;">
                    <?= sanitize($m['subjek']) ?>
                </div>
                <p style="color:#616161;font-size:0.9rem;line-height:1.6;margin-bottom:10px;">
                    <?= nl2br(sanitize($m['pesan'])) ?>
                </p>
                <?php if ($m['lampiran_path']): ?>
                    <a href="<?= asset('/' . $m['lampiran_path']) ?>" target="_blank" style="font-size:0.85rem;color:#1B5E20;">📎
                        Lihat
                        Lampiran</a>
                <?php endif; ?>
                <div style="display:flex;gap:8px;margin-top:10px;">
                    <?php if ($m['status'] === 'baru'): ?>
                        <form method="POST"><input type="hidden" name="action" value="mark_read"><input type="hidden" name="id"
                                value="<?= $m['id'] ?>">
                            <?= csrfField() ?><button class="btn btn-sm btn-primary" style="padding:4px 12px;font-size:0.8rem;">✅
                                Tandai Dibaca</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST"><input type="hidden" name="action" value="delete"><input type="hidden" name="id"
                            value="<?= $m['id'] ?>">
                        <?= csrfField() ?><button class="btn btn-danger btn-sm" data-confirm="Hapus pesan?"
                            style="padding:4px 12px;font-size:0.8rem;">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>