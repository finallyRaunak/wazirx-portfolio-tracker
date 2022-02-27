<?php
if ($has_error) {
    $alertClass = 'alert-danger';
    $alertIcon = 'fa fa-fw fa-times-circle';
} else {
    $alertClass = 'alert-success';
    $alertIcon = 'fa fa-fw fa-check';
}
?>
<div class="alert <?= $alertClass; ?> d-flex align-items-center justify-content-between alert-dismissible" role="alert">
    <div class="flex-shrink-0">
        <i class="<?= $alertIcon; ?>"></i>
    </div>
    <div class="flex-grow-1 ms-3">
        <p class="mb-0"><?= $alert_message; ?></p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>