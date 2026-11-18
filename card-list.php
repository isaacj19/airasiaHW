<?php
require_once __DIR__ . '/includes/db.php';

$pdo = getDbConnection();
$stmt = $pdo->query('SELECT cardId, cardName, cardType, cardValue, points FROM GIFTCARD ORDER BY cardId ASC');
$cards = $stmt->fetchAll();

$statusMessage = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'added') {
        $statusMessage = 'New gift card added successfully.';
    } elseif ($_GET['status'] === 'deleted') {
        $statusMessage = 'Gift card deleted successfully.';
    }
}

$pageTitle = 'AirAsia Rewards Catalog';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($statusMessage !== ''): ?>
    <div class="card-panel" style="margin-bottom: 1.5rem; border-left: 5px solid #28a745; padding: 1rem 1.5rem;">
        <strong><?= htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8') ?></strong>
    </div>
<?php endif; ?>

<div class="catalog-header">
    <h2>Available Rewards (<?= count($cards) ?>)</h2>
    <div>
        <a href="card-add.php" class="btn btn-primary">Add Gift Card</a>
    </div>
</div>

<?php if (empty($cards)): ?>
    <section class="card-panel">
        <p>No gift cards are currently available in the catalog.</p>
    </section>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($cards as $card): ?>
            <div class="card-item">
                <a href="card-details.php?card_id=<?= (int)$card['cardId'] ?>">
                    <img src="images/gift-card.png" alt="<?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?>">
                </a>
                <div class="card-item-body">
                    <h3>
                        <a href="card-details.php?card_id=<?= (int)$card['cardId'] ?>">
                            <?= htmlspecialchars($card['cardName'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </h3>
                    <p class="points-tag"><?= number_format((int)$card['points']) ?> Points Needed</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>