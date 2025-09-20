// Sample data
const sampleData = [
    { id: 1, name: 'أحمد محمد', position: 'مطور', department: 'تقنية المعلومات', salary: 8000, joinDate: '2023-01-15' },
    { id: 2, name: 'فاطمة علي', position: 'مصممة', department: 'التصميم', salary: 7500, joinDate: '2023-02-20' },
    { id: 3, name: 'محمد حسن', position: 'محلل', department: 'تقنية المعلومات', salary: 7000, joinDate: '2023-03-10' },
    { id: 4, name: 'سارة أحمد', position: 'مديرة مشروع', department: 'الإدارة', salary: 9500, joinDate: '2023-01-05' },
    { id: 5, name: 'عمر خالد', position: 'محاسب', department: 'المحاسبة', salary: 6500, joinDate: '2023-04-12' },
    { id: 6, name: 'نور الدين', position: 'مسوق', department: 'التسويق', salary: 6000, joinDate: '2023-05-18' },
    { id: 7, name: 'ليلى حسام', position: 'كاتبة محتوى', department: 'التسويق', salary: 5500, joinDate: '2023-06-22' },
    { id: 8, name: 'كريم صالح', position: 'مطور واجهات', department: 'تقنية المعلومات', salary: 8500, joinDate: '2023-07-30' },
    { id: 9, name: 'هدى محمود', position: 'محاسبة', department: 'المحاسبة', salary: 6200, joinDate: '2023-08-14' },
    { id: 10, name: 'يوسف عبدالله', position: 'مدير المبيعات', department: 'المبيعات', salary: 10000, joinDate: '2023-09-01' },
    { id: 11, name: 'زينب أحمد', position: 'مصممة جرافيك', department: 'التصميم', salary: 7200, joinDate: '2023-10-05' },
    { id: 12, name: 'طارق عماد', position: 'محلل بيانات', department: 'تقنية المعلومات', salary: 7800, joinDate: '2023-11-12' },
    { id: 13, name: 'آية حسام', position: 'مديرة موارد بشرية', department: 'الموارد البشرية', salary: 9000, joinDate: '2023-12-01' },
    { id: 14, name: 'خالد عبدالرحمن', position: 'مهندس شبكات', department: 'تقنية المعلومات', salary: 8200, joinDate: '2024-01-10' },
    { id: 15, name: 'مريم سالم', position: 'كاتبة تقنية', department: 'التسويق', salary: 5800, joinDate: '2024-02-14' }
];

let currentData = [...sampleData];
let filteredData = [...sampleData];
let currentPage = 1;
let entriesPerPage = 5;
let sortColumn = '';
let sortDirection = '';

// Initialize the table
function init() {
    updateTable();
    setupEventListeners();
}

// Setup event listeners
function setupEventListeners() {
    // Entries selector
    document.getElementById('entriesSelect').addEventListener('change', function(e) {
        entriesPerPage = parseInt(e.target.value);
        currentPage = 1;
        updateTable();
    });

    // Search input
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        filteredData = sampleData.filter(item =>
            Object.values(item).some(value =>
                value.toString().toLowerCase().includes(searchTerm)
            )
        );
        currentPage = 1;
        updateTable();
    });

    // Sort headers
    document.querySelectorAll('th[data-column]').forEach(th => {
        th.addEventListener('click', function() {
            const column = this.getAttribute('data-column');
            handleSort(column);
        });
    });
}

// Handle sorting
function handleSort(column) {
    if (sortColumn === column) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn = column;
        sortDirection = 'asc';
    }

    filteredData.sort((a, b) => {
        let aVal = a[column];
        let bVal = b[column];

        if (column === 'salary') {
            aVal = parseInt(aVal);
            bVal = parseInt(bVal);
        }

        if (aVal < bVal) return sortDirection === 'asc' ? -1 : 1;
        if (aVal > bVal) return sortDirection === 'asc' ? 1 : -1;
        return 0;
    });

    updateSortIcons();
    currentPage = 1;
    updateTable();
}

// Update sort icons
function updateSortIcons() {
    document.querySelectorAll('th[data-column]').forEach(th => {
        th.classList.remove('sorted');
        const icon = th.querySelector('.sort-icon');
        if (th.getAttribute('data-column') === sortColumn) {
            th.classList.add('sorted');
            if (sortDirection === 'asc') {
                icon.innerHTML = '<polyline points="6,15 12,9 18,15"></polyline>';
            } else {
                icon.innerHTML = '<polyline points="6,9 12,15 18,9"></polyline>';
            }
        } else {
            icon.innerHTML = '<polyline points="6,9 12,15 18,9"></polyline>';
        }
    });
}

// Update table
function updateTable() {
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);

    const tbody = document.getElementById('tableBody');

    if (pageData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="no-results">لا توجد نتائج</td></tr>';
    } else {
        tbody.innerHTML = pageData.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.position}</td>
                        <td>${item.department}</td>
                        <td class="salary">${item.salary.toLocaleString()} ريال</td>
                        <td>${item.joinDate}</td>
                    </tr>
                `).join('');
    }

    updatePagination();
}

// Update pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredData.length / entriesPerPage);
    const startEntry = (currentPage - 1) * entriesPerPage + 1;
    const endEntry = Math.min(currentPage * entriesPerPage, filteredData.length);

    // Update pagination info
    document.getElementById('paginationInfo').innerHTML =
        `عرض ${startEntry} إلى ${endEntry} من ${filteredData.length} إدخال`;

    // Update pagination controls
    const controls = document.getElementById('paginationControls');
    let controlsHTML = '';

    // Previous button
    controlsHTML += `<button class="pagination-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>السابق</button>`;

    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        controlsHTML += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }

    // Next button
    controlsHTML += `<button class="pagination-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>التالي</button>`;

    controls.innerHTML = controlsHTML;
}

// Go to specific page
function goToPage(page) {
    const totalPages = Math.ceil(filteredData.length / entriesPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        updateTable();
    }
}

// Initialize the table when page loads
document.addEventListener('DOMContentLoaded', init);
