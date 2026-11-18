<?php
require_once __DIR__ . '/../includes/validation.php';

$isCli = (PHP_SAPI === 'cli');

$tests = [
    [
        'name' => 'Valid Gift Card data',
        'input' => [
            'card_name' => 'AirAsia $75 Flight Pass',
            'card_type' => 'Travel',
            'card_value' => '75.00',
            'points_required' => '7500',
        ],
        'expectedValid' => true,
    ],
    [
        'name' => 'Blank Card Name',
        'input' => [
            'card_name' => '   ',
            'card_type' => 'Travel',
            'card_value' => '50.00',
            'points_required' => '5000',
        ],
        'expectedValid' => false,
    ],
    [
        'name' => 'Altered or invalid Card Type',
        'input' => [
            'card_name' => 'Mystery Voucher',
            'card_type' => 'InvalidCategory',
            'card_value' => '50.00',
            'points_required' => '5000',
        ],
        'expectedValid' => false,
    ],
    [
        'name' => 'Zero or negative Card Value',
        'input' => [
            'card_name' => 'Zero Value Card',
            'card_type' => 'Dining',
            'card_value' => '0',
            'points_required' => '1000',
        ],
        'expectedValid' => false,
    ],
    [
        'name' => 'Negative Card Value',
        'input' => [
            'card_name' => 'Negative Value Card',
            'card_type' => 'Dining',
            'card_value' => '-15.50',
            'points_required' => '1000',
        ],
        'expectedValid' => false,
    ],
    [
        'name' => 'Invalid Points Required (non-integer / zero)',
        'input' => [
            'card_name' => 'Decimal Points Card',
            'card_type' => 'Lifestyle',
            'card_value' => '25.00',
            'points_required' => '25.5',
        ],
        'expectedValid' => false,
    ],
    [
        'name' => 'Valid positive integer Card ID',
        'idInput' => '12',
        'expectedId' => 12,
    ],
    [
        'name' => 'Invalid non-numeric Card ID',
        'idInput' => 'abc',
        'expectedId' => null,
    ],
];

$passed = 0;
$total = count($tests);

if (!$isCli) {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Validation Tests</title></head><body style='font-family: sans-serif; padding: 2rem;'><h1>Validation Test Harness</h1><ul>";
}

foreach ($tests as $test) {
    if (array_key_exists('idInput', $test)) {
        $actualId = validateCardId($test['idInput']);
        $ok = ($actualId === $test['expectedId']);
    } else {
        $res = validateGiftCardData($test['input']);
        $ok = ($res['valid'] === $test['expectedValid']);
    }

    if ($ok) {
        $passed++;
    }
    $label = $ok ? 'PASS' : 'FAIL';

    if ($isCli) {
        echo "[$label] {$test['name']}" . PHP_EOL;
    } else {
        $color = $ok ? 'green' : 'red';
        echo "<li><strong style='color: {$color};'>[{$label}]</strong> " . htmlspecialchars($test['name'], ENT_QUOTES, 'UTF-8') . "</li>";
    }
}

if ($isCli) {
    echo PHP_EOL . "Passed {$passed} / {$total} tests." . PHP_EOL;
} else {
    echo "</ul><p><strong>Passed {$passed} / {$total} tests.</strong></p></body></html>";
}