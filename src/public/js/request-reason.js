document.addEventListener('DOMContentLoaded', function () {
    const reasonSelect = document.getElementById('reason');

    const reasonForChangeIsOtherContainer = document.getElementById('reasonForChangeIsOtherContainer');

    reasonSelect.addEventListener('change', function (event) {
        reasonForChangeIsOtherContainer.innerHTML = '';

        if (event.target.value === 'other') {
            const textareaForReason = document.createElement('div');
            textareaForReason.innerHTML = `
                <textarea name="other-reason" class="reason__textarea"></textarea>
            `;

            reasonForChangeIsOtherContainer.appendChild(textareaForReason);
        }
    });
})
