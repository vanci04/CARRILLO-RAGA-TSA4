'use strict';
(() => {
  const rows = [...document.querySelectorAll('[data-task-row]')];
  const filterButtons = [...document.querySelectorAll('[data-task-filter]')];
  const search = document.querySelector('#task-search');
  const groups = [...document.querySelectorAll('[data-date-group]')];
  let status = 'all';
  function filter() {
    const query = search?.value.trim().toLocaleLowerCase() || '';
    let visible = 0;
    for (const row of rows) {
      row.hidden = (status !== 'all' && row.dataset.status !== status) || !row.dataset.title.includes(query);
      if (!row.hidden) visible++;
    }
    for (const group of groups) group.hidden = ![...group.querySelectorAll('[data-task-row]')].some(row => !row.hidden);
    const empty = document.querySelector('#no-task-results');
    if (empty) empty.hidden = visible > 0;
    const count = document.querySelector('#visible-count');
    if (count) count.textContent = `Showing ${visible} of ${rows.length} tasks`;
  }
  for (const button of filterButtons) button.addEventListener('click', () => {
    status = button.dataset.taskFilter;
    for (const other of filterButtons) {
      other.classList.toggle('selected', button === other);
      other.setAttribute('aria-pressed', button === other ? 'true' : 'false');
    }
    filter();
  });
  search?.addEventListener('input', filter);
  const dialog = document.querySelector('#delete-dialog');
  let pendingForm;
  for (const form of document.querySelectorAll('[data-delete-form]')) form.addEventListener('submit', event => {
    if (typeof dialog?.showModal !== 'function') {
      if (!window.confirm(`Remove “${form.dataset.taskName}” permanently?`)) event.preventDefault();
      return;
    }
    event.preventDefault(); pendingForm = form;
    document.querySelector('#delete-task-name').textContent = form.dataset.taskName;
    dialog.showModal(); document.querySelector('#cancel-delete').focus();
  });
  document.querySelector('#cancel-delete')?.addEventListener('click', () => dialog.close());
  document.querySelector('#confirm-delete')?.addEventListener('click', event => {
    if (!pendingForm) return;
    event.target.disabled = true;
    HTMLFormElement.prototype.submit.call(pendingForm);
  });
  // Native forms keep task actions usable without JavaScript. Lock only after validation passes.
  for (const form of document.querySelectorAll('form:not([data-delete-form])')) form.addEventListener('submit', () => {
    const button = form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;
  });
})();

window.addEventListener('pageshow', event => {
  if (event.persisted) document.querySelectorAll('form button[type="submit"]').forEach(button => button.disabled = false);
});
