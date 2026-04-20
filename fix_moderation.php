<?php
// Fix the moderation.php file by copying from moderation_new.php
copy(__DIR__ . '/views/backOffice/moderation_new.php', __DIR__ . '/views/backOffice/moderation.php');
echo "Done! Moderation.php has been updated.";
?>
