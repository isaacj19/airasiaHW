<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$cardId = validateCardId($_GET['card_id'] ?? null);
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

$updatedNotice = (isset($_GET['status']) && $_GET['status'] === 'updated');

$pageTitle = 'Gift Card Details - AirAsia Rewards';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($updatedNotice && $card): ?>
    <div class="card-panel" style="margin-bottom: 1.5rem; border-left: 5px solid #28a745; padding: 1rem 1.5rem;">
        <strong>Gift card updated successfully.</strong>
    </div>
<?php endif; ?>

<section class="card-panel">
    <?php if ($errorMessage !== ''): ?>
        <h2>Gift Card Not Available</h2>
        <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="action-buttons">
            <a href="card-list.php" class="btn btn-secondary">Return to Gift Card List</a>
        </div>
    <?php else: ?>
        <div class="details-container">
            <div class="details-image">
                <img src="images/gift-card.png" alt="<?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="details-info">
                <h2><?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?></h2>

                <div class="info-row">
                    <span class="info-label">Card ID:</span> <?= (int)$card['cardId'] ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Card Type:</span> <?= htmlspecialchars($card['cardType'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Card Value:</span> $<?= number_format((float)$card['cardValue'], 2) ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Points Required:</span> <?= number_format((int)$card['points']) ?> Points
                </div>

                <div class="action-buttons">
                    <a href="card-update.php?card_id=<?= (int)$card['cardId'] ?>" class="btn btn-primary">Update Gift Card</a>
                    <a href="card-delete.php?card_id=<?= (int)$card['cardId'] ?>" class="btn btn-danger">Delete Gift Card</a>
                    <a href="card-list.php" class="btn btn-secondary">Return to Gift Card List</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>