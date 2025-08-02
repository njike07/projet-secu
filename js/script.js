// Sample student data
const students = [
    { id: 1, name: 'John Doe', email: 'john.doe@example.com', status: 'En attente' },
    { id: 2, name: 'Jane Smith', email: 'jane.smith@example.com', status: 'Validé' },
    { id: 3, name: 'Sam Johnson', email: 'sam.johnson@example.com', status: 'Refusé' },
  ];
  
  // Display students
  function displayStudents() {
    const tableBody = document.getElementById('student-table');
    tableBody.innerHTML = '';
  
    students.forEach(student => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${student.name}</td>
        <td>${student.email}</td>
        <td>${student.status}</td>
        <td>
          <button class="accept" onclick="changeStatus(${student.id}, 'Validé')">Valider</button>
          <button class="reject" onclick="changeStatus(${student.id}, 'Refusé')">Refuser</button>
        </td>
      `;
      tableBody.appendChild(row);
    });
  }
  
  // Change student status
  function changeStatus(id, status) {
    const student = students.find(s => s.id === id);
    if (student) {
      student.status = status;
      displayStudents();
    }
  }
  
  // Search filter
  document.getElementById('search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const filteredStudents = students.filter(student =>
      student.name.toLowerCase().includes(query) || student.email.toLowerCase().includes(query)
    );
    
    const tableBody = document.getElementById('student-table');
    tableBody.innerHTML = '';
  
    filteredStudents.forEach(student => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${student.name}</td>
        <td>${student.email}</td>
        <td>${student.status}</td>
        <td>
          <button class="accept" onclick="changeStatus(${student.id}, 'Validé')">Valider</button>
          <button class="reject" onclick="changeStatus(${student.id}, 'Refusé')">Refuser</button>
        </td>
      `;
      tableBody.appendChild(row);
    });
  });
  
  // Logout functionality
  document.getElementById('logout').addEventListener('click', function() {
    alert('Déconnexion réussie');
    // Redirect or close session logic here
  });
  
  // Initial display
  displayStudents();
  