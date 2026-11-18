<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$rawId = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? ($_POST['card_id'] ?? null)
    : ($_GET['card_id'] ?? null);

$cardId = validateCardId($rawId);
$card = null;
$errors = [];
$notFoundMessage = '';

if ($cardId === null) {
    $notFoundMessage = 'Invalid Gift Card identifier provided.';
} else {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT cardId, cardName, cardType, cardValue, points FROM GIFTCARD WHERE cardId = :cardId');
    $stmt->execute([':cardId' => $cardId]);
    $card = $stmt->fetch();

    if (!$card) {
        $notFoundMessage = 'The requested Gift Card could not be found.';
    }
}

$formValues = [
    'card_name' => $card ? (string)$card['cardName'] : '',
    'card_type' => $card ? (string)$card['cardType'] : '',
    'card_value' => $card ? number_format((float)$card['cardValue'], 2, '.', '') : '',
    'points_required' => $card ? (string)$card['points'] : '',
];

if ($card && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $formValues['card_name'] = trim((string)($_POST['card_name'] ?? ''));
    $formValues['card_type'] = trim((string)($_POST['card_type'] ?? ''));
    $formValues['card_value'] = trim((string)($_POST['card_value'] ?? ''));
    $formValues['points_required'] = trim((string)($_POST['points_required'] ?? ''));

    $result = validateGiftCardData($_POST);

    if ($result['valid']) {
        $updateStmt = $pdo->prepare(
            'UPDATE GIFTCARD
             SET cardName = :cardName,
                 cardType = :cardType,
                 cardValue = :cardValue,
                 points = :points
             WHERE cardId = :cardId'
        );
        $updateStmt->execute([
            ':cardName' => $result['data']['cardName'],
            ':cardType' => $result['data']['cardType'],
            ':cardValue' => $result['data']['cardValue'],
            ':points' => $result['data']['points'],
            ':cardId' => $cardId,
        ]);

        header('Location: card-details.php?card_id=' . $cardId . '&status=updated');
        exit();
    } else {
        $errors = $result['errors'];
    }
}

$pageTitle = 'Edit Gift Card - AirAsia Rewards';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card-panel">
    <?php if ($notFoundMessage !== ''): ?>
        <h2>Gift Card Not Available</h2>
        <p><?= htmlspecialchars($notFoundMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="action-buttons">
            <a href="card-list.php" class="btn btn-secondary">Return to Gift Card List</a>
        </div>
    <?php else: ?>
        <h2>Edit Gift Card (ID #<?= (int)$card['cardId'] ?>)</h2>

        <?php if (!empty($errors)): ?>
            <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 4px; margin-bottom: 1.25rem;">
                <strong>Please correct the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="card-update.php?card_id=<?= (int)$card['cardId'] ?>" method="post" novalidate>
            <input type="hidden" name="card_id" value="<?= (int)$card['cardId'] ?>">

            <div class="form-group">
                <label for="card_name">Card Name</label>
                <input type="text" id="card_name" name="card_name" class="form-control"
                       value="<?= htmlspecialchars($formValues['card_name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="card_type">Card Type</label>
                    <select id="card_type" name="card_type" class="form-control" required>
                        <option value="">Select Type...</option>
                        <?php foreach (ALLOWED_CARD_TYPES as $type): ?>
                            <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($formValues['card_type'] === $type) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="card_value">Card Value ($)</label>
                    <input type="number" id="card_value" name="card_value" class="form-control"
                           step="0.01" min="0.01"
                           value="<?= htmlspecialchars($formValues['card_value'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="points_required">Points Required</label>
                <input type="number" id="points_required" name="points_required" class="form-control"
                       min="1" step="1"
                       value="<?= htmlspecialchars($formValues['points_required'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="card-details.php?card_id=<?= (int)$card['cardId'] ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>