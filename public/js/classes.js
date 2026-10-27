// Class list pages: refresh the grid after changes and fill the edit dialog.

import { errorMessage, toast } from './core.js';

export function initClassGrid() {
    const grid = document.getElementById('classGrid');
    if (!grid) return;

    document.addEventListener('classes-changed', () => {
        $.getJSON(grid.dataset.source)
            .done(({ html, count }) => {
                grid.innerHTML = html; // server-rendered Blade partial, already escaped
                const counter = document.querySelector('[data-class-count]');
                if (counter) counter.textContent = `${count} active ${count === 1 ? 'class' : 'classes'}`;
            })
            .fail((xhr) => toast(errorMessage(xhr, 'Could not refresh your classes. Reload the page.'), 'error'));
    });

    document.getElementById('editClassModal')?.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        const form = event.target.querySelector('form');
        if (!trigger || !form) return;

        form.setAttribute('action', trigger.dataset.action);
        form.elements.classname.value = trigger.dataset.classname ?? '';
        form.elements.section.value = trigger.dataset.section ?? '';
        form.elements.subject.value = trigger.dataset.subject ?? '';
    });
}
