'use strict';

// ターゲットのvalueを変更
function changeChoices(targetClass, changeValue) {
    const changeElements = document.querySelectorAll(`.${targetClass}`);
    changeElements.forEach(target => {
        target.value = changeValue;
    });
}

// 選択されているvalueを判別して次のvalueを設定
function convertValue(nowValue) {
    let newValue;
    switch(nowValue) {
        case 'optionNum1':
            newValue = {'value':'×', 'optionNum':2};
            break;
        case 'optionNum2':
            newValue = {'value':'△', 'optionNum':3};
            break;
        case 'optionNum3':
            newValue = {'value':'-', 'optionNum':4};
            break;
        case 'optionNum4':
            newValue = {'value':'〇', 'optionNum':1};
            break;
        default:
            newValue = {'value':'〇', 'optionNum':1};
            break;
    }
    return newValue;
}

// 曜日ボタン・時間ボタンにイベント設定
function changeProcess(dayOrHourBtns) {
    dayOrHourBtns.forEach(pushedBtn => {
        pushedBtn.addEventListener('click', () => {
            console.log('click');
            const targetClass = pushedBtn.id;
            const nowValue = pushedBtn.dataset.value;
            const changeValue = convertValue(nowValue);
            changeChoices(targetClass, changeValue['value'])
            pushedBtn.dataset.value = `optionNum${changeValue['optionNum']}`;        
        });
    });
}

// 曜日ボタンと時間ボタンを取得
const getWeekDaysBtn = document.querySelectorAll('.dayChangeAll');
const getHoursBtn = document.querySelectorAll('.hourChangeAll');
changeProcess(getWeekDaysBtn);
changeProcess(getHoursBtn);
