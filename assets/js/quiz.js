/**
 * HMI IT Telkom - Quiz Engine JavaScript
 * Timer countdown & form handling
 */

document.addEventListener('DOMContentLoaded', function () {
    // ============================================================
    // QUIZ TIMER
    // ============================================================
    const timerEl = document.getElementById('quiz-timer');
    const quizForm = document.getElementById('quiz-form');

    if (timerEl && quizForm) {
        let minutes = parseInt(timerEl.getAttribute('data-minutes')) || 15;
        let totalSeconds = minutes * 60;

        const updateTimer = () => {
            const m = Math.floor(totalSeconds / 60);
            const s = totalSeconds % 60;
            timerEl.textContent = `⏱ ${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;

            if (totalSeconds <= 60) {
                timerEl.style.background = '#C62828';
                timerEl.style.animation = 'pulse 1s infinite';
            }

            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                alert('Waktu habis! Jawaban akan dikirim otomatis.');
                quizForm.submit();
                return;
            }

            totalSeconds--;
        };

        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);
    }

    // ============================================================
    // QUIZ OPTION SELECTION HIGHLIGHT
    // ============================================================
    document.querySelectorAll('.quiz-option').forEach(option => {
        option.addEventListener('click', function () {
            // Unselect siblings
            const parent = this.closest('.quiz-question');
            parent.querySelectorAll('.quiz-option').forEach(opt => {
                opt.style.borderColor = '';
                opt.style.background = '';
            });
            // Highlight selected
            this.style.borderColor = 'var(--hmi-green)';
            this.style.background = 'var(--hmi-green-50)';
            // Check the radio
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // ============================================================
    // QUIZ FORM VALIDATION - check all questions answered
    // ============================================================
    if (quizForm) {
        quizForm.addEventListener('submit', function (e) {
            const questions = document.querySelectorAll('.quiz-question');
            let allAnswered = true;

            questions.forEach((q, index) => {
                const checked = q.querySelector('input[type="radio"]:checked');
                if (!checked) {
                    allAnswered = false;
                    q.style.borderColor = '#C62828';
                    q.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    q.style.borderColor = '';
                }
            });

            if (!allAnswered) {
                e.preventDefault();
                alert('Mohon jawab semua pertanyaan sebelum mengirim.');
            }
        });
    }
});
