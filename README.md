## Use Case Traceability

| Use case | PHP page or pages | Database table | Test evidence |
| :--- | :--- | :--- | :--- |
| View a List of Gift Cards | `card-list.php` | `GIFTCARD` | Opened `card-list.php` in the browser and confirmed all 20 seeded Gift Card records displayed with image, Card Name, Points Required, and detail links. |
| View Details of One Gift Card | `card-details.php` | `GIFTCARD` | Selected a Gift Card from `card-list.php` (`card-details.php?card_id=1`) and verified all attributes and Update/Delete/Return links rendered properly. |
| Add a Gift Card | `card-add.php` | `GIFTCARD` | Submitted valid Gift Card data via POST and confirmed the new record appeared on `card-list.php`. |
| Update a Gift Card | `card-update.php`, `card-details.php` | `GIFTCARD` | Edited an existing Gift Card's name, value, and points via POST and confirmed the updated values displayed on `card-details.php`. |
| Delete a Gift Card | `card-delete.php`, `card-list.php` | `GIFTCARD` | Confirmed deletion on `card-delete.php` via POST and verified the record was removed from `card-list.php`. |