function showSection(section) {
    document.querySelectorAll('section').forEach(function (element) {
        element.style.display = element.getAttribute('data-section') === section ? null : 'none';
    })
}
