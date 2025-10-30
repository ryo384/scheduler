'use strict';

const deleteBtn = document.getElementById('eventDelete');
if(deleteBtn) {
    deleteBtn.addEventListener('click', function() {
        if(confirm('削除すると元には戻せません。\r\n本当に削除しますか？')) {
            const token = deleteBtn.dataset.token;
            window.location.href = '/phpPortfolio/app/eventDelete.php?token=' + token;        
        }
    });
}


