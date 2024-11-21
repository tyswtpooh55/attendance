//勤務情報変更
document.addEventListener('DOMContentLoaded', function () {
    //休憩追加ボタンを取得
    const addBreakingBtn = document.getElementById('addBreakingBtn');

    //休憩入力フィールドを追加するコンテナを取得
    const addBreakingContainer = document.getElementById('addBreakingContainer');


    //休憩追加ボタンにクリックイベントを追加
    addBreakingBtn.addEventListener('click', function (event) {
        event.preventDefault(); //デフォルトのフォーム送信を防ぐ

        //休憩入力フィールドを作成
        const addBreakingFields = document.createElement('div');
        addBreakingFields.innerHTML = `
            <div class="form__wrap">
                <input type="time" name="breaking_in[]" class="form__record">
                <span class="hyphen">-</span>
                <input type="time" name="breaking_out[]" class="form__record">
                <button type="button" class="remove-breaking-field">DELETE</button>
            </div>
        `;

        //コンテナに休憩入力フィールドを追加
        addBreakingContainer.appendChild(addBreakingFields);

        //削除ボタンのイベントリスナーを追加
        const removeBtn = addBreakingFields.querySelector('.remove-breaking-field');
        removeBtn.addEventListener('click', function () {
            addBreakingFields.remove();
        });
    });
});
