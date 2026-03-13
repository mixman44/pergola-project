document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.ctrl-block-header').forEach(header => {
        header.addEventListener('click', () => {
            header.closest('.ctrl-block').classList.toggle('is-open');
        });
    });
});
