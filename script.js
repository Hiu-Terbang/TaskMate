const $ = id => document.getElementById(id);
const currentView = document.body.dataset.view || 'dashboard';
const taskForm = $('task-form');
const taskDeadline = $('task-deadline');
let tasks = [];
let filter = 'all';
let editingId = null;

function isoToday() {
    const d = new Date();
    return new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
}
function fmt(date) {
    const [y, m, d] = date.split('-').map(Number);
    return new Date(y, m - 1, d).toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
}
function dateStatus(date) {
    if (date < isoToday()) return {label: 'Terlambat', cls: 'overdue'};
    if (date === isoToday()) return {label: 'Hari ini', cls: 'today'};
    return {label: 'Akan datang', cls: 'future'};
}
function showDate() {
    const today = $('today');
    if (today) today.textContent = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    }).format(new Date());
}
showDate();
if (taskDeadline) taskDeadline.min = isoToday();

async function api(url, method = 'GET', body = null) {
    // PUT/PATCH/DELETE dikirim sebagai POST + _method supaya jalan di hosting yang memblokirnya.
    if (method !== 'GET' && method !== 'POST') { body = { ...(body || {}), _method: method }; method = 'POST'; }
    const options = { method, headers: { 'Content-Type': 'application/json' } };
    if (body) options.body = JSON.stringify(body);
    const response = await fetch(url, options);
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Permintaan gagal.');
    return data;
}
function makeTaskCard(task, options = {}) {
    const article = document.createElement('article');
    article.className = 'task-item' + (task.completed ? ' completed' : '');
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'task-check';
    checkbox.checked = task.completed;
    checkbox.setAttribute('aria-label', 'Tandai tugas selesai');
    checkbox.onchange = () => changeTask(task, checkbox.checked);

    const content = document.createElement('div');
    content.className = 'task-content';
    const heading = document.createElement('h3');
    heading.textContent = task.title;
    const date = document.createElement('p');
    date.textContent = 'Deadline: ' + fmt(task.deadline);
    const meta = document.createElement('div');
    meta.className = 'task-meta';
    const category = document.createElement('span');
    category.className = 'category-tag';
    category.textContent = task.category;
    meta.append(category);
    if (options.deadline) {
        const statusInfo = dateStatus(task.deadline);
        const status = document.createElement('span');
        status.className = 'deadline-tag ' + statusInfo.cls;
        status.textContent = statusInfo.label;
        meta.append(status);
        if (task.completed) {
            const completed = document.createElement('span');
            completed.className = 'status-tag done';
            completed.textContent = 'Selesai';
            meta.append(completed);
        }
    } else {
        const status = document.createElement('span');
        status.className = 'status-tag' + (task.completed ? ' done' : '');
        status.textContent = task.completed ? 'Selesai' : 'Belum selesai';
        meta.append(status);
    }
    content.append(heading, date, meta);
    article.append(checkbox, content);

    if (options.actions) {
        const actions = document.createElement('div');
        actions.className = 'task-actions';
        const edit = document.createElement('button');
        edit.className = 'edit-btn';
        edit.type = 'button';
        edit.innerHTML = '<iconify-icon icon="solar:pen-2-linear" aria-hidden="true"></iconify-icon>Edit';
        edit.onclick = () => startEdit(task);
        const del = document.createElement('button');
        del.className = 'delete-btn';
        del.type = 'button';
        del.innerHTML = '<iconify-icon icon="solar:trash-bin-minimalistic-linear" aria-hidden="true"></iconify-icon>';
        del.setAttribute('aria-label', 'Hapus tugas');
        del.onclick = async () => {
            if (!confirm('Hapus tugas ini?')) return;
            try { await api('api/tasks.php', 'DELETE', { id: task.id }); await loadTasks(); }
            catch (error) { alert(error.message); }
        };
        actions.append(edit, del);
        article.append(actions);
    }
    return article;
}
async function changeTask(task, completed) {
    try { await api('api/tasks.php', 'PATCH', { id: task.id, completed }); await loadTasks(); }
    catch (error) { alert(error.message); }
}
function renderDashboard() {
    const total = tasks.length;
    const done = tasks.filter(task => task.completed).length;
    const pending = total - done;
    const percent = total ? Math.round(done / total * 100) : 0;
    const setCount = (id, n) => window.countTo ? window.countTo($(id), n) : ($(id).textContent = n);
    setCount('total-count', total);
    setCount('pending-count', pending);
    setCount('completed-count', done);
    $('progress-number').textContent = percent + '%';
    $('progress-ring').style.background = `conic-gradient(var(--primary) ${percent}%, var(--track) ${percent}%)`;
    $('progress-caption').textContent = !total
        ? 'Yuk, mulai kerjakan tugas pertamamu!'
        : percent === 100 ? 'Mantap! Semua tugas sudah selesai.'
        : `Terus semangat, masih ada ${pending} tugas lagi.`;

    const upcoming = $('dashboard-upcoming');
    upcoming.replaceChildren();
    const list = tasks.filter(task => !task.completed).sort((a, b) => a.deadline.localeCompare(b.deadline)).slice(0, 4);
    $('dashboard-empty').classList.toggle('hidden', list.length > 0);
    list.forEach(task => upcoming.append(makeTaskCard(task, { deadline: true })));
}
function renderTasks() {
    const items = $('task-items');
    items.replaceChildren();
    const shown = tasks.filter(task => filter === 'pending' ? !task.completed :
        filter === 'completed' ? task.completed : true);
    $('empty-state').classList.toggle('hidden', shown.length > 0);
    shown.forEach(task => items.append(makeTaskCard(task, { actions: true })));
}
function renderDeadlines() {
    const items = $('deadline-items');
    items.replaceChildren();
    const shown = [...tasks].sort((a, b) =>
        a.deadline.localeCompare(b.deadline) || a.title.localeCompare(b.title));
    $('deadline-empty').classList.toggle('hidden', shown.length > 0);
    shown.forEach(task => items.append(makeTaskCard(task, { deadline: true })));
}
function render() {
    if (currentView === 'dashboard') renderDashboard();
    if (currentView === 'tasks') renderTasks();
    if (currentView === 'deadlines') renderDeadlines();
}
async function loadTasks() {
    try {
        const data = await api('api/tasks.php');
        tasks = data.tasks.map(task => ({ ...task, id: Number(task.id), completed: Number(task.completed) === 1 }));
        render();
    } catch (error) { alert(error.message); }
}
if (taskForm) {
    $('open-form').onclick = () => {
        taskForm.classList.remove('hidden');
        $('task-title').focus();
    };
    $('cancel-form').onclick = () => {
        taskForm.reset();
        editingId = null;
        taskDeadline.min = isoToday();
        $('save-task').textContent = 'Simpan Tugas';
        taskForm.classList.add('hidden');
    };
    taskForm.onsubmit = async event => {
        event.preventDefault();
        const data = {
            title: $('task-title').value.trim(),
            category: $('task-category').value.trim(),
            deadline: taskDeadline.value
        };
        if (data.deadline < isoToday() && editingId === null) {
            alert('Deadline tugas baru tidak boleh lewat.');
            return;
        }
        try {
            if (editingId === null) await api('api/tasks.php', 'POST', data);
            else await api('api/tasks.php', 'PUT', { ...data, id: editingId });
            taskForm.reset();
            editingId = null;
            taskDeadline.min = isoToday();
            $('save-task').textContent = 'Simpan Tugas';
            taskForm.classList.add('hidden');
            await loadTasks();
        } catch (error) { alert(error.message); }
    };
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.onclick = () => {
            filter = button.dataset.filter;
            document.querySelectorAll('.filter-btn').forEach(item =>
                item.classList.toggle('active', item === button));
            renderTasks();
        };
    });
}
function startEdit(task) {
    editingId = task.id;
    $('task-title').value = task.title;
    $('task-category').value = task.category;
    taskDeadline.min = '';
    taskDeadline.value = task.deadline;
    $('save-task').textContent = 'Simpan Perubahan';
    taskForm.classList.remove('hidden');
    taskForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
const academicForm = $('academic-form');
if (academicForm) {
    academicForm.onsubmit = async event => {
        event.preventDefault();
        const semester = Number($('semester').value);
        const academic_year = $('academic-year').value.trim();
        try {
            await api('api/profile.php', 'POST', { semester, academic_year });
            $('semester-display').textContent = 'Semester ' + semester;
            $('year-display').textContent = 'Tahun Akademik ' + academic_year;
            $('profile-message').textContent = 'Tersimpan!';
        } catch (error) { $('profile-message').textContent = error.message; }
    };
}
loadTasks();
