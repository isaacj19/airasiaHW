<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$errors = [];
$formValues = [
    'card_name' => '',
    'card_type' => '',
    'card_value' => '',
    'points_required' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formValues['card_name'] = trim((string)($_POST['card_name'] ?? ''));
    $formValues['card_type'] = trim((string)($_POST['card_type'] ?? ''));
    $formValues['card_value'] = trim((string)($_POST['card_value'] ?? ''));
    $formValues['points_required'] = trim((string)($_POST['points_required'] ?? ''));

    $result = validateGiftCardData($_POST);

    if ($result['valid']) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO GIFTCARD (cardName, cardType, cardValue, points)
             VALUES (:cardName, :cardType, :cardValue, :points)'
        );
        $stmt->execute([
            ':cardName' => $result['data']['cardName'],
            ':cardType' => $result['data']['cardType'],
            ':cardValue' => $result['data']['cardValue'],
            ':points' => $result['data']['points'],
        ]);

        header('Location: card-list.php?status=added');
        exit();
    } else {
        $errors = $result['errors'];
    }
}

$pageTitle = 'Add New Gift Card - AirAsia Rewards';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card-panel">
    <h2>Add New Gift Card Catalog Item</h2>

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

    <form action="card-add.php" method="post" novalidate>
        <div class="form-group">
            <label for="card_name">Card Name</label>
            <input type="text" id="card_name" name="card_name" class="form-control"
                   value="<?= htmlspecialchars($formValues['card_name'], ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="e.g., AirAsia $50 Travel Pass" required>
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
                       value="<?= htmlspecialchars($formValues['card_value'], ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="50.00" required>
            </div>
        </div>

        <div class="form-group">
            <label for="points_required">Points Required</label>
            <input type="number" id="points_required" name="points_required" class="form-control"
                   min="1" step="1"
                   value="<?= htmlspecialchars($formValues['points_required'], ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="5000" required>
        </div>

        <p class="form-note">Note: Card ID is automatically generated.</p>

        <div class="action-buttons">
            <button type="submit" class="btn btn-primary">Add Gift Card</button>
            <a href="card-list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>