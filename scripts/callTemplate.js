'use strict';

const form = document.getElementById('templateForm');

form.addEventListener('submit', (event) => {
    event.preventDefault(); // フォーム送信を止める

    const formData = new FormData(form);

    fetch('app/callTemplateProcess.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.error) {
            return;
        }
        
        for (const [weekKey, info] of Object.entries(data)) {
            // h1~h23だけを取り出す
            for (let hour = 1; hour <= 23; hour++) {
                const hourKey = `h${hour}`;
                const value = info[hourKey];
                const hourClass = `hour${hour}`;

                // querySelectorAllで該当するselect要素を取得
                const targets = document.querySelectorAll(`.templateTarget.${info.week_days}.${hourClass}`);

                targets.forEach(selectEl => {
                // 例: valueを変更
                selectEl.value = value;

                // オプションを選択状態にする
                const optionToSelect = Array.from(selectEl.options).find(o => o.value === value);
                    if (optionToSelect) optionToSelect.selected = true;
                });
            }
        }

    })
    .catch(err => console.error('通信エラー:', err));
});




