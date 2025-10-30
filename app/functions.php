<?php
// functions.php

// ランダムトークン生成
function generateToken($length = 16) {
    return bin2hex(random_bytes($length));
}

// HTML出力用のエスケープ
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
