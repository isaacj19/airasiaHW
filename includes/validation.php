<?php
const ALLOWED_CARD_TYPES = ['Travel', 'Dining', 'Entertainment', 'Lifestyle'];
const MAX_CARD_NAME_LENGTH = 100;

function validateCardId($rawId): ?int
{
    if ($rawId === null || $rawId === '' || is_array($rawId)) {
        return null;
    }

    $id = filter_var($rawId, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    return ($id === false) ? null : (int)$id;
}

function validateGiftCardData(array $input): array
{
    $errors = [];

    $cardName = trim((string)($input['card_name'] ?? ''));
    $cardType = trim((string)($input['card_type'] ?? ''));
    $rawValue = trim((string)($input['card_value'] ?? ''));
    $rawPoints = trim((string)($input['points_required'] ?? ''));

    // 1. Card Name: present and <= 100 chars (per VARCHAR(100) in schema)
    if ($cardName === '') {
        $errors[] = 'Card Name is required.';
    } elseif (mb_strlen($cardName) > MAX_CARD_NAME_LENGTH) {
        $errors[] = 'Card Name must not exceed ' . MAX_CARD_NAME_LENGTH . ' characters.';
    }

    // 2. Card Type: must be in allowed list
    if (!in_array($cardType, ALLOWED_CARD_TYPES, true)) {
        $errors[] = 'Card Type must be one of: ' . implode(', ', ALLOWED_CARD_TYPES) . '.';
    }

    // 3. Card Value: must be a positive number (> 0)
    if ($rawValue === '' || !is_numeric($rawValue) || (float)$rawValue <= 0) {
        $errors[] = 'Card Value must be a positive number greater than 0.';
    }

    // 4. Points Required: must be a positive whole number (integer > 0)
    $pointsInt = filter_var($rawPoints, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    if ($pointsInt === false) {
        $errors[] = 'Points Required must be a positive whole number.';
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'data' => [
            'cardName' => $cardName,
            'cardType' => $cardType,
            'cardValue' => empty($errors) ? round((float)$rawValue, 2) : $rawValue,
            'points' => ($pointsInt !== false) ? (int)$pointsInt : $rawPoints,
        ],
    ];
}