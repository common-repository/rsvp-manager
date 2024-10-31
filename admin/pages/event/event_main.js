document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-links a');
    const contents = document.querySelectorAll('.tab-content .tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all tabs and contents
            tabs.forEach(tab => tab.parentElement.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            // Add active class to the clicked tab and corresponding content
            this.parentElement.classList.add('active');
            const target = document.querySelector(this.getAttribute('href'));
            target.classList.add('active');

            // Add the tab to the url so in case of refresh to load the right tab.
            const tab_parameter_value = this.getAttribute('href');
            if (tab_parameter_value && tab_parameter_value.startsWith('#')) {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab_parameter_value.substring(1));
                window.history.replaceState({}, '', url.toString());
            }
        });
    });
});