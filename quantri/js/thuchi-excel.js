/**
 * Chức năng xuất Excel cho trang Quản lý Thu Chi
 * TheCleaner - 2025
 */

/**
 * Hàm xuất dữ liệu thu chi sang Excel
 */
function exportThuChiToExcel() {
  // Lấy thông tin ngày từ form
  const startDate = document.getElementById('start_date').value;
  const endDate = document.getElementById('end_date').value;
  const formattedStartDate = formatDate(startDate);
  const formattedEndDate = formatDate(endDate);
  
  // Lấy dữ liệu từ biểu đồ
  const chart = Chart.getChart('financeChart');
  const dailyData = [];
  
  if (chart) {
    const labels = chart.data.labels;
    const incomeData = chart.data.datasets[0].data;
    const expenseData = chart.data.datasets[1].data;
    const revenueData = chart.data.datasets[2].data;
    
    // Tạo mảng dữ liệu theo ngày
    for (let i = 0; i < labels.length; i++) {
      dailyData.push({
        date: labels[i],
        income: formatCurrency(incomeData[i]),
        expense: formatCurrency(expenseData[i]),
        revenue: formatCurrency(revenueData[i])
      });
    }
  }
  
  // Lấy dữ liệu chi tiết
  const expenseDetails = getTableData(document.getElementById('dataTableExpense'));
  const incomeDetails = getTableData(document.getElementById('dataTableIncome'));
  
  // Lấy tổng số liệu
  const totalIncomeElement = document.querySelector('.text-primary + .h5');
  const totalExpenseElement = document.querySelector('.text-danger + .h5');
  const totalRevenueElement = document.querySelector('.text-success + .h5');
  
  const totalIncome = totalIncomeElement ? totalIncomeElement.textContent.trim() : '';
  const totalExpense = totalExpenseElement ? totalExpenseElement.textContent.trim() : '';
  const totalRevenue = totalRevenueElement ? totalRevenueElement.textContent.trim() : '';
  
  // Tạo workbook mới
  const wb = XLSX.utils.book_new();
  
  // Tạo worksheet tổng quan
  const wsOverview = XLSX.utils.aoa_to_sheet([
    ['BÁO CÁO THU CHI THECLEANER'],
    [`Từ ngày ${formattedStartDate} đến ngày ${formattedEndDate}`],
    [''],
    ['TỔNG KẾT'],
    [`Tổng thu: ${totalIncome}`],
    [`Tổng chi: ${totalExpense}`],
    [`Tổng doanh thu: ${totalRevenue}`],
    ['']
  ]);
  
  // Định dạng tiêu đề
  wsOverview['!merges'] = [
    {s: {r: 0, c: 0}, e: {r: 0, c: 5}},
    {s: {r: 1, c: 0}, e: {r: 1, c: 5}}
  ];
  
  // Thêm worksheet tổng quan vào workbook
  XLSX.utils.book_append_sheet(wb, wsOverview, 'Tổng quan');
  
  // Tạo worksheet dữ liệu chi tiết theo ngày
  const wsDailyData = XLSX.utils.aoa_to_sheet([
    ['DOANH THU THEO NGÀY'],
    [''],
    ['Ngày', 'Tổng thu', 'Tổng chi', 'Doanh thu']
  ]);
  
  // Thêm dữ liệu theo ngày
  let rowIndex = 3;
  dailyData.forEach(item => {
    XLSX.utils.sheet_add_aoa(wsDailyData, [[
      item.date,
      item.income,
      item.expense,
      item.revenue
    ]], {origin: {r: rowIndex, c: 0}});
    rowIndex++;
  });
  
  // Thêm worksheet dữ liệu theo ngày vào workbook
  XLSX.utils.book_append_sheet(wb, wsDailyData, 'Dữ liệu theo ngày');
  
  // Thêm worksheet khoản chi
  if (expenseDetails && expenseDetails.length > 0) {
    const wsExpenses = XLSX.utils.json_to_sheet(expenseDetails);
    XLSX.utils.book_append_sheet(wb, wsExpenses, 'Khoản chi');
  }
  
  // Thêm worksheet khoản thu
  if (incomeDetails && incomeDetails.length > 0) {
    const wsIncomes = XLSX.utils.json_to_sheet(incomeDetails);
    XLSX.utils.book_append_sheet(wb, wsIncomes, 'Khoản thu');
  }
  
  // Tạo tên file
  const fileName = `BaoCaoThuChi_${formattedStartDate.replace(/\//g, '-')}_den_${formattedEndDate.replace(/\//g, '-')}.xlsx`;
  
  // Xuất file Excel
  XLSX.writeFile(wb, fileName);
}

/**
 * Hàm lấy dữ liệu từ bảng HTML
 */
function getTableData(table) {
  if (!table) return [];
  
  const data = [];
  const rows = table.querySelectorAll('tbody tr');
  const headers = [];
  
  // Lấy tiêu đề
  table.querySelectorAll('thead th').forEach(th => {
    headers.push(th.textContent.trim());
  });
  
  // Lấy dữ liệu từng dòng
  rows.forEach(row => {
    const rowData = {};
    row.querySelectorAll('td').forEach((cell, index) => {
      if (index < headers.length) {
        rowData[headers[index]] = cell.textContent.trim();
      }
    });
    data.push(rowData);
  });
  
  return data;
}

/**
 * Hàm định dạng ngày từ YYYY-MM-DD sang DD/MM/YYYY
 */
function formatDate(dateString) {
  if (!dateString) return '';
  const parts = dateString.split('-');
  return `${parts[2]}/${parts[1]}/${parts[0]}`;
}

/**
 * Hàm định dạng số tiền
 */
function formatCurrency(value) {
  if (typeof value !== 'number') return value;
  return value.toLocaleString('vi-VN') + ' VND';
}