(function () {
    'use strict';

    var root = document.getElementById('acp_topictranslatesingle');
    if (!root) {
        return;
    }

    root.querySelectorAll('[data-tts-search-input]').forEach(function (search) {
        search.addEventListener('input', function () {
            var query = normalize(this.value);
            var group = this.getAttribute('data-tts-search-input');
            root.querySelectorAll('[data-tts-search-item="' + group + '"]').forEach(function (label) {
                label.hidden = query && normalize(label.getAttribute('data-tts-label')).indexOf(query) === -1;
            });
        });
    });

    root.querySelectorAll('[data-tts-languages]').forEach(function (button) {
        button.addEventListener('click', function () {
            var action = this.getAttribute('data-tts-languages');
            var selector = 'input[name="languages[]"]';

            if (action === 'popular') {
                root.querySelectorAll(selector).forEach(function (checkbox) {
                    checkbox.checked = !!checkbox.closest('[data-tts-language-list="popular"]');
                });
                updateSelectionCounts();
                return;
            }

            root.querySelectorAll(selector).forEach(function (checkbox) {
                checkbox.checked = action === 'all';
            });
            updateSelectionCounts();
        });
    });

    root.querySelectorAll('[data-tts-forums]').forEach(function (button) {
        button.addEventListener('click', function () {
            var checked = this.getAttribute('data-tts-forums') === 'all';
            root.querySelectorAll('input[name="enabled_forums[]"]:not(:disabled)').forEach(function (checkbox) {
                checkbox.checked = checked;
            });
            updateSelectionCounts();
        });
    });

    root.querySelectorAll('[data-tts-multiselect]').forEach(function (details) {
        details.addEventListener('toggle', function () {
            if (!this.open) {
                return;
            }

            root.querySelectorAll('[data-tts-multiselect][open]').forEach(function (otherDetails) {
                if (otherDetails !== details) {
                    otherDetails.open = false;
                }
            });
        });
    });

    root.addEventListener('change', function (event) {
        if (event.target && event.target.hasAttribute('data-tts-count-group')) {
            updateSelectionCounts();
        }
    });

    var form = root.querySelector('form');
    if (form) {
        form.addEventListener('reset', function () {
            window.setTimeout(updateSelectionCounts, 0);
        });
    }

    updateSelectionCounts();

    function updateSelectionCounts() {
        root.querySelectorAll('[data-tts-selection-count]').forEach(function (counter) {
            var group = counter.getAttribute('data-tts-selection-count');
            var total = 0;

            root.querySelectorAll('[data-tts-count-group="' + group + '"]:not(:disabled)').forEach(function (control) {
                if ((control.type === 'checkbox' && control.checked) || (control.tagName === 'SELECT' && control.value)) {
                    total += 1;
                }
            });

            counter.textContent = String(total);
        });
    }

    function normalize(value) {
        var normalized = String(value || '').toLowerCase();
        return normalized.normalize ? normalized.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : normalized;
    }
})();
