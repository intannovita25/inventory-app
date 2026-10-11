<?php
// Helper untuk validasi input umum

function validateInt($value, $min = null, $max = null) {
    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    if ($filtered === false) {
        return false;
    }
    if ($min !== null && $filtered < $min) {
        return false;
    }
    if ($max !== null && $filtered > $max) {
        return false;
    }
    return $filtered;
}

function validateFloat($value, $min = null) {
    $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($filtered === false) {
        return false;
    }
    if ($min !== null && $filtered < $min) {
        return false;
    }
    return $filtered;
}
?>