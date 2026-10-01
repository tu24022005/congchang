/* Chuẩn hóa các giá trị trình bày động được truyền qua data attributes. */
(function () {
    'use strict';

    document.querySelectorAll('[data-inline-width]').forEach(function (element) {
        element.style.width = element.dataset.inlineWidth + '%';
    });
    document.querySelectorAll('[data-inline-transform]').forEach(function (element) {
        element.style.transform = element.dataset.inlineTransform;
    });
    document.querySelectorAll('[data-inline-color]').forEach(function (element) {
        element.style.setProperty('--rc', element.dataset.inlineColor);
        element.style.setProperty('--rb', element.dataset.inlineColorLight + '15');
    });
    document.querySelectorAll('[data-inline-columns]').forEach(function (element) {
        element.style.width = 'calc(78% / ' + element.dataset.inlineColumns + ')';
        element.style.minWidth = '250px';
    });
})();


/* Khởi tạo structured data sau khi view đã render. */
document.querySelectorAll('.jsonld-template').forEach(function (template) {
    var script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = template.content.textContent.trim();
    template.replaceWith(script);
});
