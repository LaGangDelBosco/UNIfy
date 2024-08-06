document.addEventListener('DOMContentLoaded', function() {
    const skipLink = document.querySelector('.skip-link');
    let tabCount = 0;

    function handleTabPress(event) {
        if (event.key === 'Tab') {
            tabCount++;
            if (tabCount === 1) {
                skipLink.classList.add('show');
            } else if (tabCount === 2) {
                skipLink.classList.remove('show');
                document.removeEventListener('keydown', handleTabPress);
            }
        }
    }

    document.addEventListener('keydown', handleTabPress);

    skipLink.addEventListener('blur', function() {
        skipLink.classList.remove('show');
        tabCount = 0;
    });
});