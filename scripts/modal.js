'use strict';

const dialog = document.querySelector('dialog');
// ダイアログを開く
document.getElementById('dialogOpen').addEventListener('click', function() {
    dialog.showModal();
});
// ダイアログを閉じる
document.getElementById('dialogClose').addEventListener('click', function() {
    dialog.close();
});






