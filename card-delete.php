<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$rawId = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? ($_POST['card_id'] ?? null)
    : ($_GET['card_id'] ?? null);

$cardId = validateCardId($rawId);
$card = null;
$errorMessage = '';

if ($cardId === null) {
    $errorMessage = 'Invalid Gift Card identifier provided.';
} else {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT cardId, cardName, cardType, cardValue, points FROM GIFTCARD WHERE cardId = :cardId');
    $stmt->execute([':cardId' => $cardId]);
    $card = $stmt->fetch();

    if (!$card) {
        $errorMessage = 'The requested Gift Card could not be found.';
    }
}

if ($card && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $deleteStmt = $pdo->prepare('DELETE FROM GIFTCARD WHERE cardId = :cardId');
    $deleteStmt->execute([':cardId' => $cardId]);

    header('Location: card-list.php?status=deleted');
    exit();
}

$pageTitle = 'Delete Gift Card - AirAsia Rewards';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card-panel">
    <?php if ($errorMessage !== ''): ?>
        <h2>Gift Card Not Available</h2>
        <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="action-buttons">
            <a href="card-list.php" class="btn btn-secondary">Return to Gift Card List</a>
        </div>
    <?php else: ?>
        <h2>Confirm Gift Card Deletion</h2>
        <p>Are you sure you want to permanently delete the following gift card? This action cannot be undone.</p>

        <div style="background: #f8f9fa; border: 1px solid var(--border-color); border-radius: 6px; padding: 1.25rem; margin: 1.25rem 0;">
            <div class="info-row">
                <span class="info-label">Card ID:</span> <?= (int)$card['cardId'] ?>
            </div>
            <div class="info-row">
                <span class="info-label">Card Name:</span> <?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="info-row">
                <span class="info-label">Card Type:</span> <?= htmlspecialchars($card['cardType'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="info-row">
                <span class="info-label">Card Value:</span> $<?= number_format((float)$card['cardValue'], 2) ?>
            </div>
            <div class="info-row" style="margin-bottom: 0;">
                <span class="info-label">Points Required:</span> <?= number_format((int)$card['points']) ?> Points
            </div>
        </div>

        <form action="card-delete.php?card_id=<?= (int)$card['cardId'] ?>" method="post">
            <input type="hidden" name="card_id" value="<?= (int)$card['cardId'] ?>">
            <input type="hidden" name="confirm_delete" value="1">
            <div class="action-buttons">
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="card-details.php?card_id=<?= (int)$card['cardId'] ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>