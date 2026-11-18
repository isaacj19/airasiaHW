<?php
if (!isset($pageTitle)) {
    $pageTitle = 'AirAsia Rewards Catalog';
}
if (!isset($headerHeading)) {
    $headerHeading = 'AirAsia Rewards Catalog';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($headerHeading, ENT_QUOTES, 'UTF-8') ?></h1>
        <?php require_once __DIR__ . '/nav.php'; ?>
    </header>
    <main>