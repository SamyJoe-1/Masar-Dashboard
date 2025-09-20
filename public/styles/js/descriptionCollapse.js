function toggleDescription(jobId) {
    const description = document.getElementById('job-description-' + jobId);
    const button = description.nextElementSibling;
    const btnText = button.querySelector('.btn-text');
    const icon = button.querySelector('i');

    if (description.classList.contains('collapsed')) {
        // Show more
        description.classList.remove('collapsed');
        btnText.textContent = showLess;
        button.classList.add('expanded');
    } else {
        // Show less
        description.classList.add('collapsed');
        btnText.textContent = showMore;
        button.classList.remove('expanded');

        // Scroll back to top of description
        description.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
